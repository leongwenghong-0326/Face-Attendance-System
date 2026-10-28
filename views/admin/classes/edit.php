<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Edit Class: <?= e($class['class_name']) ?></h4>
            <a href="<?= url('/admin/classes') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= url('/admin/classes/' . $class['id'] . '/edit') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h5 class="fw-bold text-primary mb-3">1. Identification</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="class_name" class="form-control" value="<?= e($class['class_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class Code <span class="text-danger">*</span></label>
                            <input type="text" name="class_code" class="form-control" value="<?= e($class['class_code']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= ($class['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($class['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3">2. Timetable & Grace Period Policy</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" value="<?= e($class['start_time']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Late Grace Period (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="late_grace_minutes" class="form-control" value="<?= (int)$class['late_grace_minutes'] ?>" min="0" max="120" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rest/Break Window Start <span class="text-danger">*</span></label>
                            <input type="time" name="rest_start_time" class="form-control" value="<?= e($class['rest_start_time']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rest/Break Window End <span class="text-danger">*</span></label>
                            <input type="time" name="rest_end_time" class="form-control" value="<?= e($class['rest_end_time']) ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/admin/classes') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
