<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Curriculum & Teaching Allocations</h4>
        <p class="text-muted mb-0">Bind subjects to classes and assign teaching staff to cohorts (duplicate-protected)</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Class-Subject Assignment -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3 text-primary me-2"></i> 1. Assign Subject to Class</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('/admin/assignments/class-subject') ?>" method="POST" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Class Cohort</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class...</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Course Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject...</option>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['subject_name']) ?> (<?= e($s['subject_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Bind Subject to Class
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Class Subjects List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Active Class-Subject Relationships</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Subject</th>
                                <th class="text-end text-nowrap" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classSubjects)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No subjects assigned to classes yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classSubjects as $cs): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= e($cs['class_name']) ?></span>
                                            <small class="text-muted d-block"><?= e($cs['class_code']) ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary"><?= e($cs['subject_name']) ?></span>
                                            <small class="text-muted d-block"><?= e($cs['subject_code']) ?></small>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <form action="<?= url('/admin/assignments/class-subject/' . $cs['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Remove subject <?= e($cs['subject_name']) ?> from <?= e($cs['class_name']) ?>?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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

    <!-- Right Column: Teacher Assignment -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-workspace text-success me-2"></i> 2. Assign Teacher to Class & Subject</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('/admin/assignments/teacher') ?>" method="POST" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Faculty Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Select Teacher...</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= e($t['full_name']) ?> (<?= e($t['teacher_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class...</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['class_name']) ?> (<?= e($c['class_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Assigned Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject...</option>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['subject_name']) ?> (<?= e($s['subject_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Allocate Teaching Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Teacher Assignments List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Active Teacher Allocations</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Teacher</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th class="text-end text-nowrap" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($teacherAssignments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No teacher allocations configured yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teacherAssignments as $ta): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= e($ta['teacher_name']) ?></span>
                                            <small class="text-muted d-block"><?= e($ta['teacher_code']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= e($ta['class_name']) ?></span></td>
                                        <td><span class="badge bg-info-subtle text-info border border-info"><?= e($ta['subject_name']) ?></span></td>
                                        <td class="text-end text-nowrap">
                                            <form action="<?= url('/admin/assignments/teacher/' . $ta['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Remove assignment?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
