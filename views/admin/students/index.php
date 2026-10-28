<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Student Management</h4>
        <p class="text-muted mb-0">Manage enrolled students, biometric face registration, and class cohorts</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/students/export?' . http_build_query($_GET)) ?>" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </a>
        <a href="<?= url('/admin/students/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add Student
        </a>
    </div>
</div>

<!-- Search and Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= url('/admin/students') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= e($q ?? '') ?>" placeholder="Search by name, student code, email, username...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($selectedClass == $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($selectedStatus === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="<?= url('/admin/students') ?>" class="btn btn-light" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Students Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Contact</th>
                        <th>Biometric Face Status</th>
                        <th>Account</th>
                        <th class="text-end text-nowrap" style="min-width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No student records found matching criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><span class="text-muted fw-mono">#<?= $s['id'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($s['face_preview_image'])): ?>
                                            <img src="<?= url($s['face_preview_image']) ?>" class="rounded-circle me-2 border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle me-2 bg-light text-secondary d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="<?= url('/admin/students/' . $s['id']) ?>" class="fw-bold text-dark text-decoration-none">
                                                <?= e($s['full_name']) ?>
                                            </a>
                                            <div class="small text-muted">Code: <span class="fw-semibold"><?= e($s['student_code']) ?></span></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= e($s['class_name'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small"><?= e($s['email']) ?></div>
                                    <div class="small text-muted"><?= e($s['phone'] ?: '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($s['is_face_registered']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="bi bi-check2-circle me-1"></i> Registered (v<?= $s['face_version'] ?>)
                                        </span>
                                        <a href="<?= url('/admin/students/' . $s['id'] . '/register-face') ?>" class="btn btn-outline-secondary btn-sm py-0 ms-1" title="Re-Register Face">
                                            <i class="bi bi-arrow-repeat"></i> Re-Register
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning">
                                            <i class="bi bi-exclamation-circle me-1"></i> Not Registered
                                        </span>
                                        <a href="<?= url('/admin/students/' . $s['id'] . '/register-face') ?>" class="btn btn-outline-primary btn-sm py-0 ms-1" title="Register Face Now">
                                            <i class="bi bi-camera"></i> Register
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $s['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($s['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('/admin/students/' . $s['id']) ?>" class="btn btn-outline-secondary" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= url('/admin/students/' . $s['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit Student">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= url('/admin/students/' . $s['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete student <?= e($s['full_name']) ?> and all related data?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
