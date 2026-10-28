<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Attendance extends Model
{
    protected static string $table = 'attendance';

    public static function getTodayRecord(int $studentId, string $date): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM attendance WHERE student_id = :sid AND attendance_date = :dt LIMIT 1",
            [':sid' => $studentId, ':dt' => $date]
        );
    }

    public static function getLastActivityTime(int $studentId, string $date): ?string
    {
        // Check regular attendance and rest attendance for the latest action today
        $row = Database::fetchOne(
            "SELECT GREATEST(
                COALESCE(check_in_time, '00:00:00'),
                COALESCE(check_out_time, '00:00:00')
            ) AS latest_time
            FROM attendance
            WHERE student_id = :sid AND attendance_date = :dt",
            [':sid' => $studentId, ':dt' => $date]
        );

        return $row['latest_time'] ?? null;
    }

    public static function search(array $filters = []): array
    {
        $sql = "SELECT a.*, s.student_code, s.full_name AS student_name, s.face_preview_image,
                       c.class_name, c.class_code
                FROM attendance a
                INNER JOIN students s ON a.student_id = s.id
                LEFT JOIN classes c ON a.class_id = c.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['date'])) {
            $sql .= " AND a.attendance_date = :dt";
            $params[':dt'] = $filters['date'];
        }

        if (!empty($filters['student_id'])) {
            $sql .= " AND a.student_id = :sid";
            $params[':sid'] = $filters['student_id'];
        }

        if (!empty($filters['class_id'])) {
            $sql .= " AND a.class_id = :cid";
            $params[':cid'] = $filters['class_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :st";
            $params[':st'] = $filters['status'];
        }

        if (!empty($filters['query'])) {
            $sql .= " AND (s.full_name LIKE :q OR s.student_code LIKE :q)";
            $params[':q'] = "%{$filters['query']}%";
        }

        $sql .= " ORDER BY a.attendance_date DESC, a.check_in_time DESC";
        return Database::fetchAll($sql, $params);
    }

    public static function getStudentStats(int $studentId): array
    {
        $total = Database::fetchOne(
            "SELECT COUNT(*) AS total_days,
                    SUM(CASE WHEN status IN ('checked_in', 'checked_out') THEN 1 ELSE 0 END) AS present_days,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late_days,
                    SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) AS leave_days
             FROM attendance
             WHERE student_id = :sid",
            [':sid' => $studentId]
        );

        $totalDays = (int)($total['total_days'] ?? 0);
        $present = (int)($total['present_days'] ?? 0);
        $late = (int)($total['late_days'] ?? 0);
        $leave = (int)($total['leave_days'] ?? 0);
        $attended = $present + $late;

        $percentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 1) : 100.0;

        return [
            'total_days' => $totalDays,
            'present_days' => $present,
            'late_days' => $late,
            'leave_days' => $leave,
            'attended_days' => $attended,
            'attendance_rate' => $percentage,
        ];
    }

    public static function getDashboardStats(string $date): array
    {
        $stats = Database::fetchOne(
            "SELECT COUNT(*) AS total_today,
                    SUM(CASE WHEN check_in_time IS NOT NULL THEN 1 ELSE 0 END) AS checked_in,
                    SUM(CASE WHEN check_out_time IS NOT NULL THEN 1 ELSE 0 END) AS checked_out,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late_students
             FROM attendance
             WHERE attendance_date = :dt",
            [':dt' => $date]
        );

        return [
            'total_today' => (int)($stats['total_today'] ?? 0),
            'checked_in' => (int)($stats['checked_in'] ?? 0),
            'checked_out' => (int)($stats['checked_out'] ?? 0),
            'late_students' => (int)($stats['late_students'] ?? 0),
        ];
    }
}
