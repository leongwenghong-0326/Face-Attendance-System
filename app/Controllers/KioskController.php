<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;

class KioskController extends Controller
{
    public function index(Request $request): void
    {
        $settings = Setting::getAllAsKeyValue();
        $this->view('kiosk/index', [
            'title' => 'Attendance Kiosk - Apex Facial Recognition Station',
            'settings' => $settings,
        ], null); // Kiosk has its own fullscreen view without sidebar
    }
}
