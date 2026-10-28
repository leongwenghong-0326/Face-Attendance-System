<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Services\ExportService;

class AttendanceController extends Controller
{
    public function index(Request $request): void
    {
        $date = $request->input('date', date('Y-m-d'));
        $classId = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $status = $request->input('status');
        $query = trim($request->input('q', ''));

        $filters = [
            'date' => $date,
            'class_id' => $classId,
            'status' => $status,
            'query' => $query,
        ];

        $records = Attendance::search($filters);
        $classes = SchoolClass::all('class_name ASC');

        $this->view('admin/attendance/index', [
            'title' => 'Attendance Records - Apex Attendance',
            'records' => $records,
            'classes' => $classes,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): void
    {
        $date = $request->input('date');
        $classId = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $status = $request->input('status');
        $query = trim($request->input('q', ''));

        $filters = [
            'date' => $date,
            'class_id' => $classId,
            'status' => $status,
            'query' => $query,
        ];

        $records = Attendance::search($filters);
        ExportService::exportAttendanceCsv($records, 'attendance_' . ($date ?: 'all') . '.csv');
    }
}
