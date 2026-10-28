<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\LeaveApplication;
use App\Models\Student;

class LeaveController extends Controller
{
    public function index(Request $request): void
    {
        $status = $request->input('status');
        $leaves = LeaveApplication::allWithDetails($status);

        $this->view('admin/leaves/index', [
            'title' => 'Student Leave Applications - Apex Attendance',
            'leaves' => $leaves,
            'selectedStatus' => $status,
        ]);
    }

    public function approve(Request $request, string $id): void
    {
        $leave = LeaveApplication::find((int)$id);
        if (!$leave) {
            $this->flash('error', 'Leave application not found.');
            $this->redirect('/admin/leaves');
            return;
        }

        $note = trim($request->input('review_note', ''));

        LeaveApplication::update((int)$id, [
            'status' => 'approved',
            'review_note' => $note ?: 'Approved by Administrator',
            'reviewed_by' => $this->userId(),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        // If today or future date, record attendance placeholder as on_leave
        $student = Student::find((int)$leave['student_id']);
        if ($student) {
            $existingAtt = Attendance::getTodayRecord($student['id'], $leave['leave_date']);
            if (!$existingAtt) {
                Attendance::insert([
                    'student_id' => $student['id'],
                    'class_id' => (int)($student['class_id'] ?? 0),
                    'attendance_date' => $leave['leave_date'],
                    'status' => 'on_leave',
                    'notes' => 'Official Leave Approved: ' . substr($leave['reason'], 0, 50),
                ]);
            } else {
                Attendance::update($existingAtt['id'], [
                    'status' => 'on_leave',
                    'notes' => 'Official Leave Approved',
                ]);
            }
        }

        AuditLog::log("Leave Approved for student #{$leave['student_id']}", $this->userId(), (int)$leave['student_id']);

        $this->flash('success', 'Leave application approved.');
        $this->redirect('/admin/leaves');
    }

    public function reject(Request $request, string $id): void
    {
        $leave = LeaveApplication::find((int)$id);
        if (!$leave) {
            $this->flash('error', 'Leave application not found.');
            $this->redirect('/admin/leaves');
            return;
        }

        $note = trim($request->input('review_note', ''));

        LeaveApplication::update((int)$id, [
            'status' => 'rejected',
            'review_note' => $note ?: 'Rejected by Administrator',
            'reviewed_by' => $this->userId(),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log("Leave Rejected for student #{$leave['student_id']}", $this->userId(), (int)$leave['student_id']);

        $this->flash('info', 'Leave application has been rejected.');
        $this->redirect('/admin/leaves');
    }
}
