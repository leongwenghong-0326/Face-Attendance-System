<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class FaceRegistration extends Model
{
    protected static string $table = 'face_registrations';

    public static function getActiveRegistration(int $studentId): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM face_registrations WHERE student_id = :sid AND status = 'active' ORDER BY id DESC LIMIT 1",
            [':sid' => $studentId]
        );
    }

    public static function supersedeOldRegistrations(int $studentId): void
    {
        Database::query(
            "UPDATE face_registrations SET status = 'superseded' WHERE student_id = :sid AND status = 'active'",
            [':sid' => $studentId]
        );
    }
}
