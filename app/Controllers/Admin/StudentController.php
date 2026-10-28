<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\FaceDescriptor;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Setting;
use App\Models\User;
use App\Services\ExportService;

class StudentController extends Controller
{
    public function index(Request $request): void
    {
        $q = trim($request->input('q', ''));
        $classId = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $status = $request->input('status');

        $students = Student::search($q, $classId, $status);
        $classes = SchoolClass::all('class_name ASC');

        $this->view('admin/students/index', [
            'title' => 'Student Management - Apex Attendance',
            'students' => $students,
            'classes' => $classes,
            'q' => $q,
            'selectedClass' => $classId,
            'selectedStatus' => $status,
        ]);
    }

    public function create(Request $request): void
    {
        $classes = SchoolClass::where('status', 'active', 'class_name ASC');
        $this->view('admin/students/create', [
            'title' => 'Add New Student',
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): void
    {
        $errors = $this->validate($request->all(), [
            'username' => 'required|min:3|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'student_code' => 'required|min:3|max:30|unique:students,student_code',
            'full_name' => 'required|min:2|max:100',
            'password' => 'required|min:6',
            'class_id' => 'required|numeric',
            'gender' => 'required|in:male,female,other',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect('/admin/students/create');
            return;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            // 1. Create User
            $userId = User::insert([
                'username' => trim($request->input('username')),
                'email' => trim($request->input('email')),
                'password_hash' => User::hashPassword($request->input('password')),
                'role' => 'student',
                'status' => 'active',
            ]);

            // 2. Create Student
            $studentId = Student::insert([
                'user_id' => $userId,
                'student_code' => trim($request->input('student_code')),
                'full_name' => trim($request->input('full_name')),
                'email' => trim($request->input('email')),
                'phone' => trim($request->input('phone', '')),
                'gender' => $request->input('gender', 'other'),
                'date_of_birth' => $request->input('date_of_birth') ?: null,
                'class_id' => (int)$request->input('class_id'),
                'status' => 'active',
                'is_face_registered' => 0,
                'face_version' => 0,
            ]);

            AuditLog::log("Student Created: {$request->input('full_name')}", $this->userId(), $studentId);

            $db->commit();
            $this->flash('success', "Student {$request->input('full_name')} created successfully!");
            $this->redirect('/admin/students');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->flash('error', 'Error creating student: ' . $e->getMessage());
            $this->redirect('/admin/students/create');
        }
    }

    public function show(Request $request, string $id): void
    {
        $student = Student::findWithDetails((int)$id);
        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/students');
            return;
        }

        $stats = Attendance::getStudentStats((int)$id);
        $attendanceHistory = Attendance::search(['student_id' => (int)$id]);
        $descriptors = FaceDescriptor::getActiveDescriptorsForStudent((int)$id);

        $this->view('admin/students/show', [
            'title' => 'Student Profile - ' . $student['full_name'],
            'student' => $student,
            'stats' => $stats,
            'attendanceHistory' => $attendanceHistory,
            'descriptors' => $descriptors,
        ]);
    }

    public function edit(Request $request, string $id): void
    {
        $student = Student::findWithDetails((int)$id);
        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/students');
            return;
        }

        $classes = SchoolClass::all('class_name ASC');
        $this->view('admin/students/edit', [
            'title' => 'Edit Student - ' . $student['full_name'],
            'student' => $student,
            'classes' => $classes,
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $student = Student::find((int)$id);
        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/students');
            return;
        }

        $userId = (int)$student['user_id'];

        $errors = $this->validate($request->all(), [
            'full_name' => 'required|min:2|max:100',
            'email' => "required|email|unique:users,email,{$userId},id",
            'student_code' => "required|min:3|max:30|unique:students,student_code,{$id},id",
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect("/admin/students/{$id}/edit");
            return;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            // Update user email and status
            $userData = [
                'email' => trim($request->input('email')),
                'status' => $request->input('status'),
            ];
            if (!empty($request->input('password'))) {
                $userData['password_hash'] = User::hashPassword($request->input('password'));
            }
            User::update($userId, $userData);

            // Update student record
            Student::update((int)$id, [
                'full_name' => trim($request->input('full_name')),
                'student_code' => trim($request->input('student_code')),
                'email' => trim($request->input('email')),
                'phone' => trim($request->input('phone', '')),
                'gender' => $request->input('gender', 'other'),
                'date_of_birth' => $request->input('date_of_birth') ?: null,
                'class_id' => $request->input('class_id') ? (int)$request->input('class_id') : null,
                'status' => $request->input('status'),
            ]);

            AuditLog::log("Student Updated: {$request->input('full_name')}", $this->userId(), (int)$id);

            $db->commit();
            $this->flash('success', 'Student details updated successfully.');
            $this->redirect("/admin/students/{$id}");
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->flash('error', 'Update error: ' . $e->getMessage());
            $this->redirect("/admin/students/{$id}/edit");
        }
    }

    public function delete(Request $request, string $id): void
    {
        $student = Student::find((int)$id);
        if ($student) {
            $name = $student['full_name'];
            $userId = (int)$student['user_id'];

            // Delete user cascades to student, attendance, descriptors
            User::delete($userId);
            AuditLog::log("Student Deleted: {$name} (ID: {$id})", $this->userId());

            $this->flash('success', "Student {$name} removed successfully.");
        }
        $this->redirect('/admin/students');
    }

    public function registerFace(Request $request, string $id): void
    {
        $student = Student::findWithDetails((int)$id);
        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/students');
            return;
        }

        $this->view('admin/students/register_face', [
            'title' => ($student['is_face_registered'] ? 'Re-Register' : 'Register') . ' Face - ' . $student['full_name'],
            'student' => $student,
            'settings' => Setting::getAllAsKeyValue(),
        ]);
    }

    public function export(Request $request): void
    {
        $q = trim($request->input('q', ''));
        $classId = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $status = $request->input('status');

        $students = Student::search($q, $classId, $status);
        ExportService::exportStudentsCsv($students, 'students_' . date('Ymd_His') . '.csv');
    }
}
