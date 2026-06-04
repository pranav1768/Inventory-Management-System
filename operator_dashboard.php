<?php
/**
 * Operator Dashboard
 * Each section operator sees ONLY their own product data.
 */
require_once 'includes/db.php';
requireOperator();

$conn        = getDBConnection();
$pageTitle   = 'Operator Dashboard – OFV IMS';
$product_code = $_SESSION['product_code'] ?? '';
$section_name = $_SESSION['section']       ?? '';

// Fetch the product this operator owns
$stmt = $conn->prepare("SELECT * FROM products WHERE product_code = ? AND status='active'");
$stmt->bind_param("s", $product_code);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Last 10 history entries for this product
$hist_stmt = $conn->prepare(
    "SELECT * FROM inventory_history WHERE product_code = ? ORDER BY action_date DESC LIMIT 10"
);
$hist_stmt->bind_param("s", $product_code);
$hist_stmt->execute();
$history = $hist_stmt->get_result();
$hist_stmt->close();

// Total updates this operator has made
$upd_count = $conn->query(
    "SELECT COUNT(*) as c FROM inventory_history WHERE product_code='$product_code' AND action_type='OPERATOR_UPDATE'"
)->fetch_assoc()['c'] ?? 0;

// Section colour/icon map
$section_styles = [
    'OFV-556' => ['class'=>'s-556', 'color'=>'#D97706', 'icon'=>'fas fa-circle',     'label'=>'5.56mm Ammunition'],
    'OFV-762' => ['class'=>'s-762', 'color'=>'#7C3AED', 'icon'=>'fas fa-dot-circle', 'label'=>'7.62mm Ammunition'],
    'OFV-PRM' => ['class'=>'s-prm', 'color'=>'#0891B2', 'icon'=>'fas fa-bolt',       'label'=>'Primer Components'],
    'OFV-CAL' => ['class'=>'s-cal', 'color'=>'#059669', 'icon'=>'fas fa-cog',        'label'=>'Calibur Components'],
    'OFV-PKG' => ['class'=>'s-pkg', 'color'=>'#DB2777', 'icon'=>'fas fa-box',        'label'=>'Packing Materials'],
];
$style = $section_styles[$product_code] ?? ['class'=>'s-556','color'=>'#2563EB','icon'=>'fas fa-box','label'=>$section_name];
?>
<?php include 'includes/header.php'; ?>

<!-- SECTION BANNER -->
<div class="section-banner <?= $style['class'] ?>">
    <i class="<?= $style['icon'] ?>" style="color:<?= $style['color'] ?>;font-size:26px;"></i>
    <div>
        <div class="section-banner-title"><?= htmlspecialchars($section_name) ?> Section</div>
        <div class="section-banner-sub"><?= $style['label'] ?> &nbsp;&bull;&nbsp; Your inventory dashboard</div>
    </div>
    <a href="operator_update.php" class="btn btn-primary" style="margin-left:auto;">
        <i class="fas fa-edit"></i> Update Stock
    </a>
</div>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">My Section Dashboard</h1>
        <p class="page-subtitle">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
            &nbsp;&bull;&nbsp; Section: <strong><?= htmlspecialchars($section_name) ?></strong>
        </p>
    </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === '5'): ?>
<div class="alert alert-success" role="alert">
    <i class="fas fa-check-circle"></i>
    <div class="alert-content">Stock quantity updated successfully. The change has been submitted to the admin dashboard.</div>
</div>
<?php endif; ?>

<!-- STAT CARDS -->
<?php if ($product): ?>
<?php
$qty = $product['quantity'];
if ($qty >= 100)    { $stock_badge='badge-success'; $stock_text='In Stock'; $stat_class='success'; }
elseif ($qty >= 50) { $stock_badge='badge-warning'; $stock_text='Medium';   $stat_class='warning'; }
else                { $stock_badge='badge-danger';  $stock_text='Low Stock'; $stat_class='danger'; }
?>
<div class="stats-row" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card <?= $stat_class ?>">
        <div class="stat-icon-wrap <?= $stat_class ?>"><i class="fas fa-cubes"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($qty) ?></div>
            <div class="stat-label">Current Stock (<?= htmlspecialchars($product['unit']) ?>)</div>
        </div>
    </div>
    <div class="stat-card accent">
        <div class="stat-icon-wrap accent"><i class="fas fa-sync-alt"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($upd_count) ?></div>
            <div class="stat-label">Updates Made by You</div>
        </div>
    </div>
    <div class="stat-card accent">
        <div class="stat-icon-wrap accent"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= date('d M', strtotime($product['last_updated'])) ?></div>
            <div class="stat-label">Last Updated</div>
        </div>
    </div>
