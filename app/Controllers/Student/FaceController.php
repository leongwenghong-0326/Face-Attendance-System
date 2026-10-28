<?php
namespace App\Controllers\Student;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;
use App\Models\Student;

class FaceController extends Controller
{
    public function register(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $settings = Setting::getAllAsKeyValue();

        $this->view('student/face/register', [
            'title' => ($student['is_face_registered'] ? 'Re-Register' : 'Register') . ' Face - Biometrics',
            'student' => $student,
            'settings' => $settings,
        ]);
    }

    public function scan(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $settings = Setting::getAllAsKeyValue();

        $this->view('student/face/scan', [
            'title' => 'Self Attendance Scanner - Student Portal',
            'student' => $student,
            'settings' => $settings,
        ]);
    }
}
