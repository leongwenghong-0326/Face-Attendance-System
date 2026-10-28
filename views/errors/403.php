<div class="row justify-content-center text-center">
    <div class="col-md-6">
        <div class="py-5">
            <div class="display-1 fw-bold text-danger mb-3">403</div>
            <h2 class="fw-bold mb-3">Access Denied / Forbidden</h2>
            <p class="text-muted mb-4"><?= e($message ?? 'You do not have permission to view this resource.') ?></p>
            <a href="<?= url('/') ?>" class="btn btn-primary px-4 shadow-sm">
                <i class="bi bi-house-door me-1"></i> Return to Safety
            </a>
        </div>
    </div>
</div>
