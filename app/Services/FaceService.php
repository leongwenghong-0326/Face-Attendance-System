<?php
namespace App\Services;

use App\Core\Database;
use App\Helpers\Uploader;
use App\Models\AuditLog;
use App\Models\FaceDescriptor;
use App\Models\FaceRegistration;
use App\Models\Student;

class FaceService
{
    public static function registerFace(int $studentId, array $descriptors, ?string $previewBase64, ?int $performedByUserId = null): array
    {
        $student = Student::find($studentId);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found.'];
        }

        if (empty($descriptors)) {
            return ['success' => false, 'message' => 'No face descriptors provided for registration.'];
        }

        // Validate each descriptor is an array of 128 numbers
        foreach ($descriptors as $idx => $desc) {
            if (!is_array($desc) || count($desc) !== 128) {
                return ['success' => false, 'message' => "Invalid face descriptor format at sample index {$idx}."];
            }
        }

        // Save preview image if provided
        $previewPath = null;
        if (!empty($previewBase64)) {
            $previewPath = Uploader::saveBase64Image($previewBase64, 'faces');
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            $isReRegistration = ((int)$student['is_face_registered'] === 1);
            $newVersion = ((int)$student['face_version']) + 1;

            // 1. Supersede any existing active registration
            FaceRegistration::supersedeOldRegistrations($studentId);

            // 2. Create new face registration record
            $registrationId = FaceRegistration::insert([
                'student_id' => $studentId,
                'version' => $newVersion,
                'sample_count' => count($descriptors),
                'preview_image' => $previewPath ?: $student['face_preview_image'],
                'status' => 'active',
            ]);

            // 3. Insert each descriptor
            foreach ($descriptors as $index => $desc) {
                FaceDescriptor::insert([
                    'registration_id' => $registrationId,
                    'student_id' => $studentId,
                    'descriptor' => json_encode($desc),
                    'sample_index' => $index + 1,
                ]);
            }

            // 4. Update student record
            Student::update($studentId, [
                'is_face_registered' => 1,
                'face_version' => $newVersion,
                'face_preview_image' => $previewPath ?: $student['face_preview_image'],
            ]);

            // 5. Audit log
            $action = $isReRegistration ? 'Face Re-Registered' : 'Face Registered';
            AuditLog::log(
                "{$action} for {$student['full_name']} (v{$newVersion})",
                $performedByUserId,
                $studentId,
                null,
                $newVersion,
                ['sample_count' => count($descriptors), 'registration_id' => $registrationId]
            );

            $db->commit();

            return [
                'success' => true,
                'message' => $isReRegistration ? 'Face re-registration completed successfully!' : 'Face registration completed successfully!',
                'version' => $newVersion,
                'preview' => $previewPath ?: $student['face_preview_image'],
            ];
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['success' => false, 'message' => 'Registration database error: ' . $e->getMessage()];
        }
    }
}
