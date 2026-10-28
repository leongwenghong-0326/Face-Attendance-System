<?php
/**
 * Automated End-to-End System Integration Test
 */

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

require_once dirname(__DIR__) . '/app/Helpers/helpers.php';

// Bootstrap
\App\Core\Env::load(dirname(__DIR__) . '/.env');
date_default_timezone_set(\App\Core\Env::get('APP_TIMEZONE', 'Asia/Kuala_Lumpur'));
\App\Core\Session::start();

echo "========================================================\n";
echo "   APEX FACIAL ATTENDANCE SYSTEM - AUTOMATED TEST SUITE \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $description, bool $condition): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] {$description}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$description}\n";
        $failCount++;
    }
}

// 1. Test Database Connectivity
try {
    $pdo = \App\Core\Database::getConnection();
    assertTest("Database PDO connection established", $pdo instanceof PDO);
} catch (\Exception $e) {
    assertTest("Database PDO connection established: " . $e->getMessage(), false);
}

// 2. Test User Authentication & Password Hashing
$adminUser = \App\Models\User::findByUsernameOrEmail('admin');
assertTest("Admin demo user exists", !empty($adminUser));
assertTest("Password verification for admin (password123)", \App\Models\User::verifyPassword('password123', $adminUser['password_hash']));
assertTest("Invalid password rejected", !\App\Models\User::verifyPassword('wrongpass', $adminUser['password_hash']));

// 3. Test Student & Teacher Models
$student1 = \App\Models\Student::findWithDetails(1);
assertTest("Student #1 (John Doe) retrieved with class details", !empty($student1) && $student1['student_code'] === 'STD2026001');

$teacher1 = \App\Models\Teacher::findWithDetails(1);
assertTest("Teacher #1 (Dr. Alice Johnson) retrieved", !empty($teacher1) && $teacher1['teacher_code'] === 'TCH-001');

// 4. Test Face Registration Service with 128-d sample descriptors
// Generate mock 128 float descriptor
$mockDesc1 = array_map(function() { return (mt_rand(-1000, 1000) / 1000.0); }, range(1, 128));
$mockDesc2 = array_map(function() { return (mt_rand(-1000, 1000) / 1000.0); }, range(1, 128));
$mockDesc3 = array_map(function() { return (mt_rand(-1000, 1000) / 1000.0); }, range(1, 128));

$regResult = \App\Services\FaceService::registerFace(1, [$mockDesc1, $mockDesc2, $mockDesc3], null, 1);
assertTest("Face Registration Service saves 3 multi-sample descriptors", $regResult['success'] === true);

$studentAfterReg = \App\Models\Student::find(1);
assertTest("Student face status updated to registered (is_face_registered=1, version >= 1)", 
    (int)$studentAfterReg['is_face_registered'] === 1 && (int)$studentAfterReg['face_version'] >= 1
);

$activeDescriptors = \App\Models\FaceDescriptor::getActiveDescriptorsForStudent(1);
assertTest("Active face descriptors stored correctly (3 samples)", count($activeDescriptors) === 3);

// 5. Test Batch Descriptor Retrieval for Kiosk/Matcher
$allActive = \App\Models\FaceDescriptor::getAllActiveDescriptors();
assertTest("Batch active descriptors retrieved for Kiosk matcher", count($allActive) >= 1 && isset($allActive[0]['descriptors']));

// 6. Test Attendance Check-In Workflow
// Clear today's test record if any to ensure clean test
$today = date('Y-m-d');
\App\Core\Database::query("DELETE FROM attendance WHERE student_id = 1 AND attendance_date = :dt", [':dt' => $today]);

$attScan1 = \App\Services\AttendanceService::processScan(1, 0.94);
assertTest("First scan commits Check-In successfully", $attScan1['success'] === true && $attScan1['action'] === 'check_in');

// 7. Test Duplicate Scan Protection (Immediate re-scan within 300s window)
$attScanDuplicate = \App\Services\AttendanceService::processScan(1, 0.95);
assertTest("Immediate re-scan is blocked by duplicate scan protection", 
    $attScanDuplicate['success'] === false && $attScanDuplicate['status'] === 'duplicate'
);

// 8. Test Approved Leave Application & Attendance Restriction
// Temporarily simulate an approved leave for student #2 (Jane Smith)
$leaveDate = date('Y-m-d', strtotime('+1 day'));
$leaveId = \App\Models\LeaveApplication::insert([
    'student_id' => 2,
    'leave_date' => $leaveDate,
    'reason' => 'Medical appointment at General Hospital',
    'status' => 'pending',
]);
assertTest("Student leave application inserted with pending status", $leaveId > 0);

\App\Models\LeaveApplication::update($leaveId, [
    'status' => 'approved',
    'review_note' => 'Approved by Medical Registrar',
    'reviewed_by' => 1,
    'reviewed_at' => date('Y-m-d H:i:s'),
]);
assertTest("Leave application approved by admin", \App\Models\LeaveApplication::hasApprovedLeave(2, $leaveDate));

// Simulate scan attempt on leave date
// We override date check logic temporarily for test
$leaveToday = date('Y-m-d');
$leaveTodayId = \App\Models\LeaveApplication::insert([
    'student_id' => 3,
    'leave_date' => $leaveToday,
    'reason' => 'Official athletic competition',
    'status' => 'approved',
]);

$attScanOnLeave = \App\Services\AttendanceService::processScan(3, 0.92);
assertTest("Scan rejected when student has approved leave today", 
    $attScanOnLeave['success'] === false && $attScanOnLeave['status'] === 'on_leave'
);

// CLEAN UP: Immediately delete the temporary test leave records so database stays clean
\App\Core\Database::query("DELETE FROM student_leave_applications WHERE id IN (:id1, :id2)", [
    ':id1' => $leaveId,
    ':id2' => $leaveTodayId
]);

// 9. Test Audit Logs Generation
$recentLogs = \App\Models\AuditLog::allWithDetails(5);
assertTest("Audit logs automatically created and populated", count($recentLogs) > 0);

// 10. Summary
echo "\n========================================================\n";
echo "   TEST SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
