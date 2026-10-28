<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Subject Curriculum Management</h4>
        <p class="text-muted mb-0">Manage course subjects, academic modules, and syllabus descriptions</p>
    </div>
    <a href="<?= url('/admin/subjects/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Subject
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Subject Title</th>
                        <th>Description</th>
                        <th>Assigned Classes</th>
                        <th>Status</th>
                        <th class="text-end text-nowrap" style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No course subjects created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects as $s): ?>
                            <tr>
                                <td><code><?= e($s['subject_code']) ?></code></td>
                                <td class="fw-bold text-dark"><?= e($s['subject_name']) ?></td>
                                <td class="text-muted small" style="max-width: 320px;"><?= e($s['description'] ?: '-') ?></td>
                                <td><span class="badge bg-light text-dark border"><?= (int)($s['class_count'] ?? 0) ?> cohorts</span></td>
                                <td>
                                    <span class="badge <?= $s['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($s['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('/admin/subjects/' . $s['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit Subject">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= url('/admin/subjects/' . $s['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete subject <?= e($s['subject_name']) ?>?');">
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
