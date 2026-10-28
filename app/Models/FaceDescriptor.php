<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class FaceDescriptor extends Model
{
    protected static string $table = 'face_descriptors';

    public static function getActiveDescriptorsForStudent(int $studentId): array
    {
        $sql = "SELECT fd.id, fd.descriptor, fd.sample_index
                FROM face_descriptors fd
                INNER JOIN face_registrations fr ON fd.registration_id = fr.id
                WHERE fd.student_id = :sid AND fr.status = 'active'
                ORDER BY fd.sample_index ASC";
        return Database::fetchAll($sql, [':sid' => $studentId]);
    }

    /**
     * Retrieve all active face descriptors of all students for the Kiosk/Attendance matcher
     */
    public static function getAllActiveDescriptors(): array
    {
        $sql = "SELECT s.id AS student_id, s.student_code, s.full_name, s.face_preview_image,
                       c.id AS class_id, c.class_name, c.class_code,
                       fd.descriptor, fd.sample_index
                FROM students s
                INNER JOIN face_registrations fr ON fr.student_id = s.id AND fr.status = 'active'
                INNER JOIN face_descriptors fd ON fd.registration_id = fr.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE s.status = 'active' AND s.is_face_registered = 1
                ORDER BY s.id ASC, fd.sample_index ASC";

        $rows = Database::fetchAll($sql);

        // Group descriptors by student
        $students = [];
        foreach ($rows as $row) {
            $sid = (int)$row['student_id'];
            if (!isset($students[$sid])) {
                $students[$sid] = [
                    'student_id' => $sid,
                    'student_code' => $row['student_code'],
                    'full_name' => $row['full_name'],
                    'preview_image' => $row['face_preview_image'],
                    'class_id' => $row['class_id'],
                    'class_name' => $row['class_name'] ?? 'Unassigned',
                    'descriptors' => [],
                ];
            }

            $decoded = json_decode($row['descriptor'], true);
            if (is_array($decoded)) {
                $students[$sid]['descriptors'][] = $decoded;
            }
        }

        return array_values($students);
    }
}
