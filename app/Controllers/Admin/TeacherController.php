<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\User;

class TeacherController extends Controller
{
    public function index(Request $request): void
    {
        $teachers = Teacher::allWithUser();
        $this->view('admin/teachers/index', [
            'title' => 'Teacher Management - Apex Attendance',
            'teachers' => $teachers,
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('admin/teachers/create', [
            'title' => 'Add New Teacher'
        ]);
    }

    public function store(Request $request): void
    {
        $errors = $this->validate($request->all(), [
            'username' => 'required|min:3|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'teacher_code' => 'required|min:3|max:30|unique:teachers,teacher_code',
            'full_name' => 'required|min:2|max:100',
            'password' => 'required|min:6',
            'gender' => 'required|in:male,female,other',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect('/admin/teachers/create');
            return;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            $userId = User::insert([
                'username' => trim($request->input('username')),
                'email' => trim($request->input('email')),
                'password_hash' => User::hashPassword($request->input('password')),
                'role' => 'teacher',
                'status' => 'active',
            ]);

            $teacherId = Teacher::insert([
                'user_id' => $userId,
                'teacher_code' => trim($request->input('teacher_code')),
                'full_name' => trim($request->input('full_name')),
                'email' => trim($request->input('email')),
                'phone' => trim($request->input('phone', '')),
                'gender' => $request->input('gender', 'other'),
                'status' => 'active',
            ]);

            AuditLog::log("Teacher Added: {$request->input('full_name')}", $this->userId());

            $db->commit();
            $this->flash('success', "Teacher {$request->input('full_name')} added successfully!");
            $this->redirect('/admin/teachers');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->flash('error', 'Error adding teacher: ' . $e->getMessage());
            $this->redirect('/admin/teachers/create');
        }
    }

    public function edit(Request $request, string $id): void
    {
        $teacher = Teacher::findWithDetails((int)$id);
        if (!$teacher) {
            $this->flash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
            return;
        }

        $this->view('admin/teachers/edit', [
            'title' => 'Edit Teacher - ' . $teacher['full_name'],
            'teacher' => $teacher,
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $teacher = Teacher::find((int)$id);
        if (!$teacher) {
            $this->flash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
            return;
        }

        $userId = (int)$teacher['user_id'];

        $errors = $this->validate($request->all(), [
            'full_name' => 'required|min:2|max:100',
            'email' => "required|email|unique:users,email,{$userId},id",
            'teacher_code' => "required|min:3|max:30|unique:teachers,teacher_code,{$id},id",
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect("/admin/teachers/{$id}/edit");
            return;
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            $userData = [
                'email' => trim($request->input('email')),
                'status' => $request->input('status'),
            ];
            if (!empty($request->input('password'))) {
                $userData['password_hash'] = User::hashPassword($request->input('password'));
            }
            User::update($userId, $userData);

            Teacher::update((int)$id, [
                'teacher_code' => trim($request->input('teacher_code')),
                'full_name' => trim($request->input('full_name')),
                'email' => trim($request->input('email')),
                'phone' => trim($request->input('phone', '')),
                'gender' => $request->input('gender', 'other'),
                'status' => $request->input('status'),
            ]);

            AuditLog::log("Teacher Updated: {$request->input('full_name')}", $this->userId());

            $db->commit();
            $this->flash('success', 'Teacher updated successfully.');
            $this->redirect('/admin/teachers');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->flash('error', 'Update error: ' . $e->getMessage());
            $this->redirect("/admin/teachers/{$id}/edit");
        }
    }

    public function delete(Request $request, string $id): void
    {
        $teacher = Teacher::find((int)$id);
        if ($teacher) {
            $name = $teacher['full_name'];
            $userId = (int)$teacher['user_id'];
            User::delete($userId);
            AuditLog::log("Teacher Removed: {$name}", $this->userId());
            $this->flash('success', "Teacher {$name} removed.");
        }
        $this->redirect('/admin/teachers');
    }
}
