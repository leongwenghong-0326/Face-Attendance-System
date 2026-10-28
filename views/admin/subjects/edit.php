<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Edit Subject: <?= e($subject['subject_name']) ?></h4>
            <a href="<?= url('/admin/subjects') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= url('/admin/subjects/' . $subject['id'] . '/edit') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="subject_code" class="form-control" value="<?= e($subject['subject_code']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="subject_name" class="form-control" value="<?= e($subject['subject_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" <?= ($subject['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($subject['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= e($subject['description']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/admin/subjects') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
