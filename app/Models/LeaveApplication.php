<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class LeaveApplication extends Model
{
    protected static string $table = 'student_leave_applications';

    public static function hasApprovedLeave(int $studentId, string $date): bool
    {
        $row = Database::fetchOne(
            "SELECT id FROM student_leave_applications
             WHERE student_id = :sid AND leave_date = :dt AND status = 'approved'
             LIMIT 1",
            [':sid' => $studentId, ':dt' => $date]
        );
        return !empty($row);
    }

    public static function allWithDetails(?string $status = null): array
    {
        $sql = "SELECT la.*, s.student_code, s.full_name AS student_name, c.class_name,
                       u.username AS reviewer_username
                FROM student_leave_applications la
                INNER JOIN students s ON la.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN users u ON la.reviewed_by = u.id
                WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND la.status = :st";
            $params[':st'] = $status;
        }

        $sql .= " ORDER BY la.leave_date DESC, la.id DESC";
        return Database::fetchAll($sql, $params);
    }

    public static function getByStudent(int $studentId): array
    {
        $sql = "SELECT la.*, u.username AS reviewer_username
                FROM student_leave_applications la
                LEFT JOIN users u ON la.reviewed_by = u.id
                WHERE la.student_id = :sid
                ORDER BY la.leave_date DESC";
        return Database::fetchAll($sql, [':sid' => $studentId]);
    }

    public static function countPending(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS total FROM student_leave_applications WHERE status = 'pending'");
        return (int)($row['total'] ?? 0);
    }
}
