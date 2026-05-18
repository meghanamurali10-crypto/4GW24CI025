<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['ADMIN']);
verify_csrf();
$pdo = Database::conn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $invoice = upload_file($_FILES['invoice'] ?? [], 'invoices', ['pdf','jpg','jpeg','png']);
            $stmt = $pdo->prepare('INSERT INTO items (item_code,item_name,category_id,quantity,unit,unit_price,minimum_stock,storage_location,description,invoice_path,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([trim($_POST['item_code']), trim($_POST['item_name']), $_POST['category_id'], (float)$_POST['quantity'], trim($_POST['unit']), (float)$_POST['unit_price'], (float)$_POST['minimum_stock'], trim($_POST['storage_location']), trim($_POST['description']), $invoice, current_user()['id']]);
            record_stock_transaction((int)$pdo->lastInsertId(), 'INWARD', (float)$_POST['quantity'], 'Opening stock');
            log_activity('Created item ' . $_POST['item_code']);
            flash('success', 'Item added.');
        }
        if ($action === 'update') {
            $item = one('SELECT * FROM items WHERE id=?', [(int)$_POST['id']]);
            if (!$item || !can_edit_item($item['created_at'])) {
                throw new RuntimeException('Direct editing is locked after 10 minutes. Use adjustment transactions.');
            }
            $stmt = $pdo->prepare('UPDATE items SET item_code=?, item_name=?, category_id=?, unit=?, unit_price=?, minimum_stock=?, storage_location=?, description=? WHERE id=?');
            $stmt->execute([trim($_POST['item_code']), trim($_POST['item_name']), $_POST['category_id'], trim($_POST['unit']), (float)$_POST['unit_price'], (float)$_POST['minimum_stock'], trim($_POST['storage_location']), trim($_POST['description']), (int)$_POST['id']]);
            log_activity('Updated item ' . $_POST['item_code']);
            flash('success', 'Item updated.');
        }
        if ($action === 'delete') {
            $reason = trim($_POST['archive_reason'] ?? '');
            if ($reason === '') {
                throw new RuntimeException('Archive reason is required.');
            }
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM stock_transactions WHERE item_id=?');
            $stmt->execute([(int)$_POST['id']]);
            $hasTransactions = (int)$stmt->fetchColumn() > 0;
            $approvalStatus = $hasTransactions ? 'PENDING_PRINCIPAL' : 'NOT_REQUESTED';
            $pdo->prepare('UPDATE items SET status="INACTIVE", deleted_at=NOW(), archived_at=NOW(), archived_by=?, archive_reason=?, deletion_approval_status=? WHERE id=?')
                ->execute([current_user()['id'], $reason, $approvalStatus, (int)$_POST['id']]);
            log_activity('Archived item ID ' . $_POST['id'] . ' | Reason: ' . $reason . ' | Principal deletion approval: ' . $approvalStatus);
            flash('warning', $hasTransactions ? 'Item marked inactive. Permanent deletion requires Principal approval.' : 'Item marked inactive with audit reason.');
        }
    } catch (Throwable $e) {
        flash('danger', $e->getMessage());
    }
    redirect('/admin/items.php');
}

$categories = rows('SELECT * FROM categories ORDER BY name');
$category = $_GET['category'] ?? '';
$low = isset($_GET['low']);
$params = [];
$where = 'WHERE i.deleted_at IS NULL AND i.status = "ACTIVE"';
if ($category !== '') { $where .= ' AND i.category_id=?'; $params[] = $category; }
if ($low) { $where .= ' AND i.quantity < i.minimum_stock'; }
$items = rows("SELECT i.*, c.name category_name FROM items i JOIN categories c ON c.id=i.category_id $where ORDER BY i.item_name", $params);
render_header('Item Management');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Item Management</h1>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
</div>
<form class="row g-2 mb-3">
  <div class="col-md-4"><input class="form-control" data-filter-table="#itemsTable" placeholder="Search items"></div>
  <div class="col-md-3"><select name="category" class="form-select" onchange="this.form.submit()"><option value="">All categories</option><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $category==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3 form-check d-flex align-items-center ps-4"><input class="form-check-input me-2" type="checkbox" name="low" value="1" onchange="this.form.submit()" <?= $low?'checked':'' ?>><label class="form-check-label">Low stock only</label></div>
</form>
<div class="card metric-card"><div class="table-responsive">
<table class="table table-hover align-middle mb-0" id="itemsTable">
  <thead><tr><th>Code</th><th>Name</th><th>Category</th><th>Qty</th><th>Unit</th><th>Price</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($items as $i): $locked=!can_edit_item($i['created_at']); ?>
    <tr class="<?= $locked?'locked':'' ?>">
      <td><?= e($i['item_code']) ?></td><td><?= e($i['item_name']) ?></td><td><?= e($i['category_name']) ?></td><td><?= e($i['quantity']) ?></td><td><?= e($i['unit']) ?></td><td><?= e($i['unit_price']) ?></td><td><?= e($i['storage_location']) ?></td><td><?= stock_badge($i) ?></td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit<?= $i['id'] ?>" <?= $locked?'disabled title="Edit locked"':'' ?>><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#archive<?= $i['id'] ?>"><i class="bi bi-archive"></i></button>
      </td>
    </tr>
    <div class="modal fade" id="edit<?= $i['id'] ?>" tabindex="-1"><div class="modal-dialog modal-lg"><form method="post" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $i['id'] ?>"><?php include __DIR__ . '/../includes/item_form.php'; ?></form></div></div>
    <div class="modal fade" id="archive<?= $i['id'] ?>" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $i['id'] ?>"><div class="modal-header"><h5 class="modal-title">Mark Item Inactive</h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body"><p class="text-muted">This will not physically delete the item. It will be hidden from active stock and recorded in the audit report. Items with transactions require Principal approval before any permanent deletion.</p><label class="form-label">Reason</label><textarea name="archive_reason" class="form-control" rows="4" required placeholder="Reason for archiving / making inactive"></textarea></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger">Mark Inactive</button></div></form></div></div>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
<div class="modal fade" id="itemModal" tabindex="-1"><div class="modal-dialog modal-lg"><form method="post" enctype="multipart/form-data" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="action" value="create"><?php $i=[]; include __DIR__ . '/../includes/item_form.php'; ?></form></div></div>
<?php render_footer(); ?>
