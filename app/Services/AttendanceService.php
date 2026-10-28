<?php
namespace App\Services;

use App\Core\Database;
use App\Helpers\Uploader;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\LeaveApplication;
use App\Models\RestAttendance;
use App\Models\Setting;
use App\Models\Student;

class AttendanceService
{
    public static function processScan(int $studentId, float $confidence, ?string $photoBase64 = null): array
    {
        $student = Student::findWithDetails($studentId);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found in system.'];
        }

        if ($student['status'] !== 'active') {
            return ['success' => false, 'message' => 'Student account is currently inactive.'];
        }

        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        $currentTimestamp = time();

        // 1. Check if student has approved leave today
        if (LeaveApplication::hasApprovedLeave($studentId, $today)) {
            return [
                'success' => false,
                'status' => 'on_leave',
                'message' => 'Student is on approved leave today. Regular attendance cannot be recorded.'
            ];
        }

        // 2. Duplicate scan interval check
        $intervalSec = (int) Setting::get('duplicate_scan_interval', 300);
        $lastRegTime = Attendance::getLastActivityTime($studentId, $today);
        $lastRestTime = RestAttendance::getLastRestActivityTime($studentId, $today);

        $lastAnyTime = null;
        if ($lastRegTime && $lastRestTime) {
            $lastAnyTime = max($lastRegTime, $lastRestTime);
        } else {
            $lastAnyTime = $lastRegTime ?: $lastRestTime;
        }

        if ($lastAnyTime) {
            $lastTimestamp = strtotime("{$today} {$lastAnyTime}");
            $diff = $currentTimestamp - $lastTimestamp;
            if ($diff < $intervalSec) {
                $waitRemaining = $intervalSec - $diff;
                return [
                    'success' => false,
                    'status' => 'duplicate',
                    'message' => "Attendance already recorded recently. Please wait {$waitRemaining} seconds before scanning again."
                ];
            }
        }

        // Save snapshot image if provided
        $photoPath = null;
        if (!empty($photoBase64)) {
            $photoPath = Uploader::saveBase64Image($photoBase64, 'attendance');
        }

        $classId = (int)($student['class_id'] ?? 0);
        $restStart = $student['rest_start_time'] ?? '12:00:00';
        $restEnd = $student['rest_end_time'] ?? '13:00:00';

        // 3. Check if current time falls into Class Rest/Break Period
        if ($restStart && $restEnd && $currentTime >= $restStart && $currentTime <= $restEnd) {
            return self::handleRestAttendance($student, $today, $currentTime, $confidence, $photoPath);
        }

        // 4. Regular Attendance Workflow (Check-In or Check-Out)
        $record = Attendance::getTodayRecord($studentId, $today);

