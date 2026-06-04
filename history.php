<?php
require_once 'includes/db.php';
<<<<<<< HEAD
requireAdmin(); // Admin-only page

$conn      = getDBConnection();
$pageTitle = 'Audit History – OFV IMS';
=======
requireLogin();

$conn = getDBConnection();
$pageTitle = 'Inventory History – OFV IMS';
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab

// Filters
$filter_action = trim($_GET['action_type'] ?? '');
$filter_date   = trim($_GET['date_filter'] ?? '');
$search        = trim($_GET['search'] ?? '');
<<<<<<< HEAD
$page          = max(1, intval($_GET['page'] ?? 1));
$per_page      = 20;
$offset        = ($page - 1) * $per_page;

$where = "WHERE 1=1";
if ($filter_action) {
    $fa     = $conn->real_escape_string($filter_action);
    $where .= " AND action_type = '$fa'";
}
if ($filter_date) {
    $fd     = $conn->real_escape_string($filter_date);
    $where .= " AND DATE(action_date) = '$fd'";
}
if ($search) {
    $s      = $conn->real_escape_string($search);
    $where .= " AND (product_name LIKE '%$s%' OR product_code LIKE '%$s%' OR changed_by LIKE '%$s%')";
}

$total_rows  = $conn->query("SELECT COUNT(*) as c FROM inventory_history $where")->fetch_assoc()['c'];
$total_pages = ceil($total_rows / $per_page);
$history     = $conn->query("SELECT * FROM inventory_history $where ORDER BY action_date DESC LIMIT $per_page OFFSET $offset");

// Summary counts
$counts = [];
$res    = $conn->query("SELECT action_type, COUNT(*) as c FROM inventory_history GROUP BY action_type");
while ($r = $res->fetch_assoc()) $counts[$r['action_type']] = $r['c'];
?>
<?php include 'includes/header.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">Audit History</h1>
        <p class="page-subtitle">Complete log of all inventory changes and actions</p>
    </div>
    <a href="home.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>
</div>

<!-- STAT CARDS -->
<div class="stats-row">
    <div class="stat-card accent">
        <div class="stat-icon-wrap accent"><i class="fas fa-list-alt"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format(array_sum($counts)) ?></div>
            <div class="stat-label">Total Actions Logged</div>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon-wrap success"><i class="fas fa-plus-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($counts['ADD'] ?? 0) ?></div>
            <div class="stat-label">Products Added</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon-wrap warning"><i class="fas fa-edit"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format(($counts['UPDATE'] ?? 0) + ($counts['INVENTORY_UPDATE'] ?? 0)) ?></div>
            <div class="stat-label">Updates &amp; Edits</div>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon-wrap danger"><i class="fas fa-trash-alt"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($counts['DELETE'] ?? 0) ?></div>
            <div class="stat-label">Products Deleted</div>
        </div>
    </div>
</div>

<!-- HISTORY TABLE -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-history"></i>
            Action Log
        </div>
        <span class="badge badge-neutral"><?= number_format($total_rows) ?> records</span>
    </div>

    <!-- Filter Toolbar -->
    <div class="table-toolbar">
        <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;align-items:center;">
            <div class="search-field" style="flex:1;min-width:200px;max-width:340px;">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search product, code, user..."
                    value="<?= htmlspecialchars($search) ?>"
                    aria-label="Search history"
                >
            </div>

            <select name="action_type" class="form-control" style="width:auto;min-width:160px;" aria-label="Filter by action type">
                <option value="">All Action Types</option>
                <option value="ADD"              <?= $filter_action==='ADD'              ?'selected':''?>>Add</option>
                <option value="UPDATE"           <?= $filter_action==='UPDATE'           ?'selected':''?>>Edit / Update</option>
                <option value="DELETE"           <?= $filter_action==='DELETE'           ?'selected':''?>>Delete</option>
                <option value="INVENTORY_UPDATE" <?= $filter_action==='INVENTORY_UPDATE' ?'selected':''?>>Inventory Refresh</option>
            </select>

            <input
                type="date"
                name="date_filter"
                class="form-control"
                style="width:auto;"
                value="<?= htmlspecialchars($filter_date) ?>"
                aria-label="Filter by date"
            >

            <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>

            <?php if ($filter_action || $filter_date || $search): ?>
            <a href="history.php" class="btn btn-ghost btn-sm">
                <i class="fas fa-times"></i>
                Clear
            </a>
