<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-bold mb-0">Enroll New Student</h4>
            <a href="<?= url('/admin/students') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <form action="<?= url('/admin/students/create') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h5 class="fw-bold text-primary mb-3">1. Login & Academic Credentials</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required placeholder="e.g. jdoe2026">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Login Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="At least 6 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Code / Matric No. <span class="text-danger">*</span></label>
                            <input type="text" name="student_code" class="form-control" required placeholder="e.g. STD2026042">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign Class Cohort <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class...</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3">2. Personal Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Official Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="johndoe@apex.edu">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+60 12-345 6789">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/admin/students') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Save & Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
