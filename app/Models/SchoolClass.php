<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class SchoolClass extends Model
{
    protected static string $table = 'classes';

    public static function allWithStats(): array
    {
        $sql = "SELECT c.*,
                       COUNT(DISTINCT s.id) AS student_count,
                       COUNT(DISTINCT cs.subject_id) AS subject_count
                FROM classes c
                LEFT JOIN students s ON s.class_id = c.id
                LEFT JOIN class_subjects cs ON cs.class_id = c.id
                GROUP BY c.id
                ORDER BY c.id ASC";
        return Database::fetchAll($sql);
    }

    public static function getSubjects(int $classId): array
    {
        $sql = "SELECT sub.*
                FROM subjects sub
                INNER JOIN class_subjects cs ON cs.subject_id = sub.id
                WHERE cs.class_id = :cid
                ORDER BY sub.subject_name ASC";
        return Database::fetchAll($sql, [':cid' => $classId]);
    }
}
