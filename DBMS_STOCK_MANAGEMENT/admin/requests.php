<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['ADMIN']);
verify_csrf();
$pdo = Database::conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $request = one('SELECT * FROM requests WHERE id=? AND status="APPROVED_BY_PRINCIPAL" FOR UPDATE', [(int)$_POST['id']]);
        if (!$request) throw new RuntimeException('Request is not approved by Principal.');
        $items = rows('SELECT ri.*, i.item_name FROM request_items ri JOIN items i ON i.id=ri.item_id WHERE ri.request_id=?', [(int)$_POST['id']]);
        foreach ($items as $ri) {
            $issueQty = (float)($_POST['issue_qty'][$ri['id']] ?? 0);
            if ($issueQty < 0 || $issueQty > (float)$ri['requested_quantity']) throw new RuntimeException('Invalid issue quantity.');
            if ($issueQty > 0) {
                record_stock_transaction((int)$ri['item_id'], 'OUTWARD', $issueQty, 'Issued against request ' . $request['request_no'], (int)$ri['id']);
                $pdo->prepare('UPDATE request_items SET issued_quantity=? WHERE id=?')->execute([$issueQty, $ri['id']]);
            }
        }
        $pdo->prepare('UPDATE requests SET status="ISSUED", admin_issued_by=?, admin_issued_at=NOW() WHERE id=?')->execute([current_user()['id'], (int)$_POST['id']]);
        $pdo->prepare('INSERT INTO approvals (request_id, approver_id, role, status, remarks) VALUES (?, ?, "ADMIN", "ISSUED", ?)')->execute([(int)$_POST['id'], current_user()['id'], trim($_POST['remarks'])]);
        $pdo->commit();
        log_activity('Issued stock for request ID ' . $_POST['id']);
        flash('success', 'Stock issued.');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', $e->getMessage());
    }
    redirect('/admin/requests.php');
}
$requests = rows('SELECT r.*, d.name department_name, u.name requested_by_name FROM requests r JOIN departments d ON d.id=r.department_id JOIN users u ON u.id=r.requested_by ORDER BY r.id DESC');
render_header('Admin Issue Requests');
?>
<h1 class="h3 mb-3">Central Admin Issue Desk</h1>
<div class="card metric-card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Indent No</th><th>Department</th><th>Subject</th><th>Status</th><th>Principal Remarks</th><th>Actions</th></tr></thead><tbody><?php foreach($requests as $r): $items=rows('SELECT ri.*, i.item_name, i.quantity, i.unit FROM request_items ri JOIN items i ON i.id=ri.item_id WHERE ri.request_id=?', [$r['id']]); ?><tr><td><?= e($r['request_no']) ?></td><td><?= e($r['department_name']) ?></td><td><?= e($r['purpose']) ?></td><td><span class="badge text-bg-secondary"><?= e($r['status']) ?></span></td><td><?= e($r['principal_remarks']) ?></td><td><a class="btn btn-sm btn-outline-danger" target="_blank" href="<?= BASE_URL ?>/download_pdf.php?type=request&id=<?= $r['id'] ?>"><i class="bi bi-file-pdf me-1"></i>PDF</a><?php if($r['status']==='APPROVED_BY_PRINCIPAL'): ?><button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#issue<?= $r['id'] ?>">Issue Items</button><?php endif; ?></td></tr><div class="modal fade" id="issue<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog modal-xl"><form method="post" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><div class="modal-header"><h5 class="modal-title">Issue <?= e($r['request_no']) ?></h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body"><table class="table"><thead><tr><th>Item</th><th>Requested</th><th>Available</th><th>Justification</th><th>Issue</th></tr></thead><tbody><?php foreach($items as $it): ?><tr><td><?= e($it['item_name']) ?></td><td><?= e($it['requested_quantity'].' '.$it['unit']) ?></td><td><?= e($it['quantity'].' '.$it['unit']) ?></td><td><?= e($it['justification']) ?></td><td><input class="form-control" name="issue_qty[<?= $it['id'] ?>]" type="number" min="0" max="<?= e(min($it['requested_quantity'],$it['quantity'])) ?>" step="0.01" value="<?= e((string)min($it['requested_quantity'],$it['quantity'])) ?>"></td></tr><?php endforeach; ?></tbody></table><textarea name="remarks" class="form-control" placeholder="Issue remarks"></textarea></div><div class="modal-footer"><button class="btn btn-success">Issue Approved Items</button></div></form></div></div><?php endforeach; ?></tbody></table></div></div>
<?php render_footer(); ?>
