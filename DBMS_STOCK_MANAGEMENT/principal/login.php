<?php
require_once __DIR__ . '/../includes/auth.php';
if (current_user()) {
    redirect('/principal/requests.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (login_user(trim($_POST['email'] ?? ''), $_POST['password'] ?? '') && current_user()['role'] === 'PRINCIPAL') {
        redirect('/principal/requests.php');
    }
    $_SESSION = [];
    session_destroy();
    session_start();
    flash('danger', 'Invalid principal login.');
}
require_once __DIR__ . '/../includes/layout.php';
render_header('Principal Login');
?>
<div class="auth-shell">
  <div class="card auth-card">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-2 mb-3">
        <img src="<?= APP_LOGO ?>" alt="<?= e(APP_SHORT_NAME) ?> logo" width="44" height="44" class="brand-logo">
        <h1 class="h4 mb-0">Principal Login</h1>
      </div>
      <form method="post">
        <?php csrf_field(); ?>
        <div class="mb-3">
          <label class="form-label">Principal Email</label>
          <input type="email" name="email" class="form-control" value="principal@college.test" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100"><i class="bi bi-check2-square me-1"></i> Login for Approval</button>
      </form>
    </div>
  </div>
</div>
<?php render_footer(); ?>

