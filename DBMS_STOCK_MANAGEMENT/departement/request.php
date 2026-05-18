<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['DEPARTMENT']);
verify_csrf();
$pdo = Database::conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['items'] ?? [];
    if (!$selected) {
        flash('danger', 'Select at least one item.');
        redirect('/department/request.php');
    }
    try {
        $pdo->beginTransaction();
        $requestNo = next_request_number();
        $pdo->prepare('INSERT INTO requests (request_no, department_id, requested_by, purpose, status) VALUES (?, ?, ?, ?, "PENDING_PRINCIPAL")')
            ->execute([$requestNo, current_user()['department_id'], current_user()['id'], trim($_POST['purpose'])]);
        $requestId = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO request_items (request_id, item_id, requested_quantity, justification) VALUES (?, ?, ?, ?)');
        foreach ($selected as $itemId) {
            $qty = (float)($_POST['qty'][$itemId] ?? 0);
            if ($qty <= 0) {
                throw new RuntimeException('Requested quantity must be positive.');
            }
            $stmt->execute([$requestId, (int)$itemId, $qty, trim($_POST['justification'][$itemId] ?? '')]);
        }
        $pdo->prepare('INSERT INTO approvals (request_id, approver_id, role, status, remarks) VALUES (?, NULL, "PRINCIPAL", "PENDING", "Awaiting approval")')->execute([$requestId]);
        $pdo->commit();
        log_activity('Created request ' . $requestNo);
        flash('success', 'Request submitted to Principal.');
        redirect('/department/history.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', $e->getMessage());
    }
}
$items = rows('SELECT i.*, c.name category_name FROM items i JOIN categories c ON c.id=i.category_id WHERE i.deleted_at IS NULL AND i.status="ACTIVE" ORDER BY c.name,i.item_name');
render_header('New Request');
?>
<h1 class="h3 mb-3">Department Indent Request</h1>
<form method="post" class="card metric-card">
  <?php csrf_field(); ?>
  <div class="card-body">
    <div class="alert alert-info">This form generates the Principal approval letter PDF with item-wise available quantity, required quantity, and justification.</div>
    <div class="mb-3">
      <label class="form-label">Subject / requirement purpose</label>
      <textarea name="purpose" class="form-control" required placeholder="Example: Stationery requirement for Odd Semester 2025-26"></textarea>
    </div>
    <input class="form-control mb-3" data-filter-table="#requestItems" placeholder="Search available items">
    <div class="table-responsive">
      <table class="table table-hover align-middle" id="requestItems">
        <thead><tr><th>Select</th><th>Item</th><th>Category</th><th>Available</th><th>Required Qty</th><th>Justification</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach($items as $item): ?>
          <tr>
            <td><input class="form-check-input request-check" type="checkbox" name="items[]" value="<?= $item['id'] ?>"></td>
            <td><?= e($item['item_name']) ?><div class="small text-muted"><?= e($item['item_code']) ?></div></td>
            <td><?= e($item['category_name']) ?></td><td><?= e($item['quantity'].' '.$item['unit']) ?></td>
            <td><input class="form-control qty-input request-qty" type="number" min="1" step="0.01" name="qty[<?= $item['id'] ?>]" disabled></td>
            <td><input class="form-control request-justification" name="justification[<?= $item['id'] ?>]" placeholder="Reason / event / semester use" disabled></td>
            <td><?= stock_badge($item) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white text-end"><button class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Request</button></div>
</form>
<?php render_footer(); ?>
