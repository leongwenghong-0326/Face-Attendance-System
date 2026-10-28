<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0"><?= $student['is_face_registered'] ? 'Re-Register' : 'Register' ?> Biometric Face Profile</h4>
                <p class="text-muted small mb-0">Follow on-screen instructions to enroll your facial template</p>
            </div>
            <a href="<?= url('/student/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg me-1"></i> Cancel
            </a>
        </div>

        <div class="card border-0 shadow-sm text-center p-3 p-md-4">
            <!-- Dynamic Status Alert -->
            <div id="reg-status" class="alert alert-info py-2 mb-3 fw-semibold shadow-sm">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Starting camera and local neural models...
            </div>

            <!-- Progress Bar -->
            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-semibold text-muted mb-1">
                    <span>Biometric Capture Progress</span>
                    <span id="sample-count">0 / 3</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div id="reg-progress" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%">0%</div>
                </div>
            </div>

            <!-- Camera Viewport -->
            <div class="camera-container mx-auto mb-3">
                <video id="reg-video" class="camera-video" autoplay playsinline muted></video>
                <canvas id="reg-canvas" class="camera-canvas" width="640" height="480"></canvas>
                <div class="scan-radar"></div>
            </div>

            <p class="text-muted small mb-3">
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Please face the camera directly in a well-lit environment. Maintain a steady posture until all 3 samples are successfully captured.
            </p>

            <div>
                <button id="retry-btn" class="btn btn-outline-danger btn-sm" style="display: none;" onclick="location.reload();">
                    <i class="bi bi-arrow-clockwise me-1"></i> Retry Capture
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Face API and Modular Scripts -->
<script src="<?= asset('vendor/face-api.min.js') ?>"></script>
<script src="<?= asset('js/face/camera.js') ?>"></script>
<script src="<?= asset('js/face/face-engine.js') ?>"></script>
<script src="<?= asset('js/face/quality.js') ?>"></script>
<script src="<?= asset('js/face/pose.js') ?>"></script>
<script src="<?= asset('js/face/registration-flow.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const flow = new FaceRegistrationFlow({
        studentId: <?= (int)$student['id'] ?>,
        videoElement: document.getElementById('reg-video'),
        canvasElement: document.getElementById('reg-canvas'),
        statusElement: document.getElementById('reg-status'),
        progressBar: document.getElementById('reg-progress'),
        sampleCountElement: document.getElementById('sample-count'),
        samples: <?= (int)($settings['registration_samples'] ?? 3) ?>,
        modelUrl: '<?= asset('models') ?>',
        apiRegisterUrl: '<?= url('/api/face/register') ?>',
        redirectUrl: '<?= url('/student/dashboard') ?>',
        settings: <?= json_encode($settings) ?>
    });

    flow.init().catch(err => {
        console.error('Registration error:', err);
        document.getElementById('reg-status').className = 'alert alert-danger py-2 text-center';
        document.getElementById('reg-status').textContent = 'Camera or neural network error: ' + err.message;
    });
});
</script>
