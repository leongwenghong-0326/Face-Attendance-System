<?php
namespace App\Helpers;

use App\Core\Session;

class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return $token;
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $stored = Session::get('_csrf_token');
        if (!$stored) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
