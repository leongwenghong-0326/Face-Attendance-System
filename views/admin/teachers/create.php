<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Add New Faculty Teacher</h4>
            <a href="<?= url('/admin/teachers') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= url('/admin/teachers/create') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h5 class="fw-bold text-primary mb-3">1. Login Account</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required placeholder="e.g. prof_alice">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="At least 6 characters">
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3">2. Faculty Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teacher Code <span class="text-danger">*</span></label>
                            <input type="text" name="teacher_code" class="form-control" required placeholder="e.g. TCH-010">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Dr. Alice Johnson">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="alice@apex.edu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+60 12-345 6789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/admin/teachers') ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Save Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
