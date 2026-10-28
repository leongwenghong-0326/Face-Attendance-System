<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Teacher Management</h4>
        <p class="text-muted mb-0">Manage faculty staff profiles, credentials, and subject assignments</p>
    </div>
    <a href="<?= url('/admin/teachers/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add Teacher
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Teacher</th>
                        <th>Code</th>
                        <th>Username</th>
                        <th>Contact Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-end text-nowrap" style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No teacher profiles registered yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teachers as $t): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $t['id'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 me-2">
                                            <i class="bi bi-mortarboard fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= e($t['full_name']) ?></div>
                                            <small class="text-muted text-capitalize"><?= e($t['gender'] ?? 'other') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($t['teacher_code']) ?></span></td>
                                <td><code><?= e($t['username']) ?></code></td>
                                <td><?= e($t['email']) ?></td>
                                <td><?= e($t['phone'] ?: '-') ?></td>
                                <td>
                                    <span class="badge <?= $t['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($t['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('/admin/teachers/' . $t['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit Teacher">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= url('/admin/teachers/' . $t['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete teacher <?= e($t['full_name']) ?>?');">
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
