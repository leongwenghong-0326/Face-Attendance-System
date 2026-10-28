<?php
use App\Core\Env;

return [
    'name' => Env::get('APP_NAME', 'Apex Facial Attendance System'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', false),
    'url' => Env::get('APP_URL', 'http://localhost/face_attendance_system/public'),
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'max_upload_size' => (int) Env::get('MAX_UPLOAD_SIZE_MB', 5) * 1024 * 1024,
    'allowed_document_types' => explode(',', Env::get('ALLOWED_DOCUMENT_TYPES', 'pdf,jpg,jpeg,png')),
    'allowed_document_mimes' => explode(',', Env::get('ALLOWED_DOCUMENT_MIMES', 'application/pdf,image/jpeg,image/png')),
];
