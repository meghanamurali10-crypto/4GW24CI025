<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['ADMIN']);
verify_csrf();
$pdo = Database::conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'create') {
        $pdo->prepare('INSERT INTO users (name,email,password_hash,role,department_id,status) VALUES (?,?,?,?,?,?)')
            ->execute([trim($_POST['name']), trim($_POST['email']), password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['role'], $_POST['department_id'] ?: null, $_POST['status']]);
        log_activity('Created user ' . $_POST['email']);
        flash('success', 'User created.');
    }
    redirect('/admin/users.php');
}
$users = rows('SELECT u.*, d.name department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id ORDER BY u.role,u.name');
$departments = rows('SELECT * FROM departments ORDER BY name');
render_header('Users');
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Users</h1><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">Add User</button></div>
<div class="card metric-card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['department_name']) ?></td><td><?= e($u['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="userModal" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="action" value="create"><div class="modal-header"><h5 class="modal-title">Add User</h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div><div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div><div class="mb-3"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required></div><div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select"><option>ADMIN</option><option>PRINCIPAL</option><option>DEPARTMENT</option></select></div><div class="mb-3"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">None</option><?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?></select></div><div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option>ACTIVE</option><option>INACTIVE</option></select></div></div><div class="modal-footer"><button class="btn btn-primary">Create</button></div></form></div></div>
<?php render_footer(); ?>

