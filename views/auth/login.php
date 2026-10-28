<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
                <i class="bi bi-person-bounding-box fs-1"></i>
            </div>
            <h3 class="fw-bold text-dark">Apex Academy</h3>
            <p class="text-muted">Facial Recognition Attendance System</p>
        </div>

        <?php if (!empty($flashes)): ?>
            <?php foreach ($flashes as $type => $messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-danger shadow-sm mb-3" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i><?= e($msg) ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= url('/login') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="login" class="form-label fw-semibold">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="login" name="login" required autofocus placeholder="admin / teacher1 / student1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </div>
                </form>

                <hr class="my-4 text-muted">

                <!-- Demo Account Helper Buttons -->
                <div class="text-center">
                    <small class="text-muted d-block mb-2 fw-semibold">Quick Demo Fill</small>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary" onclick="fillDemo('admin', 'password123')">Admin</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="fillDemo('teacher1', 'password123')">Teacher</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="fillDemo('student1', 'password123')">Student</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="<?= url('/kiosk') ?>" class="text-decoration-none text-primary fw-semibold me-3" target="_blank">
                <i class="bi bi-camera-video me-1"></i> Open Attendance Kiosk
            </a>
            <a href="<?= url('/privacy') ?>" class="text-decoration-none text-muted small">Privacy Notice</a>
        </div>
    </div>
</div>

<script>
function fillDemo(username, password) {
    document.getElementById('login').value = username;
    document.getElementById('password').value = password;
}
</script>
