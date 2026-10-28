<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request): void
    {
        $logs = AuditLog::allWithDetails(300);
        $this->view('admin/audit_logs/index', [
            'title' => 'System Audit & Activity Logs - Apex Attendance',
            'logs' => $logs,
        ]);
    }
}
