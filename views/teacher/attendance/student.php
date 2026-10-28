<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Student Attendance Dossier: <?= e($student['full_name']) ?></h4>
        <p class="text-muted mb-0">Class: <?= e($student['class_name']) ?> &bull; Student Code: <?= e($student['student_code']) ?></p>
    </div>
    <a href="<?= url('/teacher/attendance') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Class Roll
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-primary text-white text-center">
            <small class="text-white-50 text-uppercase fw-semibold">Attendance Rate</small>
            <h2 class="fw-bold my-1"><?= $stats['attendance_rate'] ?>%</h2>
            <small>Presence rate</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-success text-white text-center">
            <small class="text-white-50 text-uppercase fw-semibold">Present Days</small>
            <h2 class="fw-bold my-1"><?= $stats['present_days'] ?></h2>
            <small>On time</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-warning text-white text-center">
            <small class="text-white-50 text-uppercase fw-semibold">Late Days</small>
            <h2 class="fw-bold my-1"><?= $stats['late_days'] ?></h2>
            <small>After grace cutoff</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-secondary text-white text-center">
            <small class="text-white-50 text-uppercase fw-semibold">Approved Leaves</small>
            <h2 class="fw-bold my-1"><?= $stats['leave_days'] ?></h2>
            <small>Excused absence</small>
        </div>
    </div>
</div>

<!-- Attendance History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i> Attendance History Log</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th>Snapshot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendanceHistory)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No attendance activity recorded for this student.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendanceHistory as $att): ?>
                            <tr>
                                <td class="fw-semibold"><?= format_date($att['attendance_date']) ?></td>
                                <td><?= e($att['check_in_time'] ?: '-') ?></td>
                                <td><?= e($att['check_out_time'] ?: '-') ?></td>
                                <td>
                                    <?php if ($att['check_in_confidence']): ?>
                                        <span class="badge bg-secondary"><?= round($att['check_in_confidence'] * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $badgeColor = match($att['status']) {
                                            'checked_in' => 'bg-success',
                                            'checked_out' => 'bg-info',
                                            'late' => 'bg-warning text-dark',
                                            'on_leave' => 'bg-secondary',
                                            default => 'bg-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $badgeColor ?>"><?= strtoupper(str_replace('_', ' ', $att['status'])) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($att['check_in_photo'])): ?>
                                        <a href="<?= url($att['check_in_photo']) ?>" target="_blank">
                                            <img src="<?= url($att['check_in_photo']) ?>" class="rounded border" style="width: 36px; height: 36px; object-fit: cover;">
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
