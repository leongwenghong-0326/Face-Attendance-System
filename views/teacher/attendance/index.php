<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Class Attendance Records</h4>
        <p class="text-muted mb-0">Review facial attendance logs for your assigned academic cohorts</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= url('/teacher/attendance') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Filter by Date</label>
                <input type="date" name="date" class="form-control" value="<?= e($date) ?>">
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Assigned Class Cohort</label>
                <select name="class_id" class="form-select">
                    <option value="">All My Assigned Classes</option>
                    <?php foreach ($assignedClasses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($selectedClass == $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" style="margin-top: 1.5rem;">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Records Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Student Code</th>
                        <th>Class</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th class="text-end">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                No attendance records found for your cohorts on this date.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td class="fw-semibold"><?= format_date($r['attendance_date']) ?></td>
                                <td>
                                    <a href="<?= url('/teacher/attendance/student/' . $r['student_id']) ?>" class="fw-bold text-dark text-decoration-none">
                                        <?= e($r['student_name']) ?>
                                    </a>
                                </td>
                                <td><code><?= e($r['student_code']) ?></code></td>
                                <td><span class="badge bg-light text-dark border"><?= e($r['class_name']) ?></span></td>
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
                                <td class="text-end">
                                    <a href="<?= url('/teacher/attendance/student/' . $r['student_id']) ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-person-lines-fill me-1"></i> View History
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
