<?php
namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherAssignment;

class AttendanceController extends Controller
{
    public function index(Request $request): void
    {
        $teacher = Teacher::findByUserId($this->userId());
        if (!$teacher) {
            $this->flash('error', 'Teacher profile not found.');
            $this->redirect('/login');
            return;
        }

        $teacherId = (int)$teacher['id'];
        $assignedClasses = TeacherAssignment::getClassesByTeacher($teacherId);
        $assignedClassIds = array_column($assignedClasses, 'id');

        $date = $request->input('date', date('Y-m-d'));
        $selectedClass = $request->input('class_id') ? (int)$request->input('class_id') : null;

        // If a class is selected, verify teacher is assigned to it
        if ($selectedClass && !in_array($selectedClass, $assignedClassIds, true)) {
            $this->flash('error', 'You are not assigned to this class.');
            $this->redirect('/teacher/attendance');
            return;
        }

        $records = [];
        if (!empty($assignedClassIds)) {
            $targetClassId = $selectedClass ?: null;
            $allRecords = Attendance::search(['date' => $date, 'class_id' => $targetClassId]);

            // Filter to only assigned classes
            foreach ($allRecords as $r) {
                if (in_array((int)$r['class_id'], $assignedClassIds, true)) {
                    $records[] = $r;
                }
            }
        }

        $this->view('teacher/attendance/index', [
            'title' => 'Assigned Class Attendance - Teacher Portal',
            'records' => $records,
            'assignedClasses' => $assignedClasses,
            'selectedClass' => $selectedClass,
            'date' => $date,
        ]);
    }

    public function studentDetails(Request $request, string $studentId): void
    {
        $teacher = Teacher::findByUserId($this->userId());
        $assignedClasses = TeacherAssignment::getClassesByTeacher((int)$teacher['id']);
        $assignedClassIds = array_column($assignedClasses, 'id');

        $student = Student::findWithDetails((int)$studentId);
        if (!$student || !in_array((int)$student['class_id'], $assignedClassIds, true)) {
            $this->flash('error', 'Student not found or not in your assigned cohorts.');
            $this->redirect('/teacher/attendance');
            return;
        }

        $stats = Attendance::getStudentStats((int)$studentId);
        $attendanceHistory = Attendance::search(['student_id' => (int)$studentId]);

        $this->view('teacher/attendance/student', [
            'title' => 'Student Attendance Profile - ' . $student['full_name'],
            'student' => $student,
            'stats' => $stats,
            'attendanceHistory' => $attendanceHistory,
        ]);
    }
}
