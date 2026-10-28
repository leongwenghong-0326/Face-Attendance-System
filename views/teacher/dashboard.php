<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Teacher Portal: Welcome, <?= e($teacher['full_name']) ?></h4>
        <p class="text-muted mb-0">Teacher Code: <?= e($teacher['teacher_code']) ?> &bull; <?= date('l, d F Y') ?></p>
    </div>
    <a href="<?= url('/teacher/attendance') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-calendar-check me-1"></i> Class Attendance
    </a>
</div>

<!-- Faculty Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card border-primary p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Assigned Classes</span>
                    <h3 class="fw-bold my-1 text-dark"><?= count($assignedClasses) ?></h3>
                    <small class="text-muted">Cohorts under your care</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-success p-3" style="border-left-color: #10b981 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Active Students</span>
                    <h3 class="fw-bold my-1 text-dark"><?= $studentsCount ?></h3>
                    <small class="text-success">Across all assigned classes</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-info p-3" style="border-left-color: #0ea5e9 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Taught Subjects</span>
                    <h3 class="fw-bold my-1 text-dark"><?= count($assignedSubjects) ?></h3>
                    <small class="text-info">Curriculum syllabus</small>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Assigned Classes & Subjects Overview -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3 text-primary me-2"></i> My Assigned Classes</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($assignedClasses)): ?>
                        <li class="list-group-item text-muted text-center py-4">No cohorts assigned to you yet.</li>
                    <?php else: ?>
                        <?php foreach ($assignedClasses as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark"><?= e($c['class_name']) ?></h6>
                                    <small class="text-muted">Code: <?= e($c['class_code']) ?> &bull; Start: <?= format_time($c['start_time']) ?></small>
                                </div>
                                <a href="<?= url('/teacher/attendance?class_id=' . $c['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    Roll Call
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-bookmark text-info me-2"></i> Responsible Subjects</h5>
            </div>
            <div class="card-body p-3">
                <?php if (empty($assignedSubjects)): ?>
                    <p class="text-muted text-center mb-0">No subject modules assigned.</p>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($assignedSubjects as $s): ?>
                            <span class="badge bg-light text-dark border p-2">
                                <i class="bi bi-book me-1 text-primary"></i> <?= e($s['subject_name']) ?> (<?= e($s['subject_code']) ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Today's Attendance in Assigned Cohorts -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar2-check text-success me-2"></i> Today's Cohort Attendance</h5>
                <a href="<?= url('/teacher/attendance') ?>" class="btn btn-outline-secondary btn-sm">Full History</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>In Time</th>
                                <th>Confidence</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($todayAttendance)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No students checked in today for your assigned cohorts.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($todayAttendance as $rec): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('/teacher/attendance/student/' . $rec['student_id']) ?>" class="fw-bold text-dark text-decoration-none">
                                                <?= e($rec['student_name']) ?>
                                            </a>
                                            <div class="small text-muted"><?= e($rec['student_code']) ?></div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= e($rec['class_name']) ?></span></td>
                                        <td><?= e($rec['check_in_time'] ?: '-') ?></td>
                                        <td>
                                            <?php if ($rec['check_in_confidence']): ?>
                                                <span class="badge bg-secondary"><?= round($rec['check_in_confidence'] * 100, 1) ?>%</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $badge = match($rec['status']) {
                                                    'checked_in' => 'bg-success',
                                                    'checked_out' => 'bg-info',
                                                    'late' => 'bg-warning text-dark',
                                                    'on_leave' => 'bg-secondary',
                                                    default => 'bg-dark'
                                                };
                                            ?>
                                            <span class="badge <?= $badge ?>"><?= strtoupper(str_replace('_', ' ', $rec['status'])) ?></span>
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
</div>
