<?php

function getSsoApiBase(): string
{
    static $resolved = null;
    if (is_string($resolved)) return $resolved;

    $candidate = getenv('SSO_API_BASE');
    if ($candidate === false || trim((string)$candidate) === '') {
        $candidate = $_ENV['SSO_API_BASE'] ?? '';
    }

    if (trim((string)$candidate) === '') {
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envFile) && is_readable($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                if (!preg_match('/^\s*(?:export\s+)?SSO_API_BASE\s*=\s*(.*?)\s*$/', (string)$line, $match)) continue;
                $candidate = trim($match[1]);
                if (strlen($candidate) >= 2 && (($candidate[0] === '"' && substr($candidate, -1) === '"') || ($candidate[0] === "'" && substr($candidate, -1) === "'"))) {
                    $candidate = substr($candidate, 1, -1);
                } else {
                    $candidate = preg_replace('/\s+#.*$/', '', $candidate);
                }
                break;
            }
        }
    }

    $candidate = rtrim(trim((string)$candidate), '/');
    $parts = $candidate !== '' ? parse_url($candidate) : false;
    $valid = is_array($parts)
        && in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)
        && !empty($parts['host'])
        && !isset($parts['user'])
        && !isset($parts['pass']);
    if ($valid && strtolower((string)$parts['scheme']) === 'http') {
        $valid = in_array(strtolower((string)$parts['host']), ['localhost', '127.0.0.1', '::1'], true);
    }

    $resolved = $valid ? $candidate : 'https://apisso.bkkjateng.co.id';
    return $resolved;
}
