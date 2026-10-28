<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-bold mb-0">Edit Student: <?= e($student['full_name']) ?></h4>
            <a href="<?= url('/admin/students/' . $student['id']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <form action="<?= url('/admin/students/' . $student['id'] . '/edit') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h5 class="fw-bold text-primary mb-3">1. Academic & Identification</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Code <span class="text-danger">*</span></label>
                            <input type="text" name="student_code" class="form-control" value="<?= e($student['student_code']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class Cohort</label>
                            <select name="class_id" class="form-select">
                                <option value="">Unassigned</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($student['class_id'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reset Password (leave blank to keep unchanged)</label>
                            <input type="password" name="password" class="form-control" placeholder="New password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= ($student['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($student['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3">2. Personal Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="<?= e($student['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Official Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= e($student['email']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="male" <?= ($student['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($student['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($student['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="<?= e($student['date_of_birth']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($student['phone']) ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/admin/students/' . $student['id']) ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
