<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Teacher extends Model
{
    protected static string $table = 'teachers';

    public static function findWithDetails(int $id): ?array
    {
        $sql = "SELECT t.*, u.username
                FROM teachers t
                INNER JOIN users u ON t.user_id = u.id
                WHERE t.id = :id
                LIMIT 1";
        return Database::fetchOne($sql, [':id' => $id]);
    }

    public static function findByUserId(int $userId): ?array
    {
        $sql = "SELECT t.*, u.username
                FROM teachers t
                INNER JOIN users u ON t.user_id = u.id
                WHERE t.user_id = :uid
                LIMIT 1";
        return Database::fetchOne($sql, [':uid' => $userId]);
    }

    public static function allWithUser(): array
    {
        $sql = "SELECT t.*, u.username
                FROM teachers t
                INNER JOIN users u ON t.user_id = u.id
                ORDER BY t.id DESC";
        return Database::fetchAll($sql);
    }
}
