<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-bold mb-0">My Profile & Security Settings</h4>
            <a href="<?= url('/student/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <!-- Student Profile Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge text-primary me-2"></i> Student Identification</h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Full Legal Name</label>
                        <div class="fw-bold fs-6"><?= e($student['full_name']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Matric / Student Code</label>
                        <div class="fw-bold fs-6"><code><?= e($student['student_code']) ?></code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Class Cohort</label>
                        <div><span class="badge bg-light text-dark border"><?= e($student['class_name'] ?? 'Unassigned') ?></span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Account Username</label>
                        <div class="fw-bold fs-6"><?= e($student['username']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Official Email Address</label>
                        <div class="fw-bold fs-6"><?= e($student['email']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Contact Phone</label>
                        <div class="fw-bold fs-6"><?= e($student['phone'] ?: '-') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Biometric Face Enrollment</label>
                        <div>
                            <?php if ($student['is_face_registered']): ?>
                                <span class="badge bg-success-subtle text-success border border-success">
                                    <i class="bi bi-check2-circle me-1"></i> Enrolled (v<?= $student['face_version'] ?>)
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                    <i class="bi bi-exclamation-circle me-1"></i> Not Enrolled
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-key text-warning me-2"></i> Update Account Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('/student/profile/password') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required placeholder="Enter your existing password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="At least 6 characters">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" required minlength="6" placeholder="Re-enter new password">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-shield-check me-1"></i> Save New Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
