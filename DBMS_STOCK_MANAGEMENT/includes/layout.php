<?php
require_once __DIR__ . '/auth.php';

function render_header(string $title): void
{
    $u = current_user();
    $role = $u['role'] ?? '';
    $base = BASE_URL;
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' | ' . APP_NAME . '</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">';
    echo '<link href="' . $base . '/assets/css/app.css" rel="stylesheet"></head><body>';
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top"><div class="container-fluid">';
    echo '<a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="' . $base . '/dashboard.php"><img src="' . APP_LOGO . '" alt="' . e(APP_SHORT_NAME) . ' logo" width="34" height="34" class="brand-logo">' . e(APP_SHORT_NAME) . '</a>';
    if ($u) {
        echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav"><span class="navbar-toggler-icon"></span></button>';
        echo '<div class="collapse navbar-collapse" id="topnav"><ul class="navbar-nav me-auto">';
        $links = [
            'ADMIN' => [['/admin/items.php','Items','boxes'],['/admin/categories.php','Categories','tags'],['/admin/transactions.php','Transactions','arrow-left-right'],['/admin/requests.php','Issue Requests','clipboard-check'],['/admin/reports.php','Reports','file-earmark-bar-graph'],['/admin/users.php','Users','people'],['/admin/logs.php','Logs','activity']],
            'PRINCIPAL' => [['/principal/requests.php','Approvals','check2-square']],
            'DEPARTMENT' => [['/department/request.php','New Request','cart-plus'],['/department/history.php','History','clock-history']],
        ];
        foreach ($links[$role] ?? [] as $l) {
            echo '<li class="nav-item"><a class="nav-link" href="' . $base . $l[0] . '"><i class="bi bi-' . $l[2] . ' me-1"></i>' . $l[1] . '</a></li>';
        }
        echo '</ul><span class="navbar-text me-3">' . e($u['name']) . ' (' . e($role) . ')</span><a class="btn btn-outline-light btn-sm" href="' . $base . '/logout.php">Logout</a></div>';
    }
    echo '</div></nav><main class="container-fluid py-4">';
    foreach ($_SESSION['flash'] ?? [] as $f) {
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show">' . e($f['message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    unset($_SESSION['flash']);
}

function render_footer(): void
{
    echo '</main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '<script src="' . BASE_URL . '/assets/js/app.js"></script></body></html>';
}

function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
