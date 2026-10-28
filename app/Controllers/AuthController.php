<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(Request $request): void
    {
        $this->view('auth/login', [
            'title' => 'Sign In - Apex Attendance System'
        ], 'layouts/auth');
    }

    public function login(Request $request): void
    {
        $identifier = trim($request->input('login', ''));
        $password = (string)$request->input('password', '');

        if ($identifier === '' || $password === '') {
            $this->flash('error', 'Please provide both username/email and password.');
            $this->redirect('/login');
            return;
        }

        $user = User::findByUsernameOrEmail($identifier);

        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            $this->flash('error', 'Invalid login credentials or account inactive.');
            $this->redirect('/login');
            return;
        }

        // Regenerate session ID for security (Requirement 3: Regenerate session ID after successful login)
        Session::regenerate();

        // Fetch associated profile if teacher or student
        $profile = null;
        if ($user['role'] === 'student') {
            $profile = Student::findByUserId($user['id']);
        } elseif ($user['role'] === 'teacher') {
            $profile = Teacher::findByUserId($user['id']);
        }

        $sessionData = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'profile' => $profile,
            'student_id' => $profile['id'] ?? null,
            'teacher_id' => $profile['id'] ?? null,
            'full_name' => $profile['full_name'] ?? ($user['role'] === 'admin' ? 'System Administrator' : ucfirst($user['username'])),
        ];

        Session::set('user', $sessionData);

        // Audit log
        AuditLog::log("User Logged In ({$user['role']})", $user['id'], $profile['id'] ?? null);

        $this->flash('success', "Welcome back, {$sessionData['full_name']}!");

        // Redirect based on role
        $destination = match ($user['role']) {
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
            default => '/login',
        };

        $this->redirect($destination);
    }

    public function logout(Request $request): void
    {
        $user = Session::get('user');
        if ($user) {
            AuditLog::log("User Logged Out", $user['id']);
        }

        Session::destroy();
        Session::setFlash('success', 'You have been safely logged out.');
        $this->redirect('/login');
    }
}
