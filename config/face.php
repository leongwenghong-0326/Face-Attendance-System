<?php
use App\Core\Env;

return [
    'model_url' => Env::get('FACE_MODEL_PATH', '/assets/models'),
    'duplicate_scan_interval' => (int) Env::get('DUPLICATE_SCAN_INTERVAL', 300),
    'match_threshold' => (float) Env::get('FACE_MATCH_THRESHOLD', 0.55),
    'min_attendance_confidence' => (float) Env::get('MIN_ATTENDANCE_CONFIDENCE', 0.45),
];
