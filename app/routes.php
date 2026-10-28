<?php
use App\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Controllers\Admin\ClassController as AdminClassController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Controllers\Admin\SettingController as AdminSettingController;
use App\Controllers\Admin\StudentController as AdminStudentController;
use App\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\KioskController;
use App\Controllers\PrivacyController;
use App\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Controllers\Student\DashboardController as StudentDashboardController;
use App\Controllers\Student\FaceController as StudentFaceController;
use App\Controllers\Student\LeaveController as StudentLeaveController;
use App\Controllers\Student\ProfileController as StudentProfileController;
use App\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Core\App;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\StudentMiddleware;
use App\Middleware\TeacherMiddleware;

$router = App::router();

// Root redirect
$router->get('/', function ($req) {
    if (\App\Core\Session::has('user')) {
        $role = \App\Core\Session::get('user')['role'] ?? 'student';
        $dest = match ($role) {
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
            default => '/login',
        };
        (new \App\Core\Response())->redirect($dest);
    } else {
        (new \App\Core\Response())->redirect('/login');
    }
});

// Authentication
$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);

// Privacy statement (Accessible by all)
$router->get('/privacy', [PrivacyController::class, 'index']);

// Attendance Kiosk
$router->get('/kiosk', [KioskController::class, 'index']);

// Public / Authenticated APIs for Face Engine & Kiosk
$router->get('/api/face/descriptors', [ApiController::class, 'getDescriptors']);
$router->get('/api/settings', [ApiController::class, 'getSettings']);
$router->post('/api/face/register', [ApiController::class, 'registerFace'], [AuthMiddleware::class]);
$router->post('/api/attendance/scan', [ApiController::class, 'scanAttendance']);

// ========================================================
// ADMIN PORTAL ROUTES
// ========================================================
$adminAuth = [AuthMiddleware::class, AdminMiddleware::class];
$adminAuthCsrf = [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class];

// Dashboard
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], $adminAuth);

// Student Management
$router->get('/admin/students', [AdminStudentController::class, 'index'], $adminAuth);
$router->get('/admin/students/create', [AdminStudentController::class, 'create'], $adminAuth);
$router->post('/admin/students/create', [AdminStudentController::class, 'store'], $adminAuthCsrf);
$router->get('/admin/students/export', [AdminStudentController::class, 'export'], $adminAuth);
$router->get('/admin/students/{id}', [AdminStudentController::class, 'show'], $adminAuth);
$router->get('/admin/students/{id}/edit', [AdminStudentController::class, 'edit'], $adminAuth);
$router->post('/admin/students/{id}/edit', [AdminStudentController::class, 'update'], $adminAuthCsrf);
$router->post('/admin/students/{id}/delete', [AdminStudentController::class, 'delete'], $adminAuthCsrf);
$router->get('/admin/students/{id}/register-face', [AdminStudentController::class, 'registerFace'], $adminAuth);

// Teacher Management
$router->get('/admin/teachers', [AdminTeacherController::class, 'index'], $adminAuth);
$router->get('/admin/teachers/create', [AdminTeacherController::class, 'create'], $adminAuth);
$router->post('/admin/teachers/create', [AdminTeacherController::class, 'store'], $adminAuthCsrf);
$router->get('/admin/teachers/{id}/edit', [AdminTeacherController::class, 'edit'], $adminAuth);
$router->post('/admin/teachers/{id}/edit', [AdminTeacherController::class, 'update'], $adminAuthCsrf);
$router->post('/admin/teachers/{id}/delete', [AdminTeacherController::class, 'delete'], $adminAuthCsrf);

// Class Management
$router->get('/admin/classes', [AdminClassController::class, 'index'], $adminAuth);
$router->get('/admin/classes/create', [AdminClassController::class, 'create'], $adminAuth);
$router->post('/admin/classes/create', [AdminClassController::class, 'store'], $adminAuthCsrf);
$router->get('/admin/classes/{id}/edit', [AdminClassController::class, 'edit'], $adminAuth);
$router->post('/admin/classes/{id}/edit', [AdminClassController::class, 'update'], $adminAuthCsrf);
$router->post('/admin/classes/{id}/delete', [AdminClassController::class, 'delete'], $adminAuthCsrf);

