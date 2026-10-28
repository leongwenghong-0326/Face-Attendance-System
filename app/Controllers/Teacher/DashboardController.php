<?php
namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherAssignment;

class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $teacher = Teacher::findByUserId($this->userId());
        if (!$teacher) {
            $this->flash('error', 'Teacher profile not linked to this account.');
            $this->redirect('/login');
            return;
        }

        $teacherId = (int)$teacher['id'];
        $assignedClasses = TeacherAssignment::getClassesByTeacher($teacherId);
        $assignedSubjects = TeacherAssignment::getSubjectsByTeacher($teacherId);

        // Collect class IDs assigned to teacher
        $classIds = array_column($assignedClasses, 'id');

        // Total students in these classes
        $studentsCount = 0;
        $todayAttendance = [];
        $today = date('Y-m-d');

        if (!empty($classIds)) {
            $inClause = implode(',', array_map('intval', $classIds));
            $studentsCount = Student::count("class_id IN ({$inClause}) AND status = 'active'");

            // Filter today's attendance for teacher's assigned classes only
            $allToday = Attendance::search(['date' => $today]);
            foreach ($allToday as $rec) {
                if (in_array((int)$rec['class_id'], $classIds, true)) {
                    $todayAttendance[] = $rec;
                }
            }
        }

        $this->view('teacher/dashboard', [
            'title' => 'Teacher Portal - Apex Attendance',
            'teacher' => $teacher,
            'assignedClasses' => $assignedClasses,
            'assignedSubjects' => $assignedSubjects,
            'studentsCount' => $studentsCount,
            'todayAttendance' => $todayAttendance,
            'today' => $today,
        ]);
    }
}