=======

$sql = "SELECT * FROM inventory_history WHERE 1=1";
if ($filter_action) {
    $fa = $conn->real_escape_string($filter_action);
    $sql .= " AND action_type = '$fa'";
}
if ($filter_date) {
    $fd = $conn->real_escape_string($filter_date);
    $sql .= " AND DATE(action_date) = '$fd'";
}
if ($search) {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (product_name LIKE '%$s%' OR product_code LIKE '%$s%' OR changed_by LIKE '%$s%')";
}
$sql .= " ORDER BY action_date DESC LIMIT 200";
$history = $conn->query($sql);
$total   = $history ? $history->num_rows : 0;

// Summary counts
$counts = [];
$res = $conn->query("SELECT action_type, COUNT(*) as c FROM inventory_history GROUP BY action_type");
while ($row = $res->fetch_assoc()) $counts[$row['action_type']] = $row['c'];
?>
<?php include 'includes/header.php'; ?>

<div class="page-title-bar">
    <div>
        <h2>📋 Inventory Update History</h2>
        <div class="breadcrumb"><a href="home.php" style="color:var(--saffron);text-decoration:none;">Home</a> &rsaquo; <span>History</span></div>
    </div>
    <a href="home.php" class="btn btn-navy">← Back to Home</a>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card s1">
        <span class="stat-icon">📊</span>
        <div class="stat-label">Total Actions</div>
        <div class="stat-value"><?= array_sum($counts) ?></div>
    </div>
    <div class="stat-card s2">
        <span class="stat-icon">➕</span>
        <div class="stat-label">Products Added</div>
        <div class="stat-value"><?= $counts['ADD'] ?? 0 ?></div>
    </div>
    <div class="stat-card s3">
        <span class="stat-icon">✏️</span>
        <div class="stat-label">Products Updated</div>
        <div class="stat-value"><?= ($counts['UPDATE'] ?? 0) + ($counts['INVENTORY_UPDATE'] ?? 0) ?></div>
    </div>
    <div class="stat-card s4">
        <span class="stat-icon">🗑️</span>
        <div class="stat-label">Products Deleted</div>
        <div class="stat-value"><?= $counts['DELETE'] ?? 0 ?></div>
    </div>
</div>

<!-- Filter Bar -->
<div class="table-card">
    <div class="table-card-header">
        <h3>📋 Action Log</h3>
    </div>

    <div class="table-search-bar">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;width:100%;align-items:center;">
            <input type="text" name="search" class="search-input"
                   placeholder="Search product, code, or user..."
                   value="<?= htmlspecialchars($search) ?>" style="flex:2;min-width:180px;">
            <select name="action_type" style="padding:9px 14px;border:1px solid #ddd;border-radius:8px;font-size:13px;background:#f8f9fa;outline:none;">
                <option value="">All Actions</option>
                <option value="ADD" <?= $filter_action==='ADD'?'selected':'' ?>>➕ Add</option>
                <option value="UPDATE" <?= $filter_action==='UPDATE'?'selected':'' ?>>✏️ Update</option>
                <option value="DELETE" <?= $filter_action==='DELETE'?'selected':'' ?>>🗑️ Delete</option>
                <option value="INVENTORY_UPDATE" <?= $filter_action==='INVENTORY_UPDATE'?'selected':'' ?>>🔄 Inventory Update</option>
            </select>
            <input type="date" name="date_filter" value="<?= htmlspecialchars($filter_date) ?>"
                   style="padding:9px 14px;border:1px solid #ddd;border-radius:8px;font-size:13px;background:#f8f9fa;outline:none;">
            <button type="submit" class="btn btn-navy btn-sm">🔍 Filter</button>
            <?php if ($filter_action || $filter_date || $search): ?>
            <a href="history.php" class="btn btn-sm" style="background:#6c757d;color:white;">✕ Clear</a>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
            <?php endif; ?>
        </form>
    </div>

