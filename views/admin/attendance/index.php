<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Campus Attendance Records</h4>
        <p class="text-muted mb-0">Biometric check-in/out audit logs with precision facial match confidence</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/attendance/export?' . http_build_query($_GET)) ?>" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel/CSV
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= url('/admin/attendance') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Attendance Date</label>
                <input type="date" name="date" class="form-control" value="<?= e($filters['date'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Class Cohort</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['class_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="checked_in" <?= ($filters['status'] === 'checked_in') ? 'selected' : '' ?>>Checked In</option>
                    <option value="checked_out" <?= ($filters['status'] === 'checked_out') ? 'selected' : '' ?>>Checked Out</option>
                    <option value="late" <?= ($filters['status'] === 'late') ? 'selected' : '' ?>>Late</option>
                    <option value="on_leave" <?= ($filters['status'] === 'on_leave') ? 'selected' : '' ?>>On Leave</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Search Student</label>
                <input type="text" name="q" class="form-control" value="<?= e($filters['query'] ?? '') ?>" placeholder="Name or code...">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" style="margin-top: 1.5rem;">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th>Snapshot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                No attendance records found matching criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td class="fw-semibold"><?= format_date($r['attendance_date']) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($r['student_name']) ?></div>
                                    <small class="text-muted"><?= e($r['student_code']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($r['class_name'] ?? 'Unassigned') ?></span></td>
                                <td><?= e($r['check_in_time'] ?: '-') ?></td>
                                <td><?= e($r['check_out_time'] ?: '-') ?></td>
                                <td>
                                    <?php if ($r['check_in_confidence']): ?>
                                        <span class="badge bg-secondary"><?= round($r['check_in_confidence'] * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $badgeColor = match($r['status']) {
                                            'checked_in' => 'bg-success',
                                            'checked_out' => 'bg-info',
                                            'late' => 'bg-warning text-dark',
                                            'on_leave' => 'bg-secondary',
                                            default => 'bg-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $badgeColor ?>"><?= strtoupper(str_replace('_', ' ', $r['status'])) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($r['check_in_photo'])): ?>
                                        <a href="<?= url($r['check_in_photo']) ?>" target="_blank">
                                            <img src="<?= url($r['check_in_photo']) ?>" class="rounded border" style="width: 36px; height: 36px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