</div>

<!-- PRODUCT INFO CARD -->
<div class="card mb-3">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-box"></i> Product Information</div>
        <span class="badge <?= $stock_badge ?>"><?= $stock_text ?></span>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div>
                <div class="form-label">Product Name</div>
                <div style="font-size:16px;font-weight:700;color:var(--color-text-primary);"><?= htmlspecialchars($product['product_name']) ?></div>
            </div>
            <div>
                <div class="form-label">Product Code</div>
                <div style="font-family:var(--font-mono);font-size:15px;color:var(--color-navy-light);background:var(--color-accent-pale);padding:6px 12px;border-radius:6px;display:inline-block;"><?= htmlspecialchars($product['product_code']) ?></div>
            </div>
            <div>
                <div class="form-label">Category</div>
                <div><?= htmlspecialchars($product['category']) ?></div>
            </div>
            <div>
                <div class="form-label">Unit of Measure</div>
                <div><?= htmlspecialchars($product['unit']) ?></div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <div class="alert-content">No product is assigned to your section. Contact the administrator.</div>
</div>
<?php endif; ?>

<!-- RECENT HISTORY TABLE -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-history"></i> My Recent Updates</div>
        <span class="badge badge-neutral">Last 10 entries</span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" aria-label="Recent update history">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date &amp; Time</th>
                    <th>Action</th>
                    <th style="text-align:right;">Old Qty</th>
                    <th style="text-align:right;">New Qty</th>
                    <th style="text-align:right;">Change</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            if ($history && $history->num_rows > 0):
                while ($row = $history->fetch_assoc()):
                    $chg = $row['new_quantity'] - $row['old_quantity'];
                    if ($chg > 0)     { $cs='+'.number_format($chg); $cc='var(--color-success)'; }
                    elseif ($chg < 0) { $cs=number_format($chg);     $cc='var(--color-danger)'; }
                    else              { $cs='&mdash;';                $cc='var(--color-text-subtle)'; }
                    $ab = ['OPERATOR_UPDATE'=>['badge-accent','UPDATE'],'ADD'=>['badge-success','ADD'],'DELETE'=>['badge-danger','DELETE']];
                    $badge = $ab[$row['action_type']] ?? ['badge-neutral', $row['action_type']];
            ?>
            <tr>
                <td class="cell-muted"><?= $i++ ?></td>
                <td>
                    <div><?= date('d M Y', strtotime($row['action_date'])) ?></div>
                    <div class="text-sm text-muted"><?= date('H:i:s', strtotime($row['action_date'])) ?></div>
                </td>
                <td><span class="badge <?= $badge[0] ?>"><?= $badge[1] ?></span></td>
                <td style="text-align:right;" class="cell-muted"><?= number_format($row['old_quantity']) ?></td>
                <td style="text-align:right;font-weight:600;color:var(--color-text-primary);"><?= number_format($row['new_quantity']) ?></td>
                <td style="text-align:right;font-weight:700;color:<?= $cc ?>;"><?= $cs ?></td>
                <td class="cell-muted text-sm"><?= htmlspecialchars($row['remarks'] ?: '&mdash;') ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7"><div class="table-empty"><i class="fas fa-history"></i><p>No updates yet. Use the Update Stock button to log your first entry.</p></div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <span>Showing last 10 entries for <?= htmlspecialchars($product_code) ?></span>
        <span class="text-muted text-sm">All changes are visible to admin</span>
    </div>
</div>

<?php $conn->close(); include 'includes/footer.php'; ?>
