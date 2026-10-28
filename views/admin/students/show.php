<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Student Profile: <?= e($student['full_name']) ?></h4>
        <p class="text-muted mb-0">Biometric identity record, academic schedule, and cumulative attendance analytics</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/students/' . $student['id'] . '/register-face') ?>" class="btn btn-warning shadow-sm">
            <i class="bi bi-camera me-1"></i> <?= $student['is_face_registered'] ? 'Re-Register Face' : 'Register Face' ?>
        </a>
        <a href="<?= url('/admin/students/' . $student['id'] . '/edit') ?>" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
        <a href="<?= url('/admin/students') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Biometric & Personal Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            <div class="mb-3 position-relative d-inline-block mx-auto">
                <?php if (!empty($student['face_preview_image'])): ?>
                    <img src="<?= url($student['face_preview_image']) ?>" class="rounded-circle border border-3 border-primary shadow" style="width: 140px; height: 140px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-light border border-3 border-secondary d-flex align-items-center justify-content-center mx-auto text-secondary shadow-sm" style="width: 140px; height: 140px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                <?php endif; ?>
                <span class="position-absolute bottom-0 end-0 badge rounded-pill <?= $student['is_face_registered'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= $student['is_face_registered'] ? 'v' . $student['face_version'] : 'No Face' ?>
                </span>
            </div>

            <h4 class="fw-bold mb-1 text-dark"><?= e($student['full_name']) ?></h4>
            <div class="text-muted mb-2">Code: <span class="fw-semibold"><?= e($student['student_code']) ?></span></div>
            <div class="mb-3">
                <span class="badge bg-light text-dark border"><?= e($student['class_name'] ?? 'Unassigned Class') ?></span>
                <span class="badge <?= $student['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($student['status']) ?></span>
            </div>

            <hr class="my-3">

            <div class="text-start small">
                <div class="mb-2"><strong class="text-secondary">Username:</strong> <span class="float-end"><?= e($student['username']) ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Email:</strong> <span class="float-end"><?= e($student['email']) ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Phone:</strong> <span class="float-end"><?= e($student['phone'] ?: '-') ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Gender:</strong> <span class="float-end text-capitalize"><?= e($student['gender'] ?: '-') ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Date of Birth:</strong> <span class="float-end"><?= format_date($student['date_of_birth']) ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Active Face Samples:</strong> <span class="float-end badge bg-info"><?= count($descriptors) ?> samples</span></div>
            </div>
        </div>

        <!-- Class Timetable Parameters Card -->
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-primary me-2"></i> Cohort Timetable Policy</h6>
            <div class="small">
                <div class="mb-2"><span class="text-muted">Class Start Time:</span> <strong class="float-end"><?= format_time($student['start_time']) ?></strong></div>
                <div class="mb-2"><span class="text-muted">Late Grace Period:</span> <strong class="float-end"><?= (int)($student['late_grace_minutes'] ?? 15) ?> mins</strong></div>
                <div class="mb-2"><span class="text-muted">Rest/Break Window:</span> <strong class="float-end"><?= format_time($student['rest_start_time']) ?> - <?= format_time($student['rest_end_time']) ?></strong></div>
            </div>
        </div>
    </div>

    <!-- Right Column: Attendance Analytics and Log History -->
    <div class="col-lg-8">
        <!-- Statistics Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-primary text-white text-center">
                    <small class="text-white-50 text-uppercase fw-semibold">Attendance Rate</small>
                    <h2 class="fw-bold my-1"><?= $stats['attendance_rate'] ?>%</h2>
                    <small>Presence reliability</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-success text-white text-center">
                    <small class="text-white-50 text-uppercase fw-semibold">Present Days</small>
                    <h2 class="fw-bold my-1"><?= $stats['present_days'] ?></h2>
                    <small>On-time arrivals</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-warning text-white text-center">
                    <small class="text-white-50 text-uppercase fw-semibold">Late Days</small>
                    <h2 class="fw-bold my-1"><?= $stats['late_days'] ?></h2>
                    <small>Beyond grace period</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-secondary text-white text-center">
                    <small class="text-white-50 text-uppercase fw-semibold">Leave Days</small>
                    <h2 class="fw-bold my-1"><?= $stats['leave_days'] ?></h2>
                    <small>Approved absence</small>
                </div>
            </div>
        </div>

        <!-- Attendance History Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-calendar2-check text-primary me-2"></i> Attendance Log History</h5>
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
                                <th>Verification Photo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceHistory)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No attendance activity recorded for this student yet.</td>
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
                                                    <img src="<?= url($att['check_in_photo']) ?>" class="rounded border" style="width: 38px; height: 38px; object-fit: cover;">
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
    </div>
</div>
