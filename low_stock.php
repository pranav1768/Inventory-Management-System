<?php
require_once 'includes/db.php';
<<<<<<< HEAD
requireAdmin(); // Admin-only page

$conn      = getDBConnection();
$pageTitle = 'Low Stock Alert – OFV IMS';

$threshold = max(1, intval($_GET['threshold'] ?? 50));
=======
requireLogin();

$conn = getDBConnection();
$pageTitle = 'Low Stock Alert – OFV IMS';

$threshold = intval($_GET['threshold'] ?? 50);
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
$products  = $conn->query("SELECT * FROM products WHERE quantity < $threshold AND status='active' ORDER BY quantity ASC");
$total     = $products ? $products->num_rows : 0;
$critical  = $conn->query("SELECT COUNT(*) as c FROM products WHERE quantity < 10 AND status='active'")->fetch_assoc()['c'];
$zero      = $conn->query("SELECT COUNT(*) as c FROM products WHERE quantity = 0 AND status='active'")->fetch_assoc()['c'];
?>
<?php include 'includes/header.php'; ?>

<<<<<<< HEAD
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
=======
<div class="page-title-bar">
    <div>
        <h2>⚠️ Low Stock Alert</h2>
        <div class="breadcrumb"><a href="home.php" style="color:var(--saffron);text-decoration:none;">Home</a> &rsaquo; <span>Low Stock</span></div>
    </div>
    <a href="home.php" class="btn btn-navy">← Back to Home</a>
</div>

<div class="stats-grid">
    <div class="stat-card s4">
        <span class="stat-icon">⚠️</span>
        <div class="stat-label">Below Threshold (<?= $threshold ?>)</div>
        <div class="stat-value"><?= $total ?></div>
    </div>
    <div class="stat-card s4">
        <span class="stat-icon">🚨</span>
        <div class="stat-label">Critical (Below 10)</div>
        <div class="stat-value"><?= $critical ?></div>
    </div>
    <div class="stat-card s4">
        <span class="stat-icon">❌</span>
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value"><?= $zero ?></div>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
    </div>
</div>

<?php if ($total > 0): ?>
<<<<<<< HEAD
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
=======
<div class="alert alert-error">
    🚨 <strong><?= $total ?> product(s)</strong> are below the threshold of <?= $threshold ?> units.
    Immediate restocking is recommended.
</div>
<?php else: ?>
<div class="alert alert-success">
    ✅ All products are above the threshold of <?= $threshold ?> units. No action needed.
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header" style="background:linear-gradient(135deg,#8b0000,#c82333);">
        <h3>🚨 Low Stock Products</h3>
        <div class="header-actions">
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <label style="color:white;font-size:12px;">Threshold:</label>
                <input type="number" name="threshold" value="<?= $threshold ?>" min="1"
                       style="width:70px;padding:6px 10px;border-radius:6px;border:none;font-size:13px;">
                <button type="submit" class="btn btn-saffron btn-sm">Apply</button>
            </form>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Category</th>
                    <th>Current Quantity</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
                </tr>
            </thead>
            <tbody>
            <?php
<<<<<<< HEAD
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
=======
            $i=1;
            if ($products && $products->num_rows > 0):
                while ($row = $products->fetch_assoc()):
                    $q = $row['quantity'];
                    $urgency = ($q == 0) ? ['🔴 Out of Stock','#721c24','#f8d7da']
                             : ($q < 10 ? ['🟠 Critical','#7d3c00','#ffe5cc']
                             : ['🟡 Low','#856404','#fff3cd']);
            ?>
            <tr>
                <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                <td><strong><?= htmlspecialchars($row['product_name']) ?></strong></td>
                <td><span class="product-code"><?= htmlspecialchars($row['product_code']) ?></span></td>
                <td style="color:#555;font-size:12px;"><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <strong style="font-size:18px;color:#dc3545;"><?= $q ?></strong>
                </td>
                <td style="color:#777;font-size:12px;"><?= htmlspecialchars($row['unit']) ?></td>
                <td>
                    <span style="background:<?= $urgency[2] ?>;color:<?= $urgency[1] ?>;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                        <?= $urgency[0] ?>
                    </span>
                </td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-saffron btn-sm">✏️ Restock</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#999;">✅ No low stock products found.</td></tr>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
            <?php endif; ?>
            </tbody>
        </table>
    </div>
<<<<<<< HEAD

    <div class="card-footer">
        <span><?= number_format($total) ?> product<?= $total !== 1 ? 's' : '' ?> below the threshold of <?= number_format($threshold) ?> units</span>
        <span class="text-muted text-sm">Checked at: <?= date('d M Y, H:i') ?></span>
=======
    <div class="table-footer">
        <span><?= $total ?> product(s) below threshold</span>
        <span>Threshold: <?= $threshold ?> units</span>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
