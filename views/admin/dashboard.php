<!-- Dashboard Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Institutional Overview</h4>
        <p class="text-muted mb-0">Daily biometric attendance tracking & campus health for <?= date('l, d F Y') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/kiosk') ?>" class="btn btn-warning shadow-sm fw-semibold" target="_blank">
            <i class="bi bi-camera-video me-1"></i> Open Attendance Kiosk
        </a>
        <a href="<?= url('/admin/students/create') ?>" class="btn btn-primary shadow-sm fw-semibold">
            <i class="bi bi-person-plus me-1"></i> Enroll Student
        </a>
    </div>
</div>

<!-- Primary Metric Cards Row -->
<div class="row g-3 mb-4">
    <!-- Total & Active Students -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-primary p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Students</span>
                    <h3 class="fw-bold my-1 text-dark"><?= $totalStudents ?></h3>
                    <small class="text-success fw-semibold"><i class="bi bi-check-circle"></i> <?= $activeStudents ?> Active enrolled</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Face Registered Students -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-info p-3" style="border-left-color: #0ea5e9 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Face Registered</span>
                    <h3 class="fw-bold my-1 text-dark"><?= $faceRegistered ?> / <?= $totalStudents ?></h3>
                    <?php $pct = $totalStudents > 0 ? round(($faceRegistered / $totalStudents) * 100) : 0; ?>
                    <small class="text-info fw-semibold"><?= $pct ?>% biometric coverage</small>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-person-bounding-box"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Teachers -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-success p-3" style="border-left-color: #10b981 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Faculty</span>
                    <h3 class="fw-bold my-1 text-dark"><?= $totalTeachers ?></h3>
                    <small class="text-muted"><?= $totalClasses ?> active classes</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-warning p-3" style="border-left-color: #f59e0b !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pending Leaves</span>
                    <h3 class="fw-bold my-1 text-dark"><?= $pendingLeaves ?></h3>
                    <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> Needs administrative review</small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-file-earmark-medical-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today Attendance Statistics Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3 border-0 shadow-sm bg-primary text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Today's Attendance</small>
            <h2 class="fw-bold my-1"><?= $todayAttendance['total_today'] ?></h2>
            <small>Total scanned students today</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border-0 shadow-sm bg-success text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Checked In</small>
            <h2 class="fw-bold my-1"><?= $todayAttendance['checked_in'] ?></h2>
            <small>Currently on campus</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border-0 shadow-sm bg-info text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Checked Out</small>
            <h2 class="fw-bold my-1"><?= $todayAttendance['checked_out'] ?></h2>
            <small>Departed campus</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border-0 shadow-sm bg-danger text-white">
            <small class="text-white-50 text-uppercase fw-semibold">Late Students</small>
            <h2 class="fw-bold my-1"><?= $todayAttendance['late_students'] ?></h2>
            <small>Beyond class grace period</small>
        </div>
    </div>
</div>

<!-- Tables: Today Live Attendance & Pending Leaves -->
<div class="row g-4">
    <!-- Today Attendance -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold"><i class="bi bi-calendar2-check text-primary me-2"></i> Today's Live Attendance Feed</span>
                <a href="<?= url('/admin/attendance') ?>" class="btn btn-outline-primary btn-sm">View All Records</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Confidence</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentAttendance)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No attendance activity recorded yet today.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentAttendance as $rec): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= e($rec['student_name']) ?></div>
                                            <small class="text-muted"><?= e($rec['student_code']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= e($rec['class_name'] ?? 'General') ?></span></td>
                                        <td><?= e($rec['check_in_time'] ?: '-') ?></td>
                                        <td><?= e($rec['check_out_time'] ?: '-') ?></td>
                                        <td>
                                            <?php if ($rec['check_in_confidence']): ?>
                                                <span class="badge bg-secondary"><?= round($rec['check_in_confidence'] * 100, 1) ?>%</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $badgeClass = match($rec['status']) {
                                                    'checked_in' => 'bg-success',
                                                    'checked_out' => 'bg-info',
                                                    'late' => 'bg-warning text-dark',
                                                    'on_leave' => 'bg-secondary',
                                                    default => 'bg-dark'
                                                };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= strtoupper(str_replace('_', ' ', $rec['status'])) ?></span>
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

    <!-- Pending Leave Requests -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold"><i class="bi bi-file-earmark-medical text-warning me-2"></i> Pending Leave Reviews</span>
                <a href="<?= url('/admin/leaves') ?>" class="btn btn-outline-warning btn-sm text-dark">Manage</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($recentLeaves)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check2-all text-success fs-1 d-block mb-2"></i>
                        No pending leave requests!
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentLeaves as $leave): ?>
                            <li class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark"><?= e($leave['student_name']) ?></div>
                                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?= format_date($leave['leave_date']) ?></small>
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 220px;"><?= e($leave['reason']) ?></p>
                                    </div>
                                    <a href="<?= url('/admin/leaves') ?>" class="btn btn-sm btn-outline-primary">Review</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