<<<<<<< HEAD
    <!-- Table -->
    <div class="table-wrapper">
        <table class="data-table" aria-label="Audit history">
            <thead>
                <tr>
                    <th scope="col" style="width:44px;">#</th>
                    <th scope="col">Date &amp; Time</th>
                    <th scope="col">Action</th>
                    <th scope="col">Product</th>
                    <th scope="col">Code</th>
                    <th scope="col" style="text-align:right;">Old Qty</th>
                    <th scope="col" style="text-align:right;">New Qty</th>
                    <th scope="col" style="text-align:right;">Change</th>
                    <th scope="col">Updated By</th>
                    <th scope="col">Remarks</th>
=======
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Old Qty</th>
                    <th>New Qty</th>
                    <th>Change</th>
                    <th>Updated By</th>
                    <th>Remarks</th>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
                </tr>
            </thead>
            <tbody>
            <?php
<<<<<<< HEAD
            $row_num = $offset + 1;

            $action_badges = [
                'ADD'              => ['badge-success', 'ADD'],
                'UPDATE'           => ['badge-accent',  'EDIT'],
                'DELETE'           => ['badge-danger',  'DELETE'],
                'INVENTORY_UPDATE' => ['badge-warning', 'REFRESH'],
            ];

            if ($history && $history->num_rows > 0):
                while ($row = $history->fetch_assoc()):
                    $ab  = $action_badges[$row['action_type']] ?? ['badge-neutral', $row['action_type']];
                    $chg = $row['new_quantity'] - $row['old_quantity'];
                    if ($chg > 0)      { $chg_str = '+' . number_format($chg); $chg_color = 'var(--color-success)'; }
                    elseif ($chg < 0)  { $chg_str = number_format($chg);       $chg_color = 'var(--color-danger)'; }
                    else               { $chg_str = '—';                        $chg_color = 'var(--color-text-subtle)'; }
            ?>
            <tr>
                <td class="cell-muted"><?= $row_num++ ?></td>
                <td>
                    <div style="font-size:13px;color:var(--color-text-body);">
                        <?= date('d M Y', strtotime($row['action_date'])) ?>
                    </div>
                    <div class="text-muted text-sm">
                        <?= date('H:i:s', strtotime($row['action_date'])) ?>
                    </div>
                </td>
                <td>
                    <span class="badge <?= $ab[0] ?>"><?= $ab[1] ?></span>
                </td>
                <td>
                    <span class="fw-600" style="color:var(--color-text-primary);">
                        <?= htmlspecialchars($row['product_name'] ?? '—') ?>
                    </span>
                </td>
                <td class="cell-mono"><?= htmlspecialchars($row['product_code'] ?? '—') ?></td>
                <td style="text-align:right;font-variant-numeric:tabular-nums;" class="cell-muted">
                    <?= number_format($row['old_quantity']) ?>
                </td>
                <td style="text-align:right;font-variant-numeric:tabular-nums;font-weight:600;color:var(--color-text-primary);">
                    <?= number_format($row['new_quantity']) ?>
                </td>
                <td style="text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:<?= $chg_color ?>;">
                    <?= $chg_str ?>
                </td>
                <td>
                    <span class="badge badge-neutral">
                        <i class="fas fa-user" style="font-size:9px;"></i>
                        <?= htmlspecialchars($row['changed_by']) ?>
                    </span>
                </td>
                <td class="cell-muted text-sm"><?= htmlspecialchars($row['remarks'] ?: '—') ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="10">
                    <div class="table-empty">
                        <i class="fas fa-history"></i>
                        <p>No history records found for the selected filters.</p>
                    </div>
