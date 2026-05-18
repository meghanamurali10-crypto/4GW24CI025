<?php
require_once __DIR__ . '/includes/layout.php';
require_login();
$pdo = Database::conn();
$role = current_user()['role'];
$deptId = current_user()['department_id'];
$cards = [
    'Total Items' => (int)$pdo->query('SELECT COUNT(*) FROM items WHERE deleted_at IS NULL AND status="ACTIVE"')->fetchColumn(),
    'Low Stock Items' => (int)$pdo->query('SELECT COUNT(*) FROM items WHERE deleted_at IS NULL AND status="ACTIVE" AND quantity < minimum_stock')->fetchColumn(),
    'Pending Requests' => (int)$pdo->query("SELECT COUNT(*) FROM requests WHERE status IN ('PENDING_PRINCIPAL','APPROVED_BY_PRINCIPAL')")->fetchColumn(),
    'Issued Items' => (int)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_transactions WHERE type='OUTWARD'")->fetchColumn(),
];
$recent = rows('SELECT st.*, i.item_name, u.name FROM stock_transactions st JOIN items i ON i.id=st.item_id LEFT JOIN users u ON u.id=st.created_by ORDER BY st.id DESC LIMIT 8');
$deptUsage = [];
$yearTrend = [];
$departments = rows('SELECT id, code, name FROM departments WHERE code <> "ADMIN" ORDER BY code');
$selectedYear = $_GET['year'] ?? date('Y');
$selectedDept = $_GET['department_id'] ?? '';
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');
$dateClause = 'YEAR(r.created_at) = ?';
$dateParams = [$selectedYear];
if ($from !== '' && $to !== '') {
    $dateClause = 'DATE(r.created_at) BETWEEN ? AND ?';
    $dateParams = [$from, $to];
}
if ($role === 'ADMIN') {
    $deptParams = $dateParams;
    $deptFilter = '';
    if ($selectedDept !== '') {
        $deptFilter = ' AND d.id = ?';
        $deptParams[] = (int)$selectedDept;
    }
    $deptUsage = rows("
        SELECT d.code, d.name,
               COALESCE(SUM(ri.requested_quantity),0) requested_qty,
               COALESCE(SUM(ri.issued_quantity),0) issued_qty
        FROM departments d
        LEFT JOIN requests r ON r.department_id = d.id AND $dateClause
        LEFT JOIN request_items ri ON ri.request_id = r.id
        WHERE d.code <> 'ADMIN' $deptFilter
        GROUP BY d.id, d.code, d.name
        ORDER BY issued_qty DESC, requested_qty DESC, d.code
    ", $deptParams);
    $yearTrend = rows("
        SELECT YEAR(r.created_at) report_year,
               COALESCE(SUM(ri.requested_quantity),0) requested_qty,
               COALESCE(SUM(ri.issued_quantity),0) issued_qty
        FROM requests r
        JOIN request_items ri ON ri.request_id = r.id
        WHERE (? = '' OR r.department_id = ?)
        GROUP BY YEAR(r.created_at)
        ORDER BY report_year
    ", [$selectedDept, $selectedDept]);
} elseif ($role === 'DEPARTMENT') {
    $yearTrend = rows("
        SELECT YEAR(r.created_at) report_year,
               COALESCE(SUM(ri.requested_quantity),0) requested_qty,
               COALESCE(SUM(ri.issued_quantity),0) issued_qty
        FROM requests r
        JOIN request_items ri ON ri.request_id = r.id
        WHERE r.department_id = ?
        GROUP BY YEAR(r.created_at)
        ORDER BY report_year
    ", [$deptId]);
    $deptUsage = rows("
        SELECT d.code, d.name,
               COALESCE(SUM(ri.requested_quantity),0) requested_qty,
               COALESCE(SUM(ri.issued_quantity),0) issued_qty
        FROM departments d
        LEFT JOIN requests r ON r.department_id = d.id
        LEFT JOIN request_items ri ON ri.request_id = r.id
        WHERE d.id = ?
        GROUP BY d.id, d.code, d.name
    ", [$deptId]);
}
render_header('Dashboard');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h3 mb-0">Dashboard</h1>
    <div class="text-muted"><?= e(current_user()['department_name'] ?: $role) ?></div>
  </div>
</div>
<div class="row g-3 mb-4">
  <?php foreach ($cards as $label => $value): ?>
  <div class="col-6 col-xl-3">
    <div class="card metric-card"><div class="card-body">
      <div class="text-muted small"><?= e($label) ?></div>
      <div class="display-6 fw-semibold"><?= e((string)$value) ?></div>
    </div></div>
  </div>
  <?php endforeach; ?>
</div>
<?php if (in_array($role, ['ADMIN','DEPARTMENT'], true)): ?>
<?php
$maxDept = 1;
foreach ($deptUsage as $d) {
    $maxDept = max($maxDept, (float)$d['requested_qty'], (float)$d['issued_qty']);
}
$maxYear = 1;
foreach ($yearTrend as $y) {
    $maxYear = max($maxYear, (float)$y['requested_qty'], (float)$y['issued_qty']);
}
?>
<?php if ($role === 'ADMIN'): ?>
<div class="card metric-card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end">
      <div class="col-md-2"><label class="form-label">Year</label><select name="year" class="form-select"><?php for($y=(int)date('Y'); $y>=((int)date('Y')-10); $y--): ?><option value="<?= $y ?>" <?= (string)$selectedYear===(string)$y?'selected':'' ?>><?= $y ?></option><?php endfor; ?></select></div>
      <div class="col-md-3"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">All departments</option><?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>" <?= (string)$selectedDept===(string)$d['id']?'selected':'' ?>><?= e($d['code'].' - '.$d['name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><label class="form-label">From</label><input type="date" name="from" class="form-control" value="<?= e($from) ?>"></div>
      <div class="col-md-2"><label class="form-label">To</label><input type="date" name="to" class="form-control" value="<?= e($to) ?>"></div>
      <div class="col-md-3 d-grid"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Update Graphs</button></div>
    </form>
    <div class="small text-muted mt-2">Leave dates empty to use the selected year. Choose a department for department-only graphs, or leave it as all departments for overall comparison.</div>
  </div>
</div>
<?php endif; ?>
<div class="row g-3 mb-4">
  <div class="col-xl-7">
    <div class="card metric-card h-100">
      <div class="card-header bg-white fw-semibold"><?php if ($role === 'ADMIN'): ?>Department Stock Sharing<?= $from && $to ? ' - '.e($from.' to '.$to) : ' - '.e((string)$selectedYear) ?><?php else: ?>Your Department Stock Summary - All Years<?php endif; ?></div>
      <div class="card-body">
        <?php foreach ($deptUsage as $d): ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between small fw-semibold">
              <span><?= e($d['code']) ?> - <?= e($d['name']) ?></span>
              <span>Requested <?= e($d['requested_qty']) ?> | Issued <?= e($d['issued_qty']) ?></span>
            </div>
            <div class="usage-track mt-1">
              <div class="usage-bar requested" style="width: <?= max(2, ((float)$d['requested_qty'] / $maxDept) * 100) ?>%"></div>
              <div class="usage-bar issued" style="width: <?= max(2, ((float)$d['issued_qty'] / $maxDept) * 100) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="small text-muted">Blue shows requested quantity. Green shows issued quantity.</div>
      </div>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card metric-card h-100">
      <div class="card-header bg-white fw-semibold"><?= $role === 'ADMIN' ? 'All-Year Request vs Issue Trend' : 'Your All-Year Request vs Issue History' ?></div>
      <div class="card-body">
        <?php foreach ($yearTrend as $y): ?>
          <div class="year-row">
            <div class="year-label"><?= e((string)$y['report_year']) ?></div>
            <div class="year-bars">
              <div class="usage-bar requested" style="width: <?= max(2, ((float)$y['requested_qty'] / $maxYear) * 100) ?>%"></div>
              <div class="usage-bar issued" style="width: <?= max(2, ((float)$y['issued_qty'] / $maxYear) * 100) ?>%"></div>
            </div>
            <div class="year-value"><?= e($y['issued_qty']) ?> issued</div>
          </div>
        <?php endforeach; ?>
        <?php if (!$yearTrend): ?><div class="text-muted">No request history yet.</div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="card metric-card">
  <div class="card-header bg-white fw-semibold">Recent Transactions</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Date</th><th>Item</th><th>Type</th><th>Qty</th><th>Balance</th><th>By</th></tr></thead>
      <tbody>
      <?php foreach ($recent as $r): ?>
        <tr><td><?= e($r['created_at']) ?></td><td><?= e($r['item_name']) ?></td><td><span class="badge text-bg-secondary"><?= e($r['type']) ?></span></td><td><?= e($r['quantity']) ?></td><td><?= e($r['new_quantity']) ?></td><td><?= e($r['name']) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php render_footer(); ?>