        if (!$record) {
            // First scan of the day -> Check In
            $classStartTime = $student['start_time'] ?? '08:00:00';
            $graceMinutes = (int)($student['late_grace_minutes'] ?? 15);

            // Compute late cutoff time
            $startTs = strtotime("{$today} {$classStartTime}");
            $lateCutoffTs = $startTs + ($graceMinutes * 60);

            $status = 'checked_in';
            $statusText = 'Check-in successful';

            if ($currentTimestamp > $lateCutoffTs) {
                $status = 'late';
                $statusText = 'Check-in recorded as LATE';
            }

            $recordId = Attendance::insert([
                'student_id' => $studentId,
                'class_id' => $classId,
                'attendance_date' => $today,
                'check_in_time' => $currentTime,
                'check_in_confidence' => $confidence,
                'check_in_photo' => $photoPath,
                'status' => $status,
            ]);

            AuditLog::log(
                "Attendance Check-In: {$student['full_name']} ({$status})",
                null,
                $studentId,
                $confidence,
                $student['face_version'],
                ['attendance_id' => $recordId, 'status' => $status, 'time' => $currentTime]
            );

            return [
                'success' => true,
                'action' => 'check_in',
                'status' => $status,
                'message' => "{$statusText} at " . date('h:i:s A', $currentTimestamp),
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['full_name'],
                    'code' => $student['student_code'],
                    'class' => $student['class_name'] ?? 'General',
                ],
                'time' => $currentTime,
                'confidence' => round($confidence * 100, 1) . '%',
            ];
        }

        // Student already checked in today -> Process Check Out
        if (empty($record['check_out_time'])) {
            $newStatus = ($record['status'] === 'late') ? 'late' : 'checked_out';

            Attendance::update($record['id'], [
                'check_out_time' => $currentTime,
                'check_out_confidence' => $confidence,
                'check_out_photo' => $photoPath,
                'status' => $newStatus,
            ]);

            AuditLog::log(
                "Attendance Check-Out: {$student['full_name']}",
                null,
                $studentId,
                $confidence,
                $student['face_version'],
                ['attendance_id' => $record['id'], 'time' => $currentTime]
            );

            return [
                'success' => true,
                'action' => 'check_out',
                'status' => $newStatus,
                'message' => "Check-out successful at " . date('h:i:s A', $currentTimestamp),
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['full_name'],
                    'code' => $student['student_code'],
                    'class' => $student['class_name'] ?? 'General',
                ],
                'time' => $currentTime,
                'confidence' => round($confidence * 100, 1) . '%',
            ];
        }

        // Already checked in and checked out
        return [
            'success' => true,
            'action' => 'already_completed',
            'status' => $record['status'],
            'message' => "Attendance for today already completed (In: {$record['check_in_time']}, Out: {$record['check_out_time']}).",
            'student' => [
                'id' => $student['id'],
                'name' => $student['full_name'],
                'code' => $student['student_code'],
                'class' => $student['class_name'] ?? 'General',
            ],
            'time' => $currentTime,
            'confidence' => round($confidence * 100, 1) . '%',
        ];
    }

    private static function handleRestAttendance(array $student, string $date, string $time, float $confidence, ?string $photoPath): array
    {
        $studentId = (int)$student['id'];
        $classId = (int)($student['class_id'] ?? 0);
        $record = RestAttendance::getTodayRecord($studentId, $date);

        if (!$record) {
            // First rest scan -> Rest In
            RestAttendance::insert([
                'student_id' => $studentId,
                'class_id' => $classId,
                'rest_date' => $date,
                'rest_in_time' => $time,
                'rest_in_confidence' => $confidence,
                'rest_in_photo' => $photoPath,
                'status' => 'rest_in',
            ]);

            AuditLog::log(
                "Rest/Break Check-In: {$student['full_name']}",
                null,
                $studentId,
                $confidence,
                $student['face_version'],
                ['time' => $time]
            );

            return [
                'success' => true,
                'action' => 'rest_in',
                'status' => 'rest_in',
                'message' => "Break/Rest check-in recorded at " . date('h:i:s A'),
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['full_name'],
                    'code' => $student['student_code'],
                    'class' => $student['class_name'] ?? 'General',
                ],
                'time' => $time,
                'confidence' => round($confidence * 100, 1) . '%',
            ];
        }

        if (empty($record['rest_out_time'])) {
            // Second rest scan -> Rest Out
            RestAttendance::update($record['id'], [
                'rest_out_time' => $time,
                'rest_out_confidence' => $confidence,
                'rest_out_photo' => $photoPath,
                'status' => 'rest_out',
            ]);

            AuditLog::log(
                "Rest/Break Check-Out: {$student['full_name']}",
                null,
                $studentId,
                $confidence,
                $student['face_version'],
                ['time' => $time]
            );

            return [
                'success' => true,
                'action' => 'rest_out',
                'status' => 'rest_out',
                'message' => "Break/Rest check-out recorded at " . date('h:i:s A'),
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['full_name'],
                    'code' => $student['student_code'],
                    'class' => $student['class_name'] ?? 'General',
                ],
                'time' => $time,
                'confidence' => round($confidence * 100, 1) . '%',
            ];
        }

        return [
            'success' => true,
            'action' => 'rest_completed',
            'status' => 'rest_out',
            'message' => "Break period check-in/out already recorded for today.",
            'student' => [
                'id' => $student['id'],
                'name' => $student['full_name'],
                'code' => $student['student_code'],
            ],
            'time' => $time,
            'confidence' => round($confidence * 100, 1) . '%',
        ];
    }
}
