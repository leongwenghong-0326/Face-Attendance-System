<?php
namespace App\Controllers\Student;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\Uploader;
use App\Models\AuditLog;
use App\Models\LeaveApplication;
use App\Models\Student;

class LeaveController extends Controller
{
    public function index(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $leaves = LeaveApplication::getByStudent((int)$student['id']);

        $this->view('student/leaves/index', [
            'title' => 'Leave Applications - Student Portal',
            'leaves' => $leaves,
            'student' => $student,
        ]);
    }

    public function store(Request $request): void
    {
        $student = Student::findByUserId($this->userId());
        $studentId = (int)$student['id'];

        $errors = $this->validate($request->all(), [
            'leave_date' => 'required|date',
            'reason' => 'required|min:5|max:1000',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect('/student/leaves');
            return;
        }

        $documentPath = null;
        $file = $request->file('document');

        if ($file && !empty($file['name'])) {
            $uploadResult = Uploader::uploadLeaveDocument($file);
            if (!$uploadResult['success']) {
                $this->flash('error', $uploadResult['error']);
                $this->redirect('/student/leaves');
                return;
            }
            $documentPath = $uploadResult['path'];
        }

        LeaveApplication::insert([
            'student_id' => $studentId,
            'leave_date' => $request->input('leave_date'),
            'reason' => trim($request->input('reason')),
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);

        AuditLog::log("Leave Application Submitted ({$request->input('leave_date')})", $this->userId(), $studentId);

        $this->flash('success', 'Leave application submitted successfully for administrator approval.');
        $this->redirect('/student/leaves');
    }
}
