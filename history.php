<?php
require_once 'includes/db.php';
requireAdmin(); // Admin-only page

$conn      = getDBConnection();
$pageTitle = 'Audit History – OFV IMS';

// Filters
$filter_action = trim($_GET['action_type'] ?? '');
$filter_date   = trim($_GET['date_filter'] ?? '');
$search        = trim($_GET['search'] ?? '');
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
            <?php endif; ?>
        </form>
    </div>

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
                </tr>
            </thead>
            <tbody>
            <?php
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
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

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
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
