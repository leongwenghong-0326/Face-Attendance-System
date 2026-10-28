<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $today = date('Y-m-d');

        // Statistics
        $totalStudents = Student::count();
        $activeStudents = Student::count("status = 'active'");
        $totalTeachers = Teacher::count();
        $totalClasses = SchoolClass::count("status = 'active'");
        $faceRegistered = Student::count("is_face_registered = 1");
        $pendingLeaves = LeaveApplication::countPending();

        $todayAttendance = Attendance::getDashboardStats($today);

        // Recent 10 attendance records today
        $recentAttendance = Attendance::search(['date' => $today]);
        $recentAttendance = array_slice($recentAttendance, 0, 10);

        // Pending leave applications
        $recentLeaves = LeaveApplication::allWithDetails('pending');
        $recentLeaves = array_slice($recentLeaves, 0, 5);

        $this->view('admin/dashboard', [
            'title' => 'Administrator Dashboard - Apex Attendance System',
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'faceRegistered' => $faceRegistered,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
            'recentAttendance' => $recentAttendance,
            'recentLeaves' => $recentLeaves,
            'today' => $today,
        ]);
    }
}
