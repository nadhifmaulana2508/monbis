<?php
declare(strict_types=1);

function v2_normalize_base_url(string $value): string
{
    $value = trim(str_replace('\\', '/', $value));
    if ($value === '' || $value === '/') return '';
    return rtrim($value, '/');
}

function v2_use_query_routes(): bool
{
    $configured = getenv('V2_QUERY_ROUTES');
    if ($configured !== false && trim((string)$configured) !== '') {
        $value = filter_var($configured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value !== null) return $value;
    }

    return false;
}

function v2_route_url(string $baseUrl, string $route = ''): string
{
    $baseUrl = rtrim($baseUrl, '/');
    $route = trim(str_replace('\\', '/', $route), '/');
    if ($route === '') return $baseUrl;
    if (v2_use_query_routes()) return $baseUrl . '/index.php?v2_route=' . rawurlencode($route);
    return $baseUrl . '/' . $route;
}

function v2_detect_module(string $configured, string $baseUrl): string
{
    $aliases = [
        'workspace' => 'workspace',
        'v-2' => 'workspace',
        'rbb' => 'rbb',
        'gorbb' => 'rbb',
        'kpi' => 'kpi',
        'simpeg' => 'kpi',
    ];
    $value = strtolower(trim($configured));
    if ($value !== '' && isset($aliases[$value])) return $aliases[$value];

    $folder = strtolower(basename(rtrim($baseUrl, '/')));
    return $aliases[$folder] ?? 'workspace';
}

function v2_runtime_config(string $baseUrl): array
{
    $config = [];
    $configFile = dirname(__DIR__) . '/config.php';
    if (is_file($configFile)) {
        $loaded = require $configFile;
        if (is_array($loaded)) $config = $loaded;
    }

    $env = static function (string $key, $fallback = null) {
        $value = getenv($key);
        return $value === false || trim((string)$value) === '' ? $fallback : $value;
    };

    $defaultBackend = v2_normalize_base_url(dirname($baseUrl));
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host) ?: $host;
    $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    $configuredBackend = trim((string)($config['backend_base_url'] ?? ''));
    $configuredApi = trim((string)($config['api_base_url'] ?? ''));
    $configuredAuth = trim((string)($config['auth_base_url'] ?? ''));
    $environmentBackend = $isLocalHost
        ? trim((string)($config['local_backend_base_url'] ?? ''))
        : trim((string)($config['server_backend_base_url'] ?? ''));
    $backendFallback = $configuredBackend !== ''
        ? $configuredBackend
        : ($environmentBackend !== '' ? $environmentBackend : $defaultBackend);
    $backendBase = v2_normalize_base_url((string)$env('V2_BACKEND_BASE_URL', $backendFallback));
    $apiBase = v2_normalize_base_url((string)$env('V2_API_BASE_URL', $configuredApi));
    if ($apiBase === '') $apiBase = $backendBase === '' ? '/api' : $backendBase . '/api';
    $authBase = v2_normalize_base_url((string)$env('V2_AUTH_BASE_URL', $configuredAuth));
    if ($authBase === '') $authBase = $backendBase;

    $module = v2_detect_module((string)$env('V2_MODULE', $config['module'] ?? ''), $baseUrl);

    $pageAuth = $config['page_auth'] ?? true;
    $pageAuthEnv = getenv('V2_PAGE_AUTH');
    if ($pageAuthEnv !== false && trim((string)$pageAuthEnv) !== '') {
        $pageAuth = filter_var($pageAuthEnv, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($pageAuth === null) $pageAuth = true;
    }

    // Preview hanya untuk pengembangan lokal. Server produksi tetap wajib login.
    $allowPreview = $isLocalHost;
    $previewEnv = getenv('V2_ALLOW_PREVIEW');
    if ($isLocalHost && $previewEnv !== false && trim((string)$previewEnv) !== '') {
        $previewValue = filter_var($previewEnv, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($previewValue !== null) $allowPreview = $previewValue;
    }

    return [
        'backend_base_url' => $backendBase,
        'api_base_url' => $apiBase,
        'auth_base_url' => $authBase,
        'module' => $module,
        'page_auth' => (bool)$pageAuth,
        'allow_preview' => $allowPreview,
    ];
}
