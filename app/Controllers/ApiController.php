<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\FaceDescriptor;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\FaceService;

class ApiController extends Controller
{
    public function getDescriptors(Request $request): void
    {
        // Must be logged in as admin or student/teacher, or in Kiosk mode
        $descriptors = FaceDescriptor::getAllActiveDescriptors();
        $this->json([
            'success' => true,
            'count' => count($descriptors),
            'students' => $descriptors,
        ]);
    }

    public function getSettings(Request $request): void
    {
        $settings = Setting::getAllAsKeyValue();
        $this->json([
            'success' => true,
            'settings' => [
                'min_face_size' => (int)($settings['min_face_size'] ?? 80),
                'max_face_size' => (int)($settings['max_face_size'] ?? 440),
                'min_brightness' => (int)($settings['min_brightness'] ?? 40),
                'max_brightness' => (int)($settings['max_brightness'] ?? 225),
                'min_sharpness' => (int)($settings['min_sharpness'] ?? 20),
                'detection_confidence' => (float)($settings['detection_confidence'] ?? 0.50),
                'match_threshold' => (float)($settings['match_threshold'] ?? 0.55),
                'min_confidence' => (float)($settings['min_confidence'] ?? 0.45),
                'stable_frames' => (int)($settings['stable_frames'] ?? 3),
                'duplicate_scan_interval' => (int)($settings['duplicate_scan_interval'] ?? 300),
                'registration_samples' => (int)($settings['registration_samples'] ?? 3),
            ]
        ]);
    }

    public function registerFace(Request $request): void
    {
        $studentId = (int)$request->input('student_id');
        $descriptors = $request->input('descriptors'); // array of descriptor arrays
        $preview = $request->input('preview_image');

        // Authorization check: User can register if they are admin, or if they are the student themselves
        $user = $this->user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if ($user['role'] === 'student' && (int)$user['student_id'] !== $studentId) {
            $this->json(['success' => false, 'message' => 'Forbidden: Cannot register face for another student'], 403);
            return;
        }

        if (!is_array($descriptors) || count($descriptors) === 0) {
            $this->json(['success' => false, 'message' => 'No descriptor samples provided'], 422);
            return;
        }

        $res = FaceService::registerFace($studentId, $descriptors, $preview, $user['id']);
        $this->json($res, $res['success'] ? 200 : 400);
    }

    public function scanAttendance(Request $request): void
    {
        $studentId = (int)$request->input('student_id');
        $confidence = (float)$request->input('confidence', 0);
        $photoBase64 = $request->input('photo');

        $minConfidence = (float)Setting::get('min_confidence', 0.45);
        if ($confidence < $minConfidence) {
            $this->json([
                'success' => false,
                'status' => 'low_confidence',
                'message' => 'Face match confidence is too low. Please adjust lighting and face the camera directly.'
            ], 422);
            return;
        }

        $result = AttendanceService::processScan($studentId, $confidence, $photoBase64);
        $this->json($result);
    }
}
