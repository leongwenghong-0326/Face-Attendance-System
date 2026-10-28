<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Student Leave Applications</h4>
        <p class="text-muted mb-0">Submit medical or official excuses with documentary proof for admin approval</p>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Application Submission Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-plus me-2"></i> File New Leave Request</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('/student/leaves') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Leave Date <span class="text-danger">*</span></label>
                        <input type="date" name="leave_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        <small class="text-muted">Approved leaves will automatically prevent normal roll-call penalties.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Absence <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="State your reason (medical leave, family emergency, official event, etc.)..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Supporting Document (Optional)</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: <strong>PDF, JPG, JPEG, PNG</strong> (Max 5MB).</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-send me-1"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Submitted Leave Requests Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-secondary me-2"></i> My Submitted Leave History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Document</th>
                                <th>Status</th>
                                <th>Reviewer Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaves)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No leave requests submitted yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leaves as $l): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= format_date($l['leave_date']) ?></td>
                                        <td style="max-width: 200px;">
                                            <div class="text-truncate" title="<?= e($l['reason']) ?>"><?= e($l['reason']) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($l['document_path'])): ?>
                                                <a href="<?= url($l['document_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0">
                                                    <i class="bi bi-paperclip"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $badge = match($l['status']) {
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-warning text-dark'
                                                };
                                            ?>
                                            <span class="badge <?= $badge ?>"><?= ucfirst($l['status']) ?></span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= e($l['review_note'] ?: '-') ?>
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
