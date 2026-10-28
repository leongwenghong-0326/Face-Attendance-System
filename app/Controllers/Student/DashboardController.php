<?php
namespace App\Controllers\Student;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\RestAttendance;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        if (!$student) {
            $this->flash('error', 'Student record not found.');
            $this->redirect('/login');
            return;
        }

        $studentId = (int)$student['id'];
        $today = date('Y-m-d');

        // Today attendance
        $todayRecord = Attendance::getTodayRecord($studentId, $today);
        $todayRest = RestAttendance::getTodayRecord($studentId, $today);

        // Attendance statistics
        $stats = Attendance::getStudentStats($studentId);

        // Recent 5 attendance records
        $recentRecords = Attendance::search(['student_id' => $studentId]);
        $recentRecords = array_slice($recentRecords, 0, 5);

        // Recent leave applications
        $recentLeaves = LeaveApplication::getByStudent($studentId);
        $recentLeaves = array_slice($recentLeaves, 0, 3);

        $this->view('student/dashboard', [
            'title' => 'Student Portal - Apex Attendance',
            'student' => $student,
            'todayRecord' => $todayRecord,
            'todayRest' => $todayRest,
            'stats' => $stats,
            'recentRecords' => $recentRecords,
            'recentLeaves' => $recentLeaves,
            'today' => $today,
        ]);
    }
}
