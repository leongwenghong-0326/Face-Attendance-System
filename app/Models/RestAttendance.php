<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class RestAttendance extends Model
{
    protected static string $table = 'rest_attendance';

    public static function getTodayRecord(int $studentId, string $date): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM rest_attendance WHERE student_id = :sid AND rest_date = :dt LIMIT 1",
            [':sid' => $studentId, ':dt' => $date]
        );
    }

    public static function getLastRestActivityTime(int $studentId, string $date): ?string
    {
        $row = Database::fetchOne(
            "SELECT GREATEST(
                COALESCE(rest_in_time, '00:00:00'),
                COALESCE(rest_out_time, '00:00:00')
            ) AS latest_time
            FROM rest_attendance
            WHERE student_id = :sid AND rest_date = :dt",
            [':sid' => $studentId, ':dt' => $date]
        );

        return $row['latest_time'] ?? null;
    }
}
