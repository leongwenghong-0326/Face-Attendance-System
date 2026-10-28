<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;

class AssignmentController extends Controller
{
    public function index(Request $request): void
    {
        $classSubjects = ClassSubject::allWithDetails();
        $teacherAssignments = TeacherAssignment::allWithDetails();

        $classes = SchoolClass::where('status', 'active', 'class_name ASC');
        $subjects = Subject::where('status', 'active', 'subject_name ASC');
        $teachers = Teacher::where('status', 'active', 'full_name ASC');

        $this->view('admin/assignments/index', [
            'title' => 'Curriculum & Teaching Assignments - Apex Attendance',
            'classSubjects' => $classSubjects,
            'teacherAssignments' => $teacherAssignments,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    public function assignClassSubject(Request $request): void
    {
        $classId = (int)$request->input('class_id');
        $subjectId = (int)$request->input('subject_id');

        if ($classId <= 0 || $subjectId <= 0) {
            $this->flash('error', 'Please select both class and subject.');
            $this->redirect('/admin/assignments');
            return;
        }

        $ok = ClassSubject::assign($classId, $subjectId);
        if (!$ok) {
            $this->flash('warning', 'This subject is already assigned to the selected class.');
        } else {
            AuditLog::log("Class Subject Assigned (Class ID: {$classId}, Subject ID: {$subjectId})", $this->userId());
            $this->flash('success', 'Subject assigned to class successfully.');
        }

        $this->redirect('/admin/assignments');
    }

    public function removeClassSubject(Request $request, string $id): void
    {
        ClassSubject::delete((int)$id);
        $this->flash('success', 'Class subject assignment removed.');
        $this->redirect('/admin/assignments');
    }

    public function assignTeacher(Request $request): void
    {
        $teacherId = (int)$request->input('teacher_id');
        $classId = (int)$request->input('class_id');
        $subjectId = (int)$request->input('subject_id');

        if ($teacherId <= 0 || $classId <= 0 || $subjectId <= 0) {
            $this->flash('error', 'Please select teacher, class, and subject.');
            $this->redirect('/admin/assignments');
            return;
        }

        $ok = TeacherAssignment::assign($teacherId, $classId, $subjectId);
        if (!$ok) {
            $this->flash('warning', 'This teacher is already assigned to that class and subject.');
        } else {
            AuditLog::log("Teacher Assigned (Teacher ID: {$teacherId}, Class: {$classId}, Subject: {$subjectId})", $this->userId());
            $this->flash('success', 'Teacher assigned successfully.');
        }

        $this->redirect('/admin/assignments');
    }

    public function removeTeacherAssignment(Request $request, string $id): void
    {
        TeacherAssignment::delete((int)$id);
        $this->flash('success', 'Teacher assignment removed.');
        $this->redirect('/admin/assignments');
    }
}
