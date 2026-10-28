<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Face Recognition Quality & System Settings</h4>
                <p class="text-muted mb-0">Fine-tune computer vision thresholds, stability filters, and institutional metadata</p>
            </div>
        </div>

        <form action="<?= url('/admin/settings') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Computer Vision Biometric Tuning Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-eye-fill me-2"></i> 1. Face Quality & Match Threshold Parameters
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Face Size (pixels)</label>
                            <input type="number" name="min_face_size" class="form-control" value="<?= e($settings['min_face_size'] ?? 80) ?>" min="40" max="300" required>
                            <small class="text-muted">Rejects faces located too far from the camera lens.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Face Size (pixels)</label>
                            <input type="number" name="max_face_size" class="form-control" value="<?= e($settings['max_face_size'] ?? 440) ?>" min="200" max="800" required>
                            <small class="text-muted">Rejects faces positioned too close to the camera lens.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Brightness (0 - 255)</label>
                            <input type="number" name="min_brightness" class="form-control" value="<?= e($settings['min_brightness'] ?? 40) ?>" min="10" max="120" required>
                            <small class="text-muted">Detects low-light or unlit environments.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Brightness (0 - 255)</label>
                            <input type="number" name="max_brightness" class="form-control" value="<?= e($settings['max_brightness'] ?? 225) ?>" min="180" max="255" required>
                            <small class="text-muted">Prevents blinding glare or severe backlighting overexposure.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Image Sharpness Score</label>
                            <input type="number" name="min_sharpness" class="form-control" value="<?= e($settings['min_sharpness'] ?? 20) ?>" min="5" max="80" required>
                            <small class="text-muted">Rejects motion-blurred or unfocused camera frames.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Detection Confidence Threshold (0.1 - 1.0)</label>
                            <input type="number" step="0.05" name="detection_confidence" class="form-control" value="<?= e($settings['detection_confidence'] ?? 0.50) ?>" min="0.1" max="0.9" required>
                            <small class="text-muted">TinyFaceDetector minimum confidence score.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Face Match Threshold (Distance, 0.3 - 0.8)</label>
                            <input type="number" step="0.01" name="match_threshold" class="form-control" value="<?= e($settings['match_threshold'] ?? 0.55) ?>" min="0.30" max="0.80" required>
                            <small class="text-muted">Maximum Euclidean distance for positive match (lower is stricter).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Attendance Confidence (0.1 - 1.0)</label>
                            <input type="number" step="0.05" name="min_confidence" class="form-control" value="<?= e($settings['min_confidence'] ?? 0.45) ?>" min="0.1" max="0.95" required>
                            <small class="text-muted">Minimum confidence percentage required to commit attendance.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Consecutive Stable Frames</label>
                            <input type="number" name="stable_frames" class="form-control" value="<?= e($settings['stable_frames'] ?? 3) ?>" min="1" max="10" required>
                            <small class="text-muted">Required stable frames before snap.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duplicate Scan Interval (Seconds)</label>
                            <input type="number" name="duplicate_scan_interval" class="form-control" value="<?= e($settings['duplicate_scan_interval'] ?? 300) ?>" min="10" max="3600" required>
                            <small class="text-muted">Default 300s (5 minutes) anti-duplicate window.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Registration Samples Count</label>
                            <input type="number" name="registration_samples" class="form-control" value="<?= e($settings['registration_samples'] ?? 3) ?>" min="1" max="5" required>
                            <small class="text-muted">Default 3 front-facing samples.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Institution Settings Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-building me-2 text-secondary"></i> 2. Institution & Regional Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Institution Name</label>
                            <input type="text" name="institution_name" class="form-control" value="<?= e($settings['institution_name'] ?? 'Apex Academy of Technology') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">System Timezone</label>
                            <input type="text" name="system_timezone" class="form-control" value="<?= e($settings['system_timezone'] ?? 'Asia/Kuala_Lumpur') ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Save Configuration
                </button>
            </div>
        </form>
    </div>
</div>
