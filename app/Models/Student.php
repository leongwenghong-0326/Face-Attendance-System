<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Student extends Model
{
    protected static string $table = 'students';

    public static function findWithDetails(int $id): ?array
    {
        $sql = "SELECT s.*, u.username, c.class_name, c.class_code, c.start_time, c.late_grace_minutes,
                       c.rest_start_time, c.rest_end_time
                FROM students s
                INNER JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE s.id = :id
                LIMIT 1";
        return Database::fetchOne($sql, [':id' => $id]);
    }

    public static function findByUserId(int $userId): ?array
    {
        $sql = "SELECT s.*, u.username, c.class_name, c.class_code, c.start_time, c.late_grace_minutes,
                       c.rest_start_time, c.rest_end_time
                FROM students s
                INNER JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE s.user_id = :uid
                LIMIT 1";
        return Database::fetchOne($sql, [':uid' => $userId]);
    }

    public static function findByStudentCode(string $code): ?array
    {
        $sql = "SELECT s.*, c.class_name, c.class_code, c.start_time, c.late_grace_minutes,
                       c.rest_start_time, c.rest_end_time
                FROM students s
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE s.student_code = :code
                LIMIT 1";
        return Database::fetchOne($sql, [':code' => $code]);
    }

    public static function search(string $query = '', ?int $classId = null, ?string $status = null): array
    {
        $sql = "SELECT s.*, u.username, c.class_name, c.class_code
                FROM students s
                INNER JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE 1=1";
        $params = [];

        if ($query !== '') {
            $sql .= " AND (s.full_name LIKE :q1 OR s.student_code LIKE :q2 OR s.email LIKE :q3 OR u.username LIKE :q4)";
            $params[':q1'] = "%{$query}%";
            $params[':q2'] = "%{$query}%";
            $params[':q3'] = "%{$query}%";
            $params[':q4'] = "%{$query}%";
        }

        if ($classId !== null && $classId > 0) {
            $sql .= " AND s.class_id = :classId";
            $params[':classId'] = $classId;
        }

        if ($status !== null && $status !== '') {
            $sql .= " AND s.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY s.id DESC";
        return Database::fetchAll($sql, $params);
    }
}
