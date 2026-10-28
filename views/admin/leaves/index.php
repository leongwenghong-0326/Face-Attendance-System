<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Student Leave Applications</h4>
        <p class="text-muted mb-0">Review submitted medical and personal absence requests with attached supporting documents</p>
    </div>
    <div class="btn-group">
        <a href="<?= url('/admin/leaves') ?>" class="btn btn-outline-secondary <?= empty($selectedStatus) ? 'active' : '' ?>">All</a>
        <a href="<?= url('/admin/leaves?status=pending') ?>" class="btn btn-outline-warning <?= ($selectedStatus === 'pending') ? 'active' : '' ?>">Pending</a>
        <a href="<?= url('/admin/leaves?status=approved') ?>" class="btn btn-outline-success <?= ($selectedStatus === 'approved') ? 'active' : '' ?>">Approved</a>
        <a href="<?= url('/admin/leaves?status=rejected') ?>" class="btn btn-outline-danger <?= ($selectedStatus === 'rejected') ? 'active' : '' ?>">Rejected</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Leave Date</th>
                        <th>Reason</th>
                        <th>Supporting Document</th>
                        <th>Status</th>
                        <th>Reviewer / Notes</th>
                        <th class="text-end text-nowrap" style="min-width: 190px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leaves)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No leave applications found in this category.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leaves as $l): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $l['id'] ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($l['student_name']) ?></div>
                                    <small class="text-muted"><?= e($l['student_code']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($l['class_name'] ?? 'General') ?></span></td>
                                <td class="fw-semibold text-primary"><?= format_date($l['leave_date']) ?></td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="<?= e($l['reason']) ?>"><?= e($l['reason']) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($l['document_path'])): ?>
                                        <a href="<?= url($l['document_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0">
                                            <i class="bi bi-paperclip me-1"></i> View Attachment
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $statusBadge = match($l['status']) {
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-warning text-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $statusBadge ?>"><?= ucfirst($l['status']) ?></span>
                                </td>
                                <td class="small">
                                    <?php if ($l['status'] !== 'pending'): ?>
                                        <div><strong>By:</strong> <?= e($l['reviewer_username'] ?? 'Admin') ?></div>
                                        <div class="text-muted"><?= e($l['review_note'] ?: '-') ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">Pending decision</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if ($l['status'] === 'pending'): ?>
                                        <div class="d-inline-flex gap-2 align-items-center">
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#decisionModal<?= $l['id'] ?>" onclick="setModalMode(<?= $l['id'] ?>, 'approve')">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#decisionModal<?= $l['id'] ?>" onclick="setModalMode(<?= $l['id'] ?>, 'reject')">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>

                                        <!-- Decision Modal -->
                                        <div class="modal fade" id="decisionModal<?= $l['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form id="decisionForm<?= $l['id'] ?>" method="POST" action="">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold" id="modalTitle<?= $l['id'] ?>">Review Leave Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p class="mb-2"><strong>Student:</strong> <?= e($l['student_name']) ?> (<?= e($l['student_code']) ?>)</p>
                                                            <p class="mb-3"><strong>Date:</strong> <?= format_date($l['leave_date']) ?></p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Reviewer Notes (Optional)</label>
                                                                <textarea name="review_note" class="form-control" rows="3" placeholder="Approval or rejection notes..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" id="modalSubmitBtn<?= $l['id'] ?>" class="btn btn-primary">Confirm</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Decided</span>
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

<script>
function setModalMode(id, action) {
    const form = document.getElementById('decisionForm' + id);
    const title = document.getElementById('modalTitle' + id);
    const btn = document.getElementById('modalSubmitBtn' + id);

    if (action === 'approve') {
        form.action = '<?= url('/admin/leaves/') ?>' + id + '/approve';
        title.textContent = 'Approve Leave Application';
        btn.className = 'btn btn-success';
        btn.textContent = 'Approve Application';
    } else {
        form.action = '<?= url('/admin/leaves/') ?>' + id + '/reject';
        title.textContent = 'Reject Leave Application';
        btn.className = 'btn btn-danger';
        btn.textContent = 'Reject Application';
    }
}
</script>
