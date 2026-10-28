<?php
namespace App\Controllers\Student;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $this->view('student/profile/index', [
            'title' => 'My Profile & Security - Student Portal',
            'student' => $student,
        ]);
    }

    public function updatePassword(Request $request): void
    {
        $user = User::find($this->userId());

        $currentPassword = (string)$request->input('current_password', '');
        $newPassword = (string)$request->input('new_password', '');
        $confirmPassword = (string)$request->input('new_password_confirmation', '');

        if (!User::verifyPassword($currentPassword, $user['password_hash'])) {
            $this->flash('error', 'Your current password is incorrect.');
            $this->redirect('/student/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->flash('error', 'New password must be at least 6 characters long.');
            $this->redirect('/student/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->flash('error', 'New password confirmation does not match.');
            $this->redirect('/student/profile');
            return;
        }

        User::update($this->userId(), [
            'password_hash' => User::hashPassword($newPassword)
        ]);

        AuditLog::log("Student Changed Password", $this->userId());

        $this->flash('success', 'Password updated successfully!');
        $this->redirect('/student/profile');
    }
}
