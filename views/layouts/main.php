<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Apex Attendance System') ?></title>
    <!-- Local Vendor Assets -->
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <div class="d-flex align-items-center justify-content-between border-bottom" style="border-color: #1e293b !important;">
            <a href="<?= url('/') ?>" class="sidebar-brand border-0">
                <i class="bi bi-person-bounding-box text-primary fs-4"></i>
                <span>Apex Attendance</span>
            </a>
            <button type="button" class="btn btn-link text-white-50 p-2 d-lg-none me-2" id="sidebar-close" aria-label="Close sidebar">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <ul class="sidebar-menu">
            <?php if (has_role('admin')): ?>
                <li class="sidebar-heading">Core Administration</li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/dashboard') ?>" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/kiosk') ?>" class="sidebar-link text-warning" target="_blank">
                        <i class="bi bi-camera-video-fill"></i> <span>Launch Kiosk</span>
                    </a>
                </li>

                <li class="sidebar-heading">Academic Management</li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/students') ?>" class="sidebar-link">
                        <i class="bi bi-people-fill"></i> <span>Students</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/teachers') ?>" class="sidebar-link">
                        <i class="bi bi-mortarboard-fill"></i> <span>Teachers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/classes') ?>" class="sidebar-link">
                        <i class="bi bi-diagram-3-fill"></i> <span>Classes & Schedules</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/subjects') ?>" class="sidebar-link">
                        <i class="bi bi-journal-bookmark-fill"></i> <span>Subjects</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/assignments') ?>" class="sidebar-link">
                        <i class="bi bi-shuffle"></i> <span>Assignments</span>
                    </a>
                </li>

                <li class="sidebar-heading">Attendance & Audits</li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/attendance') ?>" class="sidebar-link">
                        <i class="bi bi-calendar2-check-fill"></i> <span>Attendance Records</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/leaves') ?>" class="sidebar-link">
                        <i class="bi bi-file-earmark-medical-fill"></i> <span>Leave Requests</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/settings') ?>" class="sidebar-link">
                        <i class="bi bi-sliders"></i> <span>Face Quality Settings</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/admin/audit-logs') ?>" class="sidebar-link">
                        <i class="bi bi-shield-check"></i> <span>Audit Logs</span>
                    </a>
                </li>
            <?php elseif (has_role('teacher')): ?>
                <li class="sidebar-heading">Teacher Portal</li>
                <li class="sidebar-item">
                    <a href="<?= url('/teacher/dashboard') ?>" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/teacher/attendance') ?>" class="sidebar-link">
                        <i class="bi bi-calendar-check"></i> <span>Class Attendance</span>
                    </a>
                </li>
            <?php elseif (has_role('student')): ?>
                <li class="sidebar-heading">Student Portal</li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/dashboard') ?>" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/attendance') ?>" class="sidebar-link">
                        <i class="bi bi-calendar2-week"></i> <span>My Attendance</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/face/scan') ?>" class="sidebar-link">
                        <i class="bi bi-camera-fill"></i> <span>Scan Attendance</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/face/register') ?>" class="sidebar-link">
                        <i class="bi bi-person-bounding-box"></i> <span>Face Registration</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/leaves') ?>" class="sidebar-link">
                        <i class="bi bi-file-earmark-text"></i> <span>Apply for Leave</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= url('/student/profile') ?>" class="sidebar-link">
                        <i class="bi bi-person-gear"></i> <span>Profile & Security</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="sidebar-heading">System</li>
            <li class="sidebar-item">
                <a href="<?= url('/privacy') ?>" class="sidebar-link">
                    <i class="bi bi-lock-fill"></i> <span>Privacy Policy</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?= url('/logout') ?>" class="sidebar-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> <span>Sign Out</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Layout -->
    <div class="app-main">
        <!-- Top Navbar -->
        <header class="app-navbar">
            <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden me-2">
                <button type="button" class="btn btn-light btn-sm d-lg-none shadow-none border-0 px-2" id="sidebar-toggle" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark text-truncate fs-6 fs-md-5" title="<?= e($title ?? 'Dashboard') ?>"><?= e($title ?? 'Dashboard') ?></h5>
            </div>
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
                <div class="text-end d-none d-sm-block">
                    <?php 
                        $displayName = auth_user()['full_name'] ?? 'User';
                        if (strtolower(trim($displayName)) === 'admin' || (auth_user()['role'] ?? '') === 'admin' && $displayName === 'Admin') {
                            $displayName = 'System Administrator';
                        }
                    ?>
                    <div class="fw-bold text-dark small text-truncate" style="max-width: 140px;"><?= e($displayName) ?></div>
                    <small class="badge bg-primary text-uppercase" style="font-size: 0.65rem;"><?= e(auth_user()['role'] ?? 'guest') ?></small>
                </div>
                <a href="<?= url('/logout') ?>" class="btn btn-outline-danger btn-sm" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <!-- Body Area -->
        <main class="app-content">
            <!-- Global Flash Messages -->
            <?php if (!empty($flashes)): ?>
                <?php foreach ($flashes as $type => $messages): ?>
                    <?php 
                        $alertClass = match($type) {
                            'success' => 'alert-success',
                            'error' => 'alert-danger',
                            'warning' => 'alert-warning',
                            default => 'alert-info'
                        };
                    ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="alert <?= $alertClass ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                            <i class="bi <?= $type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                            <?= e($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="<?= asset('vendor/bootstrap.bundle.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.app-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('sidebar-close');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('show');
        if (backdrop) backdrop.classList.add('show');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // Auto close sidebar when clicking a link on mobile
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });
    }
});
</script>
</body>
</html>