=======
            $i = 1;
            $action_icons = [
                'ADD'              => ['icon'=>'➕','color'=>'#155724','bg'=>'#d4edda'],
                'UPDATE'           => ['icon'=>'✏️','color'=>'#004085','bg'=>'#cce5ff'],
                'DELETE'           => ['icon'=>'🗑️','color'=>'#721c24','bg'=>'#f8d7da'],
                'INVENTORY_UPDATE' => ['icon'=>'🔄','color'=>'#856404','bg'=>'#fff3cd'],
            ];
            if ($history && $history->num_rows > 0):
                while ($row = $history->fetch_assoc()):
                    $ai  = $action_icons[$row['action_type']] ?? ['icon'=>'❓','color'=>'#555','bg'=>'#eee'];
                    $chg = $row['new_quantity'] - $row['old_quantity'];
                    $chg_str = ($chg > 0) ? "+$chg" : "$chg";
                    $chg_color = ($chg > 0) ? 'var(--green)' : (($chg < 0) ? '#dc3545' : '#999');
            ?>
            <tr>
                <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                <td style="font-size:12px;white-space:nowrap;">
                    <?= date('d M Y', strtotime($row['action_date'])) ?><br>
                    <span style="color:#999;"><?= date('h:i:s A', strtotime($row['action_date'])) ?></span>
                </td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:5px;background:<?= $ai['bg'] ?>;color:<?= $ai['color'] ?>;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                        <?= $ai['icon'] ?> <?= str_replace('_', ' ', $row['action_type']) ?>
                    </span>
                </td>
                <td><strong><?= htmlspecialchars($row['product_name'] ?? '—') ?></strong></td>
                <td><span class="product-code"><?= htmlspecialchars($row['product_code'] ?? '—') ?></span></td>
                <td style="text-align:center;"><?= number_format($row['old_quantity']) ?></td>
                <td style="text-align:center;font-weight:600;"><?= number_format($row['new_quantity']) ?></td>
                <td style="text-align:center;font-weight:700;color:<?= $chg_color ?>;"><?= $chg_str ?></td>
                <td>
                    <span style="background:#f0f0ff;color:var(--navy);padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">
                        👤 <?= htmlspecialchars($row['changed_by']) ?>
                    </span>
                </td>
                <td style="font-size:12px;color:#666;"><?= htmlspecialchars($row['remarks'] ?: '—') ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="10" style="text-align:center;padding:40px;color:#999;">
                    📭 No history records found.
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
<<<<<<< HEAD

    <!-- Pagination + Footer -->
    <div class="card-footer">
        <span>
            Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format(min($offset + $per_page, $total_rows)) ?>
            of <?= number_format($total_rows) ?> records
        </span>

        <?php if ($total_pages > 1): ?>
        <nav aria-label="Pagination" style="display:flex;gap:4px;align-items:center;">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&action_type=<?= urlencode($filter_action) ?>&date_filter=<?= urlencode($filter_date) ?>"
               class="btn btn-secondary btn-sm btn-icon" aria-label="Previous">
                <i class="fas fa-chevron-left" style="font-size:11px;"></i>
            </a>
            <?php endif; ?>

            <?php for ($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&action_type=<?= urlencode($filter_action) ?>&date_filter=<?= urlencode($filter_date) ?>"
               class="btn btn-sm <?= $i===$page?'btn-primary':'btn-secondary'?>"
               style="min-width:32px;justify-content:center;"
               <?= $i===$page?'aria-current="page"':''?>>
                <?= $i ?>
            </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&action_type=<?= urlencode($filter_action) ?>&date_filter=<?= urlencode($filter_date) ?>"
               class="btn btn-secondary btn-sm btn-icon" aria-label="Next">
                <i class="fas fa-chevron-right" style="font-size:11px;"></i>
            </a>
            <?php endif; ?>
        </nav>
        <?php else: ?>
        <span class="text-muted text-sm">Queried at: <?= date('d M Y, H:i') ?></span>
        <?php endif; ?>
=======
    <div class="table-footer">
        <span>Showing <?= $total ?> record(s)</span>
        <span>Filtered: <?= date('d M Y, h:i A') ?></span>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
