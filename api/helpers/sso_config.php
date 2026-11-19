<?php

function getAppEnvironmentUrls(): array
{
    static $resolved = null;
    if (is_array($resolved)) return $resolved;

    $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    $envValues = [];
    if (is_file($envFile) && is_readable($envFile)) {
        $parsed = parse_ini_file($envFile, false, INI_SCANNER_RAW);
        if (is_array($parsed)) $envValues = $parsed;
    }

    $config = [
        'local' => 'APP_URL_LOCAL',
        'development' => 'APP_URL_DEV',
        'production' => 'APP_URL_PROD',
    ];
    $resolved = [];
    foreach ($config as $environment => $key) {
        $value = getenv($key);
        if ($value === false || trim((string)$value) === '') {
            $value = $_ENV[$key] ?? ($envValues[$key] ?? '');
        }

        $value = rtrim(trim((string)$value), '/');
        $parts = $value !== '' ? parse_url($value) : false;
        if (!is_array($parts)
            || !in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])) {
            continue;
        }

        if ($environment !== 'local' && strtolower((string)$parts['scheme']) !== 'https') continue;
        if (strtolower((string)$parts['scheme']) === 'http'
            && !in_array(strtolower((string)$parts['host']), ['localhost', '127.0.0.1', '::1'], true)) {
            continue;
        }

        $resolved[$environment] = $value;
    }

    return $resolved;
}

function getCurrentAppBaseUrl(): string
{
    $httpHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $hostOnly = preg_replace('/:\d+$/', '', $httpHost) ?: $httpHost;
    $localHosts = ['localhost', '127.0.0.1', '::1'];

    foreach (getAppEnvironmentUrls() as $environment => $configuredUrl) {
        $configuredHost = strtolower((string)(parse_url($configuredUrl, PHP_URL_HOST) ?? ''));
        if ($hostOnly === $configuredHost
            || ($environment === 'local'
                && in_array($hostOnly, $localHosts, true)
                && in_array($configuredHost, $localHosts, true))) {
            return $configuredUrl;
        }
    }

    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
    $isHttps = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    $scheme = in_array($forwardedProto, ['http', 'https'], true)
        ? $forwardedProto
        : ($isHttps || (int)($_SERVER['SERVER_PORT'] ?? 80) === 443 ? 'https' : 'http');
    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = preg_replace('#/api$#', '', $basePath) ?: '';

    return $scheme . '://' . $httpHost . $basePath;
}

function getSsoConfigValue(string $key, ?string &$source = null): string
{
    $source = 'default';
    $value = getenv($key);
    if ($value !== false && trim((string)$value) !== '') {
        $source = 'getenv';
        return (string)$value;
    }

    $value = $_ENV[$key] ?? '';
    if (trim((string)$value) !== '') {
        $source = '_ENV';
        return (string)$value;
    }

    $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($envFile) || !is_readable($envFile)) return '';

    $keyPattern = preg_quote($key, '/');
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        if (!preg_match('/^\s*(?:export\s+)?' . $keyPattern . '\s*=\s*(.*?)\s*$/', (string)$line, $match)) continue;
        $value = trim($match[1]);
        if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        } else {
            $value = preg_replace('/\s+#.*$/', '', $value);
        }
        if (trim((string)$value) !== '') $source = 'api/.env';
        return (string)$value;
    }

    return '';
}

function getSsoApiBase(?string &$source = null): string
{
    static $resolved = null;
    static $resolvedSource = 'default';
    if (is_string($resolved)) {
        $source = $resolvedSource;
        return $resolved;
    }

    $candidate = getSsoConfigValue('SSO_API_BASE', $candidateSource);

    $candidate = rtrim(trim((string)$candidate), '/');
    $parts = $candidate !== '' ? parse_url($candidate) : false;
    $valid = is_array($parts)
        && in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)
        && !empty($parts['host'])
        && !isset($parts['user'])
        && !isset($parts['pass']);
    $candidateHost = strtolower((string)($parts['host'] ?? ''));
    $requestHost = strtolower((string)preg_replace('/:\\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    if ($valid && $requestHost !== '' && hash_equals($requestHost, $candidateHost)) {
        error_log('SSO_API_BASE points to the current Monbis host; using the default SSO host.');
        $valid = false;
    }
    if ($valid && strtolower((string)$parts['scheme']) === 'http') {
        $valid = in_array(strtolower((string)$parts['host']), ['localhost', '127.0.0.1', '::1'], true);
    }

    if ($valid) {
        // Terima base URL SSO dalam bentuk host atau host/api. Pemanggil
        // menambahkan /api/auth/... sehingga suffix /api harus dinormalkan.
        $candidate = preg_replace('#/api/?$#i', '', $candidate) ?: $candidate;
        $resolved = $candidate;
        $resolvedSource = $candidateSource;
    } else {
        $resolved = 'https://devsso.bkkjateng.co.id';
        $resolvedSource = $candidateSource === 'default' ? 'default' : 'fallback';
    }

    $source = $resolvedSource;
    return $resolved;
}

