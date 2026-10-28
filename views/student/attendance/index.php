<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">My Attendance & Analytics</h4>
        <p class="text-muted mb-0">Detailed attendance log, monthly trends, and biometric check-in history</p>
    </div>
    <a href="<?= url('/student/face/scan') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-camera me-1"></i> Scan Attendance Now
    </a>
</div>

<!-- Attendance Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center bg-primary text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Attendance Rate</small>
            <h2 class="fw-bold my-1"><?= $stats['attendance_rate'] ?>%</h2>
            <small>Presence reliability</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center bg-success text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Present Days</small>
            <h2 class="fw-bold my-1"><?= $stats['present_days'] ?></h2>
            <small>On time arrival</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center bg-warning text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Late Days</small>
            <h2 class="fw-bold my-1"><?= $stats['late_days'] ?></h2>
            <small>Exceeded grace period</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center bg-secondary text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Approved Leaves</small>
            <h2 class="fw-bold my-1"><?= $stats['leave_days'] ?></h2>
            <small>Medical/official leaves</small>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= url('/student/attendance') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Specific Date</label>
                <input type="date" name="date" class="form-control" value="<?= e($filters['date'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Filter by Month</label>
                <input type="month" name="month" class="form-control" value="<?= e($filters['month'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="checked_in" <?= ($filters['status'] === 'checked_in') ? 'selected' : '' ?>>Checked In</option>
                    <option value="checked_out" <?= ($filters['status'] === 'checked_out') ? 'selected' : '' ?>>Checked Out</option>
                    <option value="late" <?= ($filters['status'] === 'late') ? 'selected' : '' ?>>Late</option>
                    <option value="on_leave" <?= ($filters['status'] === 'on_leave') ? 'selected' : '' ?>>On Leave</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" style="margin-top: 1.5rem;">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check text-primary me-2"></i> Attendance Log History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Class Start Policy</th>
                        <th>Check-In Time</th>
                        <th>Check-Out Time</th>
                        <th>Match Confidence</th>
                        <th>Status</th>
                        <th>Verification Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                No attendance records found matching filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= format_date($r['attendance_date']) ?></td>
                                <td><?= format_time($student['start_time']) ?></td>
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
                                        $badge = match($r['status']) {
                                            'checked_in' => 'bg-success',
                                            'checked_out' => 'bg-info',
                                            'late' => 'bg-warning text-dark',
                                            'on_leave' => 'bg-secondary',
                                            default => 'bg-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= strtoupper(str_replace('_', ' ', $r['status'])) ?></span>
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
