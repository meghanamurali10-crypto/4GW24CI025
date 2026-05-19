<?php
require_once __DIR__ . '/../includes/layout.php';
require_role(['PRINCIPAL']);
verify_csrf();
$pdo = Database::conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'item_archive_decision') {
        $status = $_POST['decision'] === 'approve' ? 'APPROVED_BY_PRINCIPAL' : 'REJECTED_BY_PRINCIPAL';
        $pdo->prepare('UPDATE items SET deletion_approval_status=? WHERE id=? AND deletion_approval_status="PENDING_PRINCIPAL"')
            ->execute([$status, (int)$_POST['item_id']]);
        log_activity('Principal ' . $status . ' item deletion/archive ID ' . $_POST['item_id'] . ' | Remarks: ' . trim($_POST['remarks']));
        flash('success', 'Item deletion approval decision recorded.');
    } else {
        $status = $_POST['decision'] === 'approve' ? 'APPROVED_BY_PRINCIPAL' : 'REJECTED_BY_PRINCIPAL';
        $pdo->prepare('UPDATE requests SET status=?, principal_remarks=?, principal_approved_by=?, principal_approved_at=NOW() WHERE id=?')
            ->execute([$status, trim($_POST['remarks']), current_user()['id'], (int)$_POST['id']]);
        $pdo->prepare('INSERT INTO approvals (request_id, approver_id, role, status, remarks) VALUES (?, ?, "PRINCIPAL", ?, ?)')
            ->execute([(int)$_POST['id'], current_user()['id'], $status, trim($_POST['remarks'])]);
        log_activity('Principal ' . $status . ' request ID ' . $_POST['id']);
        flash('success', 'Decision recorded.');
    }
    redirect('/principal/requests.php');
}
$requests = rows('SELECT r.*, d.name department_name, u.name requested_by_name FROM requests r JOIN departments d ON d.id=r.department_id JOIN users u ON u.id=r.requested_by ORDER BY FIELD(r.status,"PENDING_PRINCIPAL","APPROVED_BY_PRINCIPAL","REJECTED_BY_PRINCIPAL","ISSUED"), r.id DESC');
$archiveRequests = rows('SELECT i.*, c.name category_name, u.name archived_by_name FROM items i JOIN categories c ON c.id=i.category_id LEFT JOIN users u ON u.id=i.archived_by WHERE i.deletion_approval_status="PENDING_PRINCIPAL" ORDER BY i.archived_at DESC');
render_header('Principal Approvals');
?>
<h1 class="h3 mb-3">Principal Request Review</h1>
<div class="card metric-card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Indent No</th><th>Department</th><th>Requested By</th><th>Subject</th><th>Status</th><th>Review & Approval</th></tr></thead><tbody><?php foreach($requests as $r): ?><tr><td><?= e($r['request_no']) ?></td><td><?= e($r['department_name']) ?></td><td><?= e($r['requested_by_name']) ?></td><td><?= e($r['purpose']) ?></td><td><span class="badge text-bg-secondary"><?= e($r['status']) ?></span></td><td><a class="btn btn-sm btn-outline-danger" target="_blank" href="<?= BASE_URL ?>/download_pdf.php?type=request&id=<?= $r['id'] ?>"><i class="bi bi-file-pdf me-1"></i>Review PDF</a><?php if($r['status']==='PENDING_PRINCIPAL'): ?><button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#decision<?= $r['id'] ?>"><i class="bi bi-check2-square me-1"></i>Approve / Reject</button><?php endif; ?></td></tr><div class="modal fade" id="decision<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><?php csrf_field(); ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><div class="modal-header"><h5 class="modal-title">Principal Decision - <?= e($r['request_no']) ?></h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div><div class="modal-body"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control" rows="4" placeholder="Approval note or rejection reason"></textarea></div><div class="modal-footer"><button name="decision" value="reject" class="btn btn-danger">Reject</button><button name="decision" value="approve" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button></div></form></div></div><?php endforeach; ?></tbody></table></div></div>
<div class="card metric-card mt-3"><div class="card-header bg-white fw-semibold">Item Deletion Approval Requests</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Item</th><th>Category</th><th>Archived By</th><th>Reason</th><th>Archived At</th><th>Decision</th></tr></thead><tbody><?php foreach($archiveRequests as $i): ?><tr><td><?= e($i['item_code'].' - '.$i['item_name']) ?></td><td><?= e($i['category_name']) ?></td><td><?= e($i['archived_by_name']) ?></td><td><?= e($i['archive_reason']) ?></td><td><?= e($i['archived_at']) ?></td><td><form method="post" class="d-flex gap-2"><?php csrf_field(); ?><input type="hidden" name="action" value="item_archive_decision"><input type="hidden" name="item_id" value="<?= $i['id'] ?>"><input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks"><button name="decision" value="reject" class="btn btn-sm btn-outline-danger">Reject</button><button name="decision" value="approve" class="btn btn-sm btn-success">Approve</button></form></td></tr><?php endforeach; ?><?php if(!$archiveRequests): ?><tr><td colspan="6" class="text-muted">No pending item deletion approvals.</td></tr><?php endif; ?></tbody></table></div></div>
<?php render_footer(); ?>
