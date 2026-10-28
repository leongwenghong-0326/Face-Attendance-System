<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Attendance Kiosk') ?></title>
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="kiosk-fullscreen p-3 d-flex flex-column justify-content-between">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center flex-wrap px-3 px-md-4 py-2 bg-dark bg-opacity-50 rounded-3 border border-secondary border-opacity-25 shadow-sm gap-2">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <div class="p-2 rounded bg-primary text-white">
                <i class="bi bi-person-bounding-box fs-4 fs-md-3"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-white fs-6 fs-md-5"><?= e($settings['institution_name'] ?? 'Apex Academy of Technology') ?></h5>
                <div class="text-secondary small" style="font-size: 0.75rem;"><i class="bi bi-broadcast me-1 text-success"></i> Smart Facial Attendance Station</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <div class="text-end">
                <div id="kiosk-clock" class="fs-4 fs-md-2 fw-bold font-monospace text-warning">00:00:00</div>
                <div class="text-secondary small d-none d-sm-block" style="font-size: 0.75rem;"><?= date('l, d F Y') ?></div>
            </div>
            <a href="<?= url('/admin/dashboard') ?>" class="btn btn-outline-light btn-sm" title="Exit Kiosk Mode">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="container-fluid my-auto py-3">
        <div class="row align-items-center justify-content-center g-4">
            <!-- Left / Center: Camera Screen -->
            <div class="col-lg-7 text-center">
                <div class="camera-container mx-auto">
                    <video id="kiosk-video" class="camera-video" autoplay playsinline muted></video>
                    <canvas id="kiosk-canvas" class="camera-canvas" width="640" height="480"></canvas>
                    <div class="scan-radar"></div>
                </div>

                <div class="mt-3">
                    <div id="kiosk-status" class="alert alert-info py-2 mb-0 fw-semibold text-center shadow-sm">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Starting scanner and neural models...
                    </div>
                </div>
            </div>

            <!-- Right: Realtime Student Recognition Feedback & Recent Scans -->
            <div class="col-lg-5">
                <!-- Feedback Modal Card -->
                <div id="kiosk-feedback-card" class="mb-3" style="display: none;"></div>

                <!-- Recent Activity Panel -->
                <div class="card bg-dark bg-opacity-75 border-secondary border-opacity-25 shadow-sm">
                    <div class="card-header bg-transparent border-secondary border-opacity-25 text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Recent Campus Check-ins</span>
                        <span class="badge bg-secondary">Live</span>
                    </div>
                    <div class="card-body p-0">
                        <ul id="kiosk-recent-list" class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent text-secondary text-center py-4">
                                Standing by for faces...
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-secondary small py-2">
        Protected by Apex Biometrics &bull; AI Face Recognition Engine 2026 &bull; Automatic Duplicate Suppression (<?= (int)($settings['duplicate_scan_interval'] ?? 300) ?>s)
    </footer>

    <!-- Face API and Modular Scripts -->
    <script src="<?= asset('vendor/face-api.min.js') ?>"></script>
    <script src="<?= asset('js/face/camera.js') ?>"></script>
    <script src="<?= asset('js/face/face-engine.js') ?>"></script>
    <script src="<?= asset('js/face/quality.js') ?>"></script>
    <script src="<?= asset('js/face/pose.js') ?>"></script>
    <script src="<?= asset('js/face/matcher.js') ?>"></script>
    <script src="<?= asset('js/face/kiosk-flow.js') ?>"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const kiosk = new KioskFlow({
            videoElement: document.getElementById('kiosk-video'),
            canvasElement: document.getElementById('kiosk-canvas'),
            statusElement: document.getElementById('kiosk-status'),
            feedbackCardElement: document.getElementById('kiosk-feedback-card'),
            recentListElement: document.getElementById('kiosk-recent-list'),
            clockElement: document.getElementById('kiosk-clock'),
            modelUrl: '<?= asset('models') ?>',
            apiScanUrl: '<?= url('/api/attendance/scan') ?>',
            settings: <?= json_encode($settings) ?>
        });

        kiosk.init().catch(err => {
            console.error('Kiosk initialization error:', err);
            document.getElementById('kiosk-status').className = 'alert alert-danger py-2 text-center';
            document.getElementById('kiosk-status').textContent = 'Camera or neural network error: ' + err.message;
        });
    });
    </script>
</body>
</html>
