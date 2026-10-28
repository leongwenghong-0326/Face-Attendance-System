<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Self Attendance Facial Scanner</h4>
                <p class="text-muted small mb-0">Student: <?= e($student['full_name']) ?> (<?= e($student['student_code']) ?>)</p>
            </div>
            <a href="<?= url('/student/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm text-center p-3 p-md-4">
            <!-- Dynamic Status Alert -->
            <div id="scan-status" class="alert alert-info py-2 mb-3 fw-semibold shadow-sm">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Starting face scanner and loading neural models...
            </div>

            <!-- Attendance Result Card Feedback -->
            <div id="scan-result-card" style="display: none;"></div>

            <!-- Camera Viewport -->
            <div class="camera-container mx-auto mb-3">
                <video id="scan-video" class="camera-video" autoplay playsinline muted></video>
                <canvas id="scan-canvas" class="camera-canvas" width="640" height="480"></canvas>
                <div class="scan-radar"></div>
            </div>

            <p class="text-muted small mb-0">
                <i class="bi bi-shield-check me-1 text-success"></i>
                Position your face steadily within the viewport. Verified check-in or check-out attendance is recorded automatically.
            </p>
        </div>
    </div>
</div>

<!-- Face API and Modular Scripts -->
<script src="<?= asset('vendor/face-api.min.js') ?>"></script>
<script src="<?= asset('js/face/camera.js') ?>"></script>
<script src="<?= asset('js/face/face-engine.js') ?>"></script>
<script src="<?= asset('js/face/quality.js') ?>"></script>
<script src="<?= asset('js/face/pose.js') ?>"></script>
<script src="<?= asset('js/face/matcher.js') ?>"></script>
<script src="<?= asset('js/face/attendance-flow.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const flow = new AttendanceFlow({
        studentId: <?= (int)$student['id'] ?>,
        videoElement: document.getElementById('scan-video'),
        canvasElement: document.getElementById('scan-canvas'),
        statusElement: document.getElementById('scan-status'),
        resultCardElement: document.getElementById('scan-result-card'),
        modelUrl: '<?= asset('models') ?>',
        apiScanUrl: '<?= url('/api/attendance/scan') ?>',
        settings: <?= json_encode($settings) ?>
    });

    flow.init().catch(err => {
        console.error('Attendance scanner error:', err);
        document.getElementById('scan-status').className = 'alert alert-danger py-2 text-center';
        document.getElementById('scan-status').textContent = 'Camera initialization failed: ' + err.message;
    });
});
</script>
