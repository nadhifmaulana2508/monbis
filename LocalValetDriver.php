<?php

use Valet\Drivers\BasicValetDriver;

// Herd ignores Apache rewrite rules, so mirror the app's front-controller routes here.
class LocalValetDriver extends BasicValetDriver
{
    private const MOUNT_PREFIX = '/monbis';

    public function beforeLoading(string $sitePath, string $siteName, string $uri): void
    {
        parent::beforeLoading($sitePath, $siteName, $uri);

        if ($this->isMountedRequest($uri)) {
            $_ENV['APP_URL_LOCAL'] = 'http://localhost/monbis';
        }
    }

    public function isStaticFile(string $sitePath, string $siteName, string $uri)
    {
        $uri = $this->appPath($uri);
        return $this->isBlocked($uri) || $this->isDocsRoute($uri)
            ? false
            : parent::isStaticFile($sitePath, $siteName, $uri);
    }

    public function frontControllerPath(string $sitePath, string $siteName, string $uri): ?string
    {
        $mounted = $this->isMountedRequest($uri);
        $uri = $this->appPath($uri);
        if ($this->isBlocked($uri)) {
            return null;
        }

        $route = trim($this->requestPath($uri), '/');
        if ($this->isDocsRoute($uri)) {
            $_GET['url'] ??= $route;
            return $this->parentFrontControllerPath($sitePath, $siteName, '/', $mounted);
        }

        $path = $this->resolvePath($sitePath, $uri);
        if ($path !== null && is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'php') {
            return $this->parentFrontControllerPath($sitePath, $siteName, $uri, $mounted);
        }

        if ($path !== null && is_dir($path) && is_file($path . DIRECTORY_SEPARATOR . 'index.php')) {
            return $this->parentFrontControllerPath($sitePath, $siteName, $uri, $mounted);
        }

        if (preg_match('~^api(?:/(.*))?$~i', $route, $matches)) {
            $_GET['request'] ??= $matches[1] ?? '';
            return $this->setScript($sitePath, $sitePath . '/api/index.php', $mounted);
        }

        if (preg_match('~^v-2(?:/|$)~i', $route)) {
            if (strtolower(pathinfo($route, PATHINFO_EXTENSION)) === 'php') {
                return null;
            }
            return $this->setScript($sitePath, $sitePath . '/v-2/index.php', $mounted);
        }

        if (preg_match('~^(?:rbb|gorbb|simpeg)(?:/|$)~i', $route)) {
            return null;
        }

        $_GET['url'] ??= $route;
        return $this->parentFrontControllerPath($sitePath, $siteName, '/', $mounted);
    }

    private function requestPath(string $uri): string
    {
        return rawurldecode((string) (parse_url($uri, PHP_URL_PATH) ?: '/'));
    }

    private function appPath(string $uri): string
    {
        $path = $this->requestPath($uri);
        if (!$this->isMountedRequest($uri)) {
            return $path;
        }

        $path = substr($path, strlen(self::MOUNT_PREFIX));
        return $path === '' ? '/' : $path;
    }

    private function isMountedRequest(string $uri): bool
    {
        $host = strtolower((string) preg_replace('/:\\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')));
        $path = $this->requestPath($uri);

        return $host === 'localhost'
            && (strcasecmp($path, self::MOUNT_PREFIX) === 0
                || stripos($path, self::MOUNT_PREFIX . '/') === 0);
    }

    private function parentFrontControllerPath(string $sitePath, string $siteName, string $uri, bool $mounted): ?string
    {
        $script = parent::frontControllerPath($sitePath, $siteName, $uri);
        if ($script !== null && $mounted) {
            $_SERVER['SCRIPT_NAME'] = self::MOUNT_PREFIX . (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        }

        return $script;
    }

    private function isBlocked(string $uri): bool
    {
        $path = trim(str_replace('\\', '/', $this->requestPath($uri)), '/');

        return preg_match('~(^|/)\.(?!well-known(?:/|$))~i', '/' . $path) === 1
            || preg_match('~^(?:pages|views|components|api/(?:controllers|helpers|middlewares|routes)|v-2/(?:pages|components)|v-2/config\.php|(?:debug|tmp_kpi_activate|LocalValetDriver)\.php|img/_component_demo\.php)(?:/|$)~i', $path) === 1;
    }

    private function isDocsRoute(string $uri): bool
    {
        return preg_match('~^docs(?:/|$)~i', trim($this->requestPath($uri), '/')) === 1;
    }

    private function resolvePath(string $sitePath, string $uri): ?string
    {
        $root = realpath($sitePath);
        if ($root === false) {
            return null;
        }

        $relative = str_replace('/', DIRECTORY_SEPARATOR, trim($this->requestPath($uri), '/'));
        $path = realpath($root . DIRECTORY_SEPARATOR . $relative);
        if ($path === false) {
            return null;
        }

        return strcasecmp($path, $root) === 0
            || str_starts_with(strtolower($path), strtolower($root . DIRECTORY_SEPARATOR))
            ? $path
            : null;
    }

    private function setScript(string $sitePath, string $script, bool $mounted): string
    {
        $root = realpath($sitePath) ?: $sitePath;
        $relative = ltrim(str_replace('\\', '/', substr($script, strlen($root))), '/');
        $_SERVER['SCRIPT_FILENAME'] = $script;
        $_SERVER['SCRIPT_NAME'] = ($mounted ? self::MOUNT_PREFIX : '') . '/' . $relative;
        $_SERVER['DOCUMENT_ROOT'] = $root;

        return $script;
    }
}
