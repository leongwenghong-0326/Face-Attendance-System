<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Setting extends Model
{
    protected static string $table = 'settings';
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $row = Database::fetchOne("SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1", [':k' => $key]);
        if ($row) {
            self::$cache[$key] = $row['setting_value'];
            return $row['setting_value'];
        }

        return $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $desc = null): void
    {
        $existing = Database::fetchOne("SELECT id FROM settings WHERE setting_key = :k LIMIT 1", [':k' => $key]);
        if ($existing) {
            Database::query("UPDATE settings SET setting_value = :v WHERE setting_key = :k", [
                ':v' => (string)$value,
                ':k' => $key
            ]);
        } else {
            Database::query("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES (:k, :v, :g, :d)", [
                ':k' => $key,
                ':v' => (string)$value,
                ':g' => $group,
                ':d' => $desc
            ]);
        }
        self::$cache[$key] = (string)$value;
    }

    public static function getByGroup(string $group): array
    {
        return Database::fetchAll("SELECT * FROM settings WHERE setting_group = :g ORDER BY id ASC", [':g' => $group]);
    }

    public static function getAllAsKeyValue(): array
    {
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
        $res = [];
        foreach ($rows as $r) {
            $res[$r['setting_key']] = $r['setting_value'];
        }
        return $res;
    }

    public static function getAllSettings(): array
    {
        return self::getAllAsKeyValue();
    }
}
