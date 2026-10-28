<?php
use App\Core\Env;

return [
    'session_lifetime' => (int) Env::get('SESSION_LIFETIME', 120),
    'secure_cookie' => Env::get('SESSION_SECURE_COOKIE', false),
    'roles' => [
        'admin' => 'Administrator',
        'teacher' => 'Teacher',
        'student' => 'Student',
    ],
];
