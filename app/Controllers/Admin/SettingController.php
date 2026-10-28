<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request): void
    {
        $settings = Setting::getAllAsKeyValue();
        $this->view('admin/settings/index', [
            'title' => 'System & Face Recognition Settings - Apex Attendance',
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): void
    {
        $keys = [
            'min_face_size',
            'max_face_size',
            'min_brightness',
            'max_brightness',
            'min_sharpness',
            'detection_confidence',
            'match_threshold',
            'min_confidence',
            'stable_frames',
            'duplicate_scan_interval',
            'registration_samples',
            'institution_name',
            'system_timezone',
        ];

        foreach ($keys as $k) {
            $val = $request->input($k);
            if ($val !== null) {
                $group = in_array($k, ['institution_name', 'system_timezone']) ? 'general' : 'face';
                Setting::set($k, trim($val), $group);
            }
        }

        AuditLog::log("System & Face Recognition Settings Updated", $this->userId());

        $this->flash('success', 'Configuration settings updated successfully.');
        $this->redirect('/admin/settings');
    }
}
