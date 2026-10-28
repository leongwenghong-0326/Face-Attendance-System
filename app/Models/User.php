<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByUsernameOrEmail(string $identifier): ?array
    {
        $sql = "SELECT * FROM users WHERE (username = :id1 OR email = :id2) AND status = 'active' LIMIT 1";
        return Database::fetchOne($sql, [
            ':id1' => $identifier,
            ':id2' => $identifier
        ]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
