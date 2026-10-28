<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Class Cohort Management</h4>
        <p class="text-muted mb-0">Configure student cohorts, schedule start times, late grace periods, and break intervals</p>
    </div>
    <a href="<?= url('/admin/classes/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Class
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Class Code</th>
                        <th>Class Name</th>
                        <th>Start Time</th>
                        <th>Late Grace</th>
                        <th>Rest/Break Window</th>
                        <th>Students</th>
                        <th>Subjects</th>
                        <th>Status</th>
                        <th class="text-end text-nowrap" style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No classes created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $c): ?>
                            <tr>
                                <td><code><?= e($c['class_code']) ?></code></td>
                                <td class="fw-bold text-dark"><?= e($c['class_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= format_time($c['start_time']) ?></span></td>
                                <td><span class="badge bg-warning-subtle text-warning border border-warning"><?= (int)$c['late_grace_minutes'] ?> mins</span></td>
                                <td><span class="badge bg-light text-secondary border"><?= format_time($c['rest_start_time']) ?> - <?= format_time($c['rest_end_time']) ?></span></td>
                                <td><span class="badge bg-primary"><?= (int)($c['student_count'] ?? 0) ?> students</span></td>
                                <td><span class="badge bg-info text-dark"><?= (int)($c['subject_count'] ?? 0) ?> subjects</span></td>
                                <td>
                                    <span class="badge <?= $c['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($c['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('/admin/classes/' . $c['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit Class">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= url('/admin/classes/' . $c['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete class <?= e($c['class_name']) ?>?');">
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