// Subject Management
$router->get('/admin/subjects', [AdminSubjectController::class, 'index'], $adminAuth);
$router->get('/admin/subjects/create', [AdminSubjectController::class, 'create'], $adminAuth);
$router->post('/admin/subjects/create', [AdminSubjectController::class, 'store'], $adminAuthCsrf);
$router->get('/admin/subjects/{id}/edit', [AdminSubjectController::class, 'edit'], $adminAuth);
$router->post('/admin/subjects/{id}/edit', [AdminSubjectController::class, 'update'], $adminAuthCsrf);
$router->post('/admin/subjects/{id}/delete', [AdminSubjectController::class, 'delete'], $adminAuthCsrf);

// Curriculum and Teaching Assignments
$router->get('/admin/assignments', [AdminAssignmentController::class, 'index'], $adminAuth);
$router->post('/admin/assignments/class-subject', [AdminAssignmentController::class, 'assignClassSubject'], $adminAuthCsrf);
$router->post('/admin/assignments/class-subject/{id}/delete', [AdminAssignmentController::class, 'removeClassSubject'], $adminAuthCsrf);
$router->post('/admin/assignments/teacher', [AdminAssignmentController::class, 'assignTeacher'], $adminAuthCsrf);
$router->post('/admin/assignments/teacher/{id}/delete', [AdminAssignmentController::class, 'removeTeacherAssignment'], $adminAuthCsrf);

// Attendance Records & Export
$router->get('/admin/attendance', [AdminAttendanceController::class, 'index'], $adminAuth);
$router->get('/admin/attendance/export', [AdminAttendanceController::class, 'export'], $adminAuth);

// Leave Management
$router->get('/admin/leaves', [AdminLeaveController::class, 'index'], $adminAuth);
$router->post('/admin/leaves/{id}/approve', [AdminLeaveController::class, 'approve'], $adminAuthCsrf);
$router->post('/admin/leaves/{id}/reject', [AdminLeaveController::class, 'reject'], $adminAuthCsrf);

// Face Recognition Settings
$router->get('/admin/settings', [AdminSettingController::class, 'index'], $adminAuth);
$router->post('/admin/settings', [AdminSettingController::class, 'update'], $adminAuthCsrf);

// Audit Logs
$router->get('/admin/audit-logs', [AdminAuditLogController::class, 'index'], $adminAuth);

// ========================================================
// TEACHER PORTAL ROUTES
// ========================================================
$teacherAuth = [AuthMiddleware::class, TeacherMiddleware::class];

$router->get('/teacher/dashboard', [TeacherDashboardController::class, 'index'], $teacherAuth);
$router->get('/teacher/attendance', [TeacherAttendanceController::class, 'index'], $teacherAuth);
$router->get('/teacher/attendance/student/{id}', [TeacherAttendanceController::class, 'studentDetails'], $teacherAuth);

// ========================================================
// STUDENT PORTAL ROUTES
// ========================================================
$studentAuth = [AuthMiddleware::class, StudentMiddleware::class];
$studentAuthCsrf = [AuthMiddleware::class, StudentMiddleware::class, CsrfMiddleware::class];

$router->get('/student/dashboard', [StudentDashboardController::class, 'index'], $studentAuth);
$router->get('/student/attendance', [StudentAttendanceController::class, 'index'], $studentAuth);
$router->get('/student/face/register', [StudentFaceController::class, 'register'], $studentAuth);
$router->get('/student/face/scan', [StudentFaceController::class, 'scan'], $studentAuth);
$router->get('/student/leaves', [StudentLeaveController::class, 'index'], $studentAuth);
$router->post('/student/leaves', [StudentLeaveController::class, 'store'], $studentAuthCsrf);
$router->get('/student/profile', [StudentProfileController::class, 'index'], $studentAuth);
$router->post('/student/profile/password', [StudentProfileController::class, 'updatePassword'], $studentAuthCsrf);
