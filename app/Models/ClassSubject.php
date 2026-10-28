<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class ClassSubject extends Model
{
    protected static string $table = 'class_subjects';

    public static function assign(int $classId, int $subjectId): bool
    {
        $existing = Database::fetchOne(
            "SELECT id FROM class_subjects WHERE class_id = :cid AND subject_id = :sid LIMIT 1",
            [':cid' => $classId, ':sid' => $subjectId]
        );

        if ($existing) {
            return false; // Prevent duplicate
        }

        self::insert([
            'class_id' => $classId,
            'subject_id' => $subjectId,
        ]);
        return true;
    }

    public static function remove(int $classId, int $subjectId): bool
    {
        Database::query(
            "DELETE FROM class_subjects WHERE class_id = :cid AND subject_id = :sid",
            [':cid' => $classId, ':sid' => $subjectId]
        );
        return true;
    }

    public static function allWithDetails(): array
    {
        $sql = "SELECT cs.id, c.id AS class_id, c.class_name, c.class_code,
                       s.id AS subject_id, s.subject_name, s.subject_code, cs.created_at
                FROM class_subjects cs
                INNER JOIN classes c ON cs.class_id = c.id
                INNER JOIN subjects s ON cs.subject_id = s.id
                ORDER BY c.class_name ASC, s.subject_name ASC";
        return Database::fetchAll($sql);
    }
}
