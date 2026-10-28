<!-- Student Hero Header -->
<div class="card border-0 shadow-sm bg-primary text-white p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($student['face_preview_image'])): ?>
                <img src="<?= url($student['face_preview_image']) ?>" class="rounded-circle border border-3 border-white shadow" style="width: 72px; height: 72px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-circle bg-white text-primary p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                    <i class="bi bi-person"></i>
                </div>
            <?php endif; ?>
            <div>
                <h3 class="fw-bold mb-1">Welcome, <?= e($student['full_name']) ?>!</h3>
                <div>
                    <span class="badge bg-white text-primary fw-semibold me-2"><?= e($student['class_name'] ?? 'General') ?></span>
                    <span class="text-white-50">Matric: <?= e($student['student_code']) ?></span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('/student/face/scan') ?>" class="btn btn-light fw-bold shadow-sm">
                <i class="bi bi-camera-fill me-1 text-primary"></i> Scan Attendance Now
            </a>
            <a href="<?= url('/student/leaves') ?>" class="btn btn-outline-light">
                <i class="bi bi-file-earmark-plus me-1"></i> Apply Leave
            </a>
        </div>
    </div>
</div>

<!-- Today Attendance Status Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold fs-6"><i class="bi bi-clock-history text-primary me-2"></i> Today's Attendance Snapshot (<?= date('d M Y') ?>)</span>
        <?php if ($todayRecord): ?>
            <?php 
                $badge = match($todayRecord['status']) {
                    'checked_in' => 'bg-success',
                    'checked_out' => 'bg-info',
                    'late' => 'bg-warning text-dark',
                    'on_leave' => 'bg-secondary',
                    default => 'bg-dark'
                };
            ?>
            <span class="badge <?= $badge ?> px-3 py-2"><?= strtoupper(str_replace('_', ' ', $todayRecord['status'])) ?></span>
        <?php else: ?>
            <span class="badge bg-danger-subtle text-danger px-3 py-2 border border-danger">NOT CHECKED IN YET</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <div class="row text-center g-4">
            <div class="col-md-3 border-end">
                <small class="text-muted text-uppercase fw-semibold">Check-In Time</small>
                <h4 class="fw-bold my-2 text-dark"><?= e($todayRecord['check_in_time'] ?? '--:--:--') ?></h4>
                <small class="text-muted">Start policy: <?= format_time($student['start_time']) ?></small>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted text-uppercase fw-semibold">Check-Out Time</small>
                <h4 class="fw-bold my-2 text-dark"><?= e($todayRecord['check_out_time'] ?? '--:--:--') ?></h4>
                <small class="text-muted"><?= $todayRecord && $todayRecord['check_out_time'] ? 'Departure recorded' : 'Awaiting checkout' ?></small>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted text-uppercase fw-semibold">Facial Confidence</small>
                <h4 class="fw-bold my-2 text-primary">
                    <?= $todayRecord && $todayRecord['check_in_confidence'] ? round($todayRecord['check_in_confidence'] * 100, 1) . '%' : '-' ?>
                </h4>
                <small class="text-muted">Biometric match</small>
            </div>
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-semibold">Break/Rest Period</small>
                <h4 class="fw-bold my-2 text-dark">
                    <?= $todayRest ? e($todayRest['rest_in_time']) : '-' ?>
                </h4>
                <small class="text-muted"><?= format_time($student['rest_start_time']) ?> - <?= format_time($student['rest_end_time']) ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Stats Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted text-uppercase fw-semibold">My Attendance Rate</small>
            <h2 class="fw-bold my-1 text-primary"><?= $stats['attendance_rate'] ?>%</h2>
            <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-primary" style="width: <?= $stats['attendance_rate'] ?>%"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted text-uppercase fw-semibold">Present Days</small>
            <h2 class="fw-bold my-1 text-success"><?= $stats['present_days'] ?></h2>
            <small class="text-muted">On time attendance</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted text-uppercase fw-semibold">Late Days</small>
            <h2 class="fw-bold my-1 text-warning"><?= $stats['late_days'] ?></h2>
            <small class="text-muted">After grace period</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted text-uppercase fw-semibold">Approved Leaves</small>
            <h2 class="fw-bold my-1 text-secondary"><?= $stats['leave_days'] ?></h2>
            <small class="text-muted">Authorized absence</small>
        </div>
    </div>
</div>

<!-- Two Columns: Recent Attendance and Recent Leaves -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-calendar2-week text-primary me-2"></i> Recent Attendance Logs</span>
                <a href="<?= url('/student/attendance') ?>" class="btn btn-outline-primary btn-sm">View Full History</a>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentRecords)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No attendance recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentRecords as $r): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= format_date($r['attendance_date']) ?></td>
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Biometric Status & Leaves -->
    <div class="col-lg-4">
        <!-- Biometric Registration Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold"><i class="bi bi-person-bounding-box text-info me-2"></i> Face Registration Status</span>
            </div>
            <div class="card-body text-center p-4">
                <?php if ($student['is_face_registered']): ?>
                    <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">Face Registered</h5>
                    <p class="text-muted small mb-3">Biometric profile active &bull; Version <?= $student['face_version'] ?></p>
                    <a href="<?= url('/student/face/register') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Re-Register Face
                    </a>
                <?php else: ?>
                    <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-warning mb-1">Not Registered</h5>
                    <p class="text-muted small mb-3">Please enroll your face to enable automated campus attendance verification.</p>
                    <a href="<?= url('/student/face/register') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-camera me-1"></i> Register Face Now
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Leave Applications -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-file-earmark-medical text-secondary me-2"></i> My Leave Requests</span>
                <a href="<?= url('/student/leaves') ?>" class="btn btn-outline-secondary btn-sm">View All</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($recentLeaves)): ?>
                    <p class="text-muted small text-center mb-0 py-3">No submitted leave requests.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentLeaves as $l): ?>
                            <li class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold text-dark"><?= format_date($l['leave_date']) ?></div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 180px;"><?= e($l['reason']) ?></small>
                                    </div>
                                    <span class="badge <?= $l['status'] === 'approved' ? 'bg-success' : ($l['status'] === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                        <?= ucfirst($l['status']) ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
