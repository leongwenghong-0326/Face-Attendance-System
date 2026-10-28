<?php
namespace App\Controllers\Student;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\Student;

class AttendanceController extends Controller
{
    public function index(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $studentId = (int)$student['id'];

        $today = date('Y-m-d');
        $todayRecord = Attendance::getTodayRecord($studentId, $today);

        $date = $request->input('date');
        $month = $request->input('month');
        $status = $request->input('status');

        $filters = ['student_id' => $studentId];
        if (!empty($date)) {
            $filters['date'] = $date;
        }
        if (!empty($status)) {
            $filters['status'] = $status;
        }

        $records = Attendance::search($filters);

        // If month filter is applied
        if (!empty($month)) {
            $records = array_filter($records, function ($r) use ($month) {
                return str_starts_with($r['attendance_date'], $month);
            });
        }

        $stats = Attendance::getStudentStats($studentId);

        $this->view('student/attendance/index', [
            'title' => 'My Attendance & Analytics - Student Portal',
            'student' => $student,
            'todayRecord' => $todayRecord,
            'records' => $records,
            'stats' => $stats,
            'filters' => [
                'date' => $date,
                'month' => $month,
                'status' => $status,
            ],
        ]);
    }
}
