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
    }

    $resolved = $valid ? $candidate : 'https://apisso.bkkjateng.co.id';
    return $resolved;
}

function getSsoAppCode(): string
{
    $value = getenv('SSO_APP_CODE');
    if ($value === false || trim((string)$value) === '') $value = $_ENV['SSO_APP_CODE'] ?? '';
    if (trim((string)$value) === '') {
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envFile) && is_readable($envFile)) {
            $values = parse_ini_file($envFile, false, INI_SCANNER_RAW);
            if (is_array($values)) $value = $values['SSO_APP_CODE'] ?? '';
        }
    }
    $value = strtolower(trim((string)$value));
    return preg_match('/^[a-z0-9][a-z0-9._-]{0,49}$/', $value) ? $value : 'monbis';
}

function getSsoAppToken(): string
{
    $value = getenv('SSO_APP_TOKEN');
    if ($value === false || trim((string)$value) === '') $value = $_ENV['SSO_APP_TOKEN'] ?? '';
    if (trim((string)$value) === '') {
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envFile) && is_readable($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                if (!preg_match('/^\s*(?:export\s+)?SSO_APP_TOKEN\s*=\s*(.*?)\s*$/', (string)$line, $match)) continue;
                $value = trim($match[1]);
                if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                } else {
                    $value = preg_replace('/\s+#.*$/', '', $value);
                }
                break;
            }
        }
    }
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
function getSsoCurlResolveEntries(): array
{
    $connectIp = getenv('SSO_API_CONNECT_IP');
    if ($connectIp === false || trim((string)$connectIp) === '') {
        $connectIp = $_ENV['SSO_API_CONNECT_IP'] ?? '';
    }
    if (trim((string)$connectIp) === '') {
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envFile) && is_readable($envFile)) {
            $values = parse_ini_file($envFile, false, INI_SCANNER_RAW);
            if (is_array($values)) $connectIp = $values['SSO_API_CONNECT_IP'] ?? '';
        }
    }

    $connectIp = trim(trim((string)$connectIp), " \t\n\r\0\x0B\"'");
    if ($connectIp === '' || filter_var($connectIp, FILTER_VALIDATE_IP) === false) return [];

    $parts = parse_url(getSsoApiBase());
    if (!is_array($parts) || empty($parts['host'])) return [];
    $host = strtolower((string)$parts['host']);
    $port = (int)($parts['port'] ?? (strtolower((string)$parts['scheme']) === 'https' ? 443 : 80));
    $address = filter_var($connectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? '[' . $connectIp . ']' : $connectIp;
    return [$host . ':' . $port . ':' . $address];
}
