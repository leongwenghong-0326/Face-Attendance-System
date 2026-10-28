<?php
use App\Core\Env;
use App\Core\Session;
use App\Helpers\Csrf;
use App\Models\Setting;

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            $scriptDir = str_replace('\\', '/', $scriptDir);
            $basePath = ($scriptDir === '/' || $scriptDir === '.') ? '' : $scriptDir;
            $base = "{$scheme}://{$host}{$basePath}";
        } else {
            $base = rtrim(Env::get('APP_URL', 'http://localhost/face_attendance_system/public'), '/');
        }

        if ($path === '' || $path === '/') {
            return $base;
        }
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(mixed $val): string
    {
        return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $old = Session::get('_old_input', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return Session::get('user');
    }
}

if (!function_exists('has_role')) {
    function has_role(string|array $roles): bool
    {
        $user = auth_user();
        if (!$user) {
            return false;
        }
        if (is_array($roles)) {
            return in_array($user['role'], $roles, true);
        }
        return $user['role'] === $roles;
    }
}

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'd M Y'): string
    {
        if (!$date) return '-';
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_time')) {
    function format_time(?string $time, string $format = 'h:i A'): string
    {
        if (!$time) return '-';
        return date($format, strtotime($time));
    }
}
