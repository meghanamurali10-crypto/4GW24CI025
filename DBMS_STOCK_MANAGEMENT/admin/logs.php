<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['ADMIN']);
$logs = rows('SELECT al.*, u.name FROM activity_logs al LEFT JOIN users u ON u.id=al.user_id ORDER BY al.id DESC LIMIT 300');
$logins = rows('SELECT lh.*, u.name FROM login_history lh LEFT JOIN users u ON u.id=lh.user_id ORDER BY lh.id DESC LIMIT 100');
$archivedItems = rows('SELECT i.*, c.name category_name, u.name archived_by_name FROM items i JOIN categories c ON c.id=i.category_id LEFT JOIN users u ON u.id=i.archived_by WHERE i.status="INACTIVE" OR i.deleted_at IS NOT NULL ORDER BY i.archived_at DESC, i.deleted_at DESC LIMIT 200');
render_header('Activity Logs');
?>
<h1 class="h3 mb-3">Activity Logs</h1>
<div class="row g-3">
  <div class="col-xl-7"><div class="card metric-card"><div class="card-header bg-white fw-semibold">Audit Trail</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Time</th><th>User</th><th>Action</th><th>IP</th></tr></thead><tbody><?php foreach($logs as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['name']) ?></td><td><?= e($l['action']) ?></td><td><?= e($l['ip_address']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
  <div class="col-xl-5"><div class="card metric-card"><div class="card-header bg-white fw-semibold">Login History</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Time</th><th>Email</th><th>Status</th><th>IP</th></tr></thead><tbody><?php foreach($logins as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['email']) ?></td><td><?= $l['success']?'<span class="badge text-bg-success">Success</span>':'<span class="badge text-bg-danger">Failed</span>' ?></td><td><?= e($l['ip_address']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<div class="card metric-card mt-3">
  <div class="card-header bg-white fw-semibold">Archived / Inactive Item Audit</div>
  <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Archived At</th><th>Code</th><th>Item</th><th>Category</th><th>Reason</th><th>Archived By</th><th>Deletion Approval</th></tr></thead><tbody><?php foreach($archivedItems as $i): ?><tr><td><?= e($i['archived_at'] ?: $i['deleted_at']) ?></td><td><?= e($i['item_code']) ?></td><td><?= e($i['item_name']) ?></td><td><?= e($i['category_name']) ?></td><td><?= e($i['archive_reason']) ?></td><td><?= e($i['archived_by_name']) ?></td><td><span class="badge text-bg-warning"><?= e($i['deletion_approval_status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
</div>
<?php render_footer(); ?>
