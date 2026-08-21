<?php

require_once __DIR__ . '/../helpers/response.php';

class ChatbotController
{
    private $env;

    public function __construct(array $env = array())
    {
        $this->env = $env;
    }

    private function envValue($key, $default = '')
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
        return isset($this->env[$key]) ? $this->env[$key] : $default;
    }

    private function cleanText($value, $limit = 8000)
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $value));
        if (function_exists('mb_substr')) {
            return mb_substr($text, 0, $limit, 'UTF-8');
        }
        return substr($text, 0, $limit);
    }

    private function jsonText($value, $limit = 9000)
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $this->cleanText($json ?: '', $limit);
    }

    private function httpPostJson($url, array $headers, array $payload)
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new Exception('Payload chatbot tidak valid.');
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 12,
            ));
            $result = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            if ($result === false) {
                throw new Exception('Gagal menghubungi Gemini: ' . $error);
            }
            return array($status, $result);
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'timeout' => 60,
                'ignore_errors' => true,
            )
        ));
        $result = file_get_contents($url, false, $context);
        $status = 0;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                if (preg_match('/^HTTP\/\S+\s+(\d+)/', $line, $m)) {
                    $status = (int) $m[1];
                    break;
                }
            }
        }
        if ($result === false) {
            throw new Exception('Gagal menghubungi Gemini.');
        }
        return array($status, $result);
    }

    private function extractText(array $json)
    {
        if (isset($json['output_text']) && trim((string) $json['output_text']) !== '') {
            return trim((string) $json['output_text']);
        }

        $texts = array();
        if (isset($json['steps']) && is_array($json['steps'])) {
            foreach ($json['steps'] as $step) {
                if (isset($step['content']) && is_array($step['content'])) {
                    foreach ($step['content'] as $content) {
                        if (isset($content['text'])) {
                            $texts[] = $content['text'];
                        }
                    }
                }
            }
        }

        if (isset($json['candidates']) && is_array($json['candidates'])) {
            foreach ($json['candidates'] as $candidate) {
                $parts = isset($candidate['content']['parts']) ? $candidate['content']['parts'] : array();
                foreach ($parts as $part) {
                    if (isset($part['text'])) {
                        $texts[] = $part['text'];
                    }
                }
            }
        }

        return trim(implode("\n", array_filter($texts)));
    }

    private function extractXaiText(array $json)
    {
        $content = isset($json['choices'][0]['message']['content'])
            ? $json['choices'][0]['message']['content']
            : '';
        return is_string($content) ? trim($content) : '';
    }

    private function extractXaiResponseText(array $json)
    {
        $texts = array();
        $output = isset($json['output']) && is_array($json['output']) ? $json['output'] : array();
        foreach ($output as $item) {
            $content = isset($item['content']) && is_array($item['content']) ? $item['content'] : array();
            foreach ($content as $part) {
                if (isset($part['text']) && is_string($part['text'])) {
                    $texts[] = $part['text'];
                }
            }
        }
        return trim(implode("\n", $texts));
    }

    public function ask(array $input)
    {
        $override = isset($input['_provider_override']) ? strtolower(trim((string) $input['_provider_override'])) : '';
        $provider = in_array($override, array('xai', 'gemini', 'openrouter'), true)
            ? $override
            : strtolower(trim((string) $this->envValue('AI_PROVIDER', 'auto')));
        $configuredProvider = strtolower(trim((string) $this->envValue('AI_PROVIDER', 'auto')));
        if ($provider === 'auto') {
            $provider = trim((string) $this->envValue('XAI_API_KEY', '')) !== '' ? 'xai' : 'gemini';
        }
        if ($provider === 'gemini') {
            $apiKey = trim((string) $this->envValue('GEMINI_API_KEY', ''));
        } elseif ($provider === 'openrouter') {
            $apiKey = trim((string) $this->envValue('OPENROUTER_API_KEY', ''));
        } else {
            $apiKey = trim((string) $this->envValue('XAI_API_KEY', ''));
        }
        if ($apiKey === '') {
            $keyName = $provider === 'gemini' ? 'GEMINI_API_KEY' : ($provider === 'openrouter' ? 'OPENROUTER_API_KEY' : 'XAI_API_KEY');
            sendResponse(500, $keyName . ' belum diisi di api/.env.');
        }

        if ($provider === 'gemini') {
            $model = trim((string) $this->envValue('GEMINI_MODEL', 'gemini-3.6-flash'));
        } elseif ($provider === 'openrouter') {
            $model = trim((string) $this->envValue('OPENROUTER_MODEL', 'openrouter/free'));
        } else {
            $model = trim((string) $this->envValue('XAI_MODEL', 'grok-4.6'));
        }
        $question = $this->cleanText(isset($input['question']) ? $input['question'] : '', 1000);
        $context = isset($input['context']) && is_array($input['context']) ? $input['context'] : array();
        $pageContext = $this->jsonText($context, 9000);

        if ($question === '') {
            $question = 'Jelaskan ringkasan data pada halaman ini.';
        }

        $system = 'Anda adalah asisten analisis laporan Monbis untuk direksi dan cabang. '
            . 'Jawab dalam Bahasa Indonesia yang singkat, jelas, dan operasional. '
            . 'Gunakan hanya data konteks halaman yang diberikan. Jangan mengarang angka. '
            . 'Jika data tidak tersedia, katakan data belum cukup. '
            . 'Fokus pada ringkasan kondisi, anomali, risiko, dan tindak lanjut. '
            . 'Jawab maksimal 5 poin singkat dan selesaikan setiap kalimat sampai tuntas. '
            . 'Jangan tampilkan data sensitif seperti nomor rekening, nomor HP, alamat, atau identitas nasabah.';

        $inputText = "KONTEKS HALAMAN:\n" . $pageContext . "\n\nPERTANYAAN USER:\n" . $question;
        if ($provider === 'gemini') {
            $payload = array(
                'systemInstruction' => array('parts' => array(array('text' => $system))),
                'contents' => array(array(
                    'role' => 'user',
                    'parts' => array(array('text' => $inputText))
                )),
                'generationConfig' => array('temperature' => 0.2, 'maxOutputTokens' => 2400)
            );
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
                . rawurlencode($model) . ':generateContent';
            $headers = array('Content-Type: application/json', 'x-goog-api-key: ' . $apiKey);
        } elseif ($provider === 'openrouter') {
            $payload = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'system', 'content' => $system),
                    array('role' => 'user', 'content' => $inputText)
                ),
                'temperature' => 0.2,
                'max_tokens' => 2400
            );
            $url = 'https://openrouter.ai/api/v1/chat/completions';
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'HTTP-Referer: http://localhost/report-dpk',
                'X-Title: Monbis Asisten Data'
            );
        } else {
            $payload = array(
                'model' => $model,
                'input' => array(
                    array('role' => 'system', 'content' => $system),
                    array('role' => 'user', 'content' => $inputText)
                ),
                'temperature' => 0.2,
                'max_output_tokens' => 2400
            );
            $url = 'https://api.x.ai/v1/responses';
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            );
        }

        try {
            list($status, $body) = $this->httpPostJson($url, $headers, $payload);
            $json = json_decode($body, true);
            if (!is_array($json)) {
                sendResponse(502, 'Response AI bukan JSON.', array('raw' => substr($body, 0, 300)));
            }
            if ($status < 200 || $status >= 300) {
                if ($provider === 'xai' && $configuredProvider === 'auto' && trim((string) $this->envValue('GEMINI_API_KEY', '')) !== '') {
                    $fallback = $input;
                    $fallback['_provider_override'] = 'gemini';
                    return $this->ask($fallback);
                }
                $message = isset($json['error']['message']) ? $json['error']['message'] : 'Provider AI gagal memproses request.';
                sendResponse(502, $message, array(
                    'status_code' => $status,
                    'provider' => $provider,
                    'raw' => substr($body, 0, 500)
                ));
            }

            $answer = $provider === 'gemini'
                ? $this->extractText($json)
                : ($provider === 'openrouter' ? $this->extractXaiText($json) : $this->extractXaiResponseText($json));
            if ($answer === '') {
                sendResponse(502, 'Provider AI tidak mengembalikan jawaban teks.');
            }

            sendResponse(200, 'OK', array(
                'answer' => $answer,
                'model' => $model,
                'provider' => $provider
            ));
        } catch (Exception $e) {
            if ($provider === 'xai' && $configuredProvider === 'auto' && trim((string) $this->envValue('GEMINI_API_KEY', '')) !== '') {
                $fallback = $input;
                $fallback['_provider_override'] = 'gemini';
                return $this->ask($fallback);
            }
            sendResponse(502, $e->getMessage());
        }
    }
}
