<?php
require_once 'includes/db.php';
requireAdmin(); // Admin-only page

$conn      = getDBConnection();
$pageTitle = 'Low Stock Alert – OFV IMS';

$threshold = max(1, intval($_GET['threshold'] ?? 50));
$products  = $conn->query("SELECT * FROM products WHERE quantity < $threshold AND status='active' ORDER BY quantity ASC");
$total     = $products ? $products->num_rows : 0;
$critical  = $conn->query("SELECT COUNT(*) as c FROM products WHERE quantity < 10 AND status='active'")->fetch_assoc()['c'];
$zero      = $conn->query("SELECT COUNT(*) as c FROM products WHERE quantity = 0 AND status='active'")->fetch_assoc()['c'];
?>
<?php include 'includes/header.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">Low Stock Alert</h1>
        <p class="page-subtitle">Products requiring immediate restocking attention</p>
    </div>
    <a href="home.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>
</div>

<!-- STAT CARDS -->
<div class="stats-row" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card warning">
        <div class="stat-icon-wrap warning"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($total) ?></div>
            <div class="stat-label">Below Threshold (<?= $threshold ?> units)</div>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon-wrap danger"><i class="fas fa-fire"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($critical) ?></div>
            <div class="stat-label">Critical (Below 10 units)</div>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon-wrap danger"><i class="fas fa-ban"></i></div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($zero) ?></div>
            <div class="stat-label">Out of Stock (0 units)</div>
        </div>
    </div>
</div>

<?php if ($total > 0): ?>
<div class="alert alert-warning" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <div class="alert-content">
        <strong><?= number_format($total) ?> product<?= $total !== 1 ? 's' : '' ?> below threshold.</strong>
        Immediate restocking is recommended to maintain operational readiness.
    </div>
</div>
<?php else: ?>
<div class="alert alert-success" role="status">
    <i class="fas fa-check-circle"></i>
    <div class="alert-content">
        <strong>All clear.</strong>
        All active products are above the threshold of <?= number_format($threshold) ?> units.
    </div>
</div>
<?php endif; ?>

<!-- TABLE CARD -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-exclamation-triangle" style="color:var(--color-warning);"></i>
            Low Stock Products
        </div>
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <label for="thresholdInput" class="form-label mb-0" style="font-size:12px;white-space:nowrap;margin-bottom:0;">
                Threshold:
            </label>
            <input
                type="number"
                id="thresholdInput"
                name="threshold"
                class="form-control"
                value="<?= $threshold ?>"
                min="1"
                max="9999"
                style="width:90px;"
                aria-label="Low stock threshold value"
            >
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="data-table" aria-label="Low stock products">
            <thead>
                <tr>
                    <th scope="col" style="width:44px;">#</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Code</th>
                    <th scope="col">Category</th>
                    <th scope="col" style="text-align:right;">Current Stock</th>
                    <th scope="col">Unit</th>
                    <th scope="col">Urgency</th>
                    <th scope="col" style="width:120px;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            if ($products && $products->num_rows > 0):
                while ($row = $products->fetch_assoc()):
                    $q = $row['quantity'];
                    if ($q === 0)    { $urgency_badge = 'badge-danger';  $urgency_text = 'Out of Stock'; }
                    elseif ($q < 10) { $urgency_badge = 'badge-danger';  $urgency_text = 'Critical'; }
                    else             { $urgency_badge = 'badge-warning'; $urgency_text = 'Low Stock'; }
            ?>
            <tr>
                <td class="cell-muted"><?= $i++ ?></td>
                <td>
                    <span class="fw-600" style="color:var(--color-text-primary);">
                        <?= htmlspecialchars($row['product_name']) ?>
                    </span>
                </td>
                <td class="cell-mono"><?= htmlspecialchars($row['product_code']) ?></td>
                <td class="cell-muted"><?= htmlspecialchars($row['category']) ?></td>
                <td style="text-align:right;font-variant-numeric:tabular-nums;">
                    <strong style="font-size:16px;color:var(--color-danger);"><?= number_format($q) ?></strong>
                </td>
                <td class="cell-muted"><?= htmlspecialchars($row['unit']) ?></td>
                <td>
                    <span class="badge <?= $urgency_badge ?>"><?= $urgency_text ?></span>
                </td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-up"></i>
                        Restock
                    </a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="8">
                    <div class="table-empty">
                        <i class="fas fa-check-circle" style="color:var(--color-success);"></i>
                        <p>No products are below the threshold of <?= number_format($threshold) ?> units.</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <span><?= number_format($total) ?> product<?= $total !== 1 ? 's' : '' ?> below the threshold of <?= number_format($threshold) ?> units</span>
        <span class="text-muted text-sm">Checked at: <?= date('d M Y, H:i') ?></span>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
                                     