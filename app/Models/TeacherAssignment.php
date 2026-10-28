<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class TeacherAssignment extends Model
{
    protected static string $table = 'teacher_assignments';

    public static function assign(int $teacherId, int $classId, int $subjectId): bool
    {
        $existing = Database::fetchOne(
            "SELECT id FROM teacher_assignments WHERE teacher_id = :tid AND class_id = :cid AND subject_id = :sid LIMIT 1",
            [':tid' => $teacherId, ':cid' => $classId, ':sid' => $subjectId]
        );

        if ($existing) {
            return false; // Prevent duplicate
        }

        self::insert([
            'teacher_id' => $teacherId,
            'class_id' => $classId,
            'subject_id' => $subjectId,
        ]);
        return true;
    }

    public static function allWithDetails(): array
    {
        $sql = "SELECT ta.id, t.id AS teacher_id, t.full_name AS teacher_name, t.teacher_code,
                       c.id AS class_id, c.class_name, c.class_code,
                       s.id AS subject_id, s.subject_name, s.subject_code, ta.created_at
                FROM teacher_assignments ta
                INNER JOIN teachers t ON ta.teacher_id = t.id
                INNER JOIN classes c ON ta.class_id = c.id
                INNER JOIN subjects s ON ta.subject_id = s.id
                ORDER BY t.full_name ASC, c.class_name ASC";
        return Database::fetchAll($sql);
    }

    public static function getClassesByTeacher(int $teacherId): array
    {
        $sql = "SELECT DISTINCT c.*
                FROM classes c
                INNER JOIN teacher_assignments ta ON ta.class_id = c.id
                WHERE ta.teacher_id = :tid AND c.status = 'active'
                ORDER BY c.class_name ASC";
        return Database::fetchAll($sql, [':tid' => $teacherId]);
    }

    public static function getSubjectsByTeacher(int $teacherId, ?int $classId = null): array
    {
        $sql = "SELECT DISTINCT s.*
                FROM subjects s
                INNER JOIN teacher_assignments ta ON ta.subject_id = s.id
                WHERE ta.teacher_id = :tid AND s.status = 'active'";
        $params = [':tid' => $teacherId];

        if ($classId !== null) {
            $sql .= " AND ta.class_id = :cid";
            $params[':cid'] = $classId;
        }

        $sql .= " ORDER BY s.subject_name ASC";
        return Database::fetchAll($sql, $params);
    }
}
