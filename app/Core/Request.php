<?php
namespace App\Core;

class Request
{
    private array $getParams;
    private array $postParams;
    private array $files;
    private array $server;
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $this->jsonBody = json_decode($input, true) ?? [];
        }
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->postParams['_method'])) {
            $override = strtoupper($this->postParams['_method']);
            if (in_array($override, ['PUT', 'DELETE', 'PATCH'])) {
                return $override;
            }
        }
        return $method;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        // Strip query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Strip subfolder if installed in subfolder, e.g. /face_attendance_system/public
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName); // e.g. /face_attendance_system/public
        $baseDir = str_replace('\\', '/', $baseDir);

        if ($baseDir !== '/' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $uri = '/' . trim($uri, '/');

        // Automatically strip index.php if accessed directly in URL
        if ($uri === '/index.php') {
            $uri = '/';
        } elseif (str_starts_with($uri, '/index.php/')) {
            $uri = substr($uri, 10); // length of '/index.php'
            $uri = '/' . trim($uri, '/');
        }

        return $uri === '/' ? '/' : $uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        return $this->postParams[$key] ?? $this->getParams[$key] ?? $default;
    }

    public function all(): array
    {
        if ($this->jsonBody !== null) {
            return array_merge($this->getParams, $this->jsonBody);
        }
        return array_merge($this->getParams, $this->postParams);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isAjax(): bool
    {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) &&
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public function ip(): string
    {
        if (!empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $this->server['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }
}
