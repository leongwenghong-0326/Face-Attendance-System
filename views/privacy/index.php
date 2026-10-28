<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Biometric Facial Recognition & Data Privacy Policy</h4>
            </div>
            <div class="card-body p-4">
                <p class="lead text-dark">
                    At Apex Academy, we are committed to safeguarding student and faculty personal information while delivering seamless, secure, and modern attendance verification.
                </p>
                <hr class="my-4">

                <h5 class="fw-bold text-primary mb-3">1. Scope and Purpose</h5>
                <p>
                    Facial recognition biometric technology is strictly used for <strong>verifying student identity during daily institutional attendance check-in, check-out, and break intervals</strong>. The system eliminates manual roll calls, mitigates proxy attendance, and ensures verified student presence on campus.
                </p>

                <h5 class="fw-bold text-primary mb-3">2. Facial Descriptors and Storage</h5>
                <p>
                    When a face is registered, our browser-based neural network processes facial landmarks into a <strong>128-dimensional numerical vector (mathematical facial descriptor)</strong>. These numerical descriptors are stored securely in the database for distance-based matching algorithms.
                </p>

                <h5 class="fw-bold text-primary mb-3">3. Registration & Verification Images</h5>
                <p>
                    Registration preview photos and timestamped attendance snapshots are preserved solely to provide visual verification logs for authorized academic administrators and auditing compliance. Raw unencrypted images are never transmitted to commercial third-party platforms.
                </p>

                <h5 class="fw-bold text-primary mb-3">4. Strict Role-Based Access Control</h5>
                <p>
                    Access to biometric parameters, descriptors, and attendance histories is rigorously enforced by cryptographic sessions and role authorization middleware:
                </p>
                <ul>
                    <li><strong>Administrators:</strong> May register faces, view full logs, export CSV attendance records, and adjust quality parameters.</li>
                    <li><strong>Teachers:</strong> Permitted only to view attendance records for cohorts and classes they are officially assigned to teach.</li>
                    <li><strong>Students:</strong> Can only inspect their individual attendance logs, review their registration status, and submit leave requests.</li>
                </ul>

                <h5 class="fw-bold text-primary mb-3">5. Data Minimization & Retention</h5>
                <p>
                    The system adheres to the principle of data minimization—only information strictly essential for school attendance management is collected. Students or guardians with inquiries or seeking biometric re-registration may contact the campus administrative registrar.
                </p>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Policy Version 1.0 &bull; Effective Academic Year 2026</span>
                    <a href="<?= url('/') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
