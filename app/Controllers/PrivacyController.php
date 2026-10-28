<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class PrivacyController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('privacy/index', [
            'title' => 'Biometric Data & Privacy Policy - Apex Attendance System'
        ], 'layouts/main');
    }
}
