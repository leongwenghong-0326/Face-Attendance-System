<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">System Audit & Activity Logs</h4>
        <p class="text-muted mb-0">Immutable chronological trail of security logins, face registrations, and attendance operations</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Action Description</th>
                        <th>User Account</th>
                        <th>Target Student</th>
                        <th>Confidence</th>
                        <th>IP Address</th>
                        <th>Client Browser / Metadata</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No audit entries recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $log['id'] ?></span></td>
                                <td class="text-nowrap small text-muted"><?= e($log['created_at']) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= e($log['action']) ?></span>
                                    <?php if ($log['registration_version']): ?>
                                        <span class="badge bg-info ms-1">v<?= $log['registration_version'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['username']): ?>
                                        <div class="fw-semibold"><?= e($log['username']) ?></div>
                                        <small class="badge bg-light text-dark border"><?= e($log['user_role'] ?? 'user') ?></small>
                                    <?php else: ?>
                                        <span class="text-muted small">System / Kiosk</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['student_name']): ?>
                                        <div class="fw-semibold text-primary"><?= e($log['student_name']) ?></div>
                                        <small class="text-muted"><?= e($log['student_code']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['confidence_score']): ?>
                                        <span class="badge bg-secondary"><?= round($log['confidence_score'] * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><code><?= e($log['ip_address'] ?? '127.0.0.1') ?></code></td>
                                <td class="small text-muted" style="max-width: 280px;">
                                    <div class="text-truncate" title="<?= e($log['user_agent'] . ' ' . $log['metadata']) ?>">
                                        <?= e($log['metadata'] ?: $log['user_agent']) ?>
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