function getSsoAppCode(): string
{
    $value = getSsoConfigValue('SSO_APP_CODE');
    $value = strtolower(trim((string)$value));
    return preg_match('/^[a-z0-9][a-z0-9._-]{0,49}$/', $value) ? $value : 'monbis';
}

function getSsoAppToken(): string
{
    $value = getSsoConfigValue('SSO_APP_TOKEN');
    return trim((string)$value);
}

function getSsoAppHeaders(): array
{
    $token = getSsoAppToken();
    if ($token === '' || preg_match('/[\r\n]/', $token)) return [];
    return ['App: ' . getSsoAppCode(), 'X-App-Token: ' . $token];
}

/**
 * Optional internal address for reaching the SSO virtual host without routing
 * through this server's public IP. TLS still uses the hostname in the URL.
 */
function getSsoCurlResolveEntries(?string &$source = null): array
{
    $connectIp = getSsoConfigValue('SSO_API_CONNECT_IP', $source);

    $connectIp = trim(trim((string)$connectIp), " \t\n\r\0\x0B\"'");
    if ($connectIp === '' || filter_var($connectIp, FILTER_VALIDATE_IP) === false) return [];

    $parts = parse_url(getSsoApiBase());
    if (!is_array($parts) || empty($parts['host'])) return [];
    $host = strtolower((string)$parts['host']);
    $port = (int)($parts['port'] ?? (strtolower((string)$parts['scheme']) === 'https' ? 443 : 80));
    $address = filter_var($connectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? '[' . $connectIp . ']' : $connectIp;
    return [$host . ':' . $port . ':' . $address];
}

/**
 * Stream fallback for Apache PHP installations without the cURL extension.
 * It connects to SSO_API_CONNECT_IP when configured while preserving the SSO
 * hostname for HTTP Host, TLS SNI, and certificate verification.
 *
 * @return array{body:string|false,status:int,error:string}
 */
function ssoStreamRequest(string $url, string $method, array $headers, string $payload = '', int $timeout = 15): array
{
    $parts = parse_url($url);
    if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
        return ['body' => false, 'status' => 0, 'error' => 'invalid_url'];
    }

    $scheme = strtolower((string)$parts['scheme']);
    $host = strtolower((string)$parts['host']);
    $port = (int)($parts['port'] ?? ($scheme === 'https' ? 443 : 80));
    $connectHost = $host;
    foreach (getSsoCurlResolveEntries() as $entry) {
        $entryParts = explode(':', (string)$entry, 3);
        if (count($entryParts) !== 3
            || strtolower($entryParts[0]) !== $host
            || (int)$entryParts[1] !== $port) {
            continue;
        }
        $connectHost = trim($entryParts[2], '[]');
        break;
    }

    $urlHost = filter_var($connectHost, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
        ? '[' . $connectHost . ']'
        : $connectHost;
    $defaultPort = $scheme === 'https' ? 443 : 80;
    $requestUrl = $scheme . '://' . $urlHost . ($port !== $defaultPort ? ':' . $port : '')
        . (string)($parts['path'] ?? '/')
        . (isset($parts['query']) ? '?' . $parts['query'] : '');

    $hostHeader = $host . ($port !== $defaultPort ? ':' . $port : '');
    $safeHeaders = [];
    foreach ($headers as $header) {
        $header = trim((string)$header);
        if ($header === '' || preg_match('/[\r\n]/', $header)) {
            continue;
        }
        if (stripos($header, 'Host:') === 0
            || stripos($header, 'Connection:') === 0
            || stripos($header, 'Content-Length:') === 0
            || stripos($header, 'Accept-Encoding:') === 0) {
            continue;
        }
        $safeHeaders[] = $header;
    }
    $safeHeaders[] = 'Host: ' . $hostHeader;
    $safeHeaders[] = 'Connection: close';
    $safeHeaders[] = 'Accept-Encoding: identity';
    if (strtoupper($method) !== 'GET' && strtoupper($method) !== 'HEAD') {
        $safeHeaders[] = 'Content-Length: ' . strlen($payload);
    }

    $options = [
        'http' => [
            'method' => strtoupper($method),
            'timeout' => max(1, $timeout),
            'ignore_errors' => true,
            'follow_location' => 0,
            'header' => implode("\r\n", $safeHeaders) . "\r\n",
        ],
    ];
    if (strtoupper($method) !== 'GET' && strtoupper($method) !== 'HEAD') {
        $options['http']['content'] = $payload;
    }
    if ($scheme === 'https') {
        $options['ssl'] = [
            'peer_name' => $host,
            'verify_peer' => true,
            'verify_peer_name' => true,
            'SNI_enabled' => true,
        ];
    }

    $context = stream_context_create($options);
    $body = @file_get_contents($requestUrl, false, $context);
    $status = 0;
    foreach ($http_response_header ?? [] as $responseHeader) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/i', (string)$responseHeader, $match)) {
            $status = (int)$match[1];
            break;
        }
    }
    $lastError = error_get_last();

    return [
        'body' => is_string($body) ? $body : false,
        'status' => $status,
        'error' => is_string($body) ? '' : (string)($lastError['message'] ?? 'request_failed'),
    ];
}
