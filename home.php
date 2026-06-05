<?php
require_once 'includes/db.php';
requireAdmin(); // Admin-only page — operators are redirected to operator_dashboard.php

$conn = getDBConnection();
$pageTitle = 'Dashboard – OFV Inventory Management System';

// Handle Update Inventory action
if (isset($_POST['confirm_update_inventory'])) {
    $conn->query("UPDATE products SET last_updated = NOW() WHERE status='active'");
    $products_res = $conn->query("SELECT * FROM products WHERE status='active'");
    while ($p = $products_res->fetch_assoc()) {
        logAction($conn, $p['id'], $p['product_name'], $p['product_code'],
                  'INVENTORY_UPDATE', $p['quantity'], $p['quantity'],
                  $_SESSION['username'], 'Full inventory update triggered');
    }
    header("Location: home.php?success=4");
    exit();
}

// Stats
$total_products = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='active'")->fetch_assoc()['c'];
$total_qty      = $conn->query("SELECT SUM(quantity) as s FROM products WHERE status='active'")->fetch_assoc()['s'] ?? 0;
$low_stock      = $conn->query("SELECT COUNT(*) as c FROM products WHERE quantity < 50 AND status='active'")->fetch_assoc()['c'];
$categories     = $conn->query("SELECT COUNT(DISTINCT category) as c FROM products WHERE status='active'")->fetch_assoc()['c'];

// Fetch products with search + pagination
$search   = trim($_GET['search'] ?? '');
$page     = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset   = ($page - 1) * $per_page;

$where = "WHERE status='active'";
if ($search) {
    $s = $conn->real_escape_string($search);
    $where .= " AND (product_name LIKE '%$s%' OR product_code LIKE '%$s%' OR category LIKE '%$s%')";
}

$total_rows    = $conn->query("SELECT COUNT(*) as c FROM products $where")->fetch_assoc()['c'];
$total_pages   = ceil($total_rows / $per_page);
$products      = $conn->query("SELECT * FROM products $where ORDER BY id DESC LIMIT $per_page OFFSET $offset");
?>
<?php include 'includes/header.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">Inventory Dashboard</h1>
        <p class="page-subtitle">Real-time overview of all stock levels and product activity</p>
    </div>
    <div class="page-actions">
        <a href="add_product.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Product
        </a>
        <button class="btn btn-secondary" onclick="document.getElementById('updateInvModal').classList.add('show')">
            <i class="fas fa-sync-alt"></i>
            Update Inventory
        </button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<?php
$alertMsgs = [
    '1' => ['Product added to inventory successfully.', 'success', 'fa-check-circle'],
    '2' => ['Product details updated successfully.',   'success', 'fa-check-circle'],
    '3' => ['Product removed from inventory.',         'info',    'fa-info-circle'],
    '4' => ['Inventory records refreshed successfully.','success','fa-check-circle'],
];
$al = $alertMsgs[$_GET['success']] ?? ['Action completed.','info','fa-info-circle'];
?>
<div class="alert alert-<?= $al[1] ?>" role="alert">
    <i class="fas <?= $al[2] ?>"></i>
    <div class="alert-content"><?= $al[0] ?></div>
</div>
<?php endif; ?>

<!-- STAT CARDS -->
<div class="stats-row">
    <div class="stat-card accent">
        <div class="stat-icon-wrap accent">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($total_products) ?></div>
            <div class="stat-label">Total Products</div>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon-wrap success">
            <i class="fas fa-cubes"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($total_qty) ?></div>
            <div class="stat-label">Total Units in Stock</div>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon-wrap danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($low_stock) ?></div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon-wrap warning">
            <i class="fas fa-tag"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($categories) ?></div>
            <div class="stat-label">Categories</div>
        </div>
    </div>
</div>

<!-- PRODUCT TABLE CARD -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-table"></i>
            Product Inventory
        </div>
        <div class="flex gap-2">
            <a href="history.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-history"></i>
                View Audit Log
            </a>
            <a href="low_stock.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-exclamation-triangle"></i>
                Low Stock
            </a>
        </div>
    </div>

    <!-- Table Toolbar -->
    <div class="table-toolbar">
        <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;align-items:center;">
            <div class="search-field" style="flex:1;min-width:220px;max-width:400px;">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by name, code or category..."
                    value="<?= htmlspecialchars($search) ?>"
                    aria-label="Search products"
                >
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
            <a href="home.php" class="btn btn-ghost btn-sm">
                <i class="fas fa-times"></i>
                Clear
            </a>
            <?php endif; ?>
        </form>
        <div class="text-muted text-sm">
            <?= number_format($total_rows) ?> record<?= $total_rows !== 1 ? 's' : '' ?>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="data-table" aria-label="Product inventory">
            <thead>
                <tr>
                    <th scope="col" style="width:44px;">#</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Code</th>
                    <th scope="col">Category</th>
                    <th scope="col" style="text-align:right;">Quantity</th>
                    <th scope="col">Unit</th>
                    <th scope="col">Date Added</th>
                    <th scope="col">Last Updated</th>
                    <th scope="col">Status</th>
                    <th scope="col" style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $row_num = $offset + 1;
            if ($products && $products->num_rows > 0):
                while ($row = $products->fetch_assoc()):
                    $qty = $row['quantity'];
                    if ($qty >= 100)     { $badge_class = 'badge-success'; $badge_text = 'In Stock'; }
                    elseif ($qty >= 50)  { $badge_class = 'badge-warning'; $badge_text = 'Medium'; }
                    else                 { $badge_class = 'badge-danger';  $badge_text = 'Low Stock'; }
            ?>
            <tr>
                <td class="cell-muted"><?= $row_num++ ?></td>
                <td>
                    <span class="fw-600" style="color:var(--color-text-primary);">
                        <?= htmlspecialchars($row['product_name']) ?>
                    </span>
                </td>
                <td class="cell-mono"><?= htmlspecialchars($row['product_code']) ?></td>
                <td class="cell-muted"><?= htmlspecialchars($row['category']) ?></td>
                <td style="text-align:right;font-variant-numeric:tabular-nums;font-weight:600;color:var(--color-text-primary);">
                    <?= number_format($qty) ?>
                </td>
                <td class="cell-muted"><?= htmlspecialchars($row['unit']) ?></td>
                <td class="cell-muted"><?= date('d M Y', strtotime($row['date_added'])) ?></td>
                <td class="cell-muted"><?= date('d M Y, H:i', strtotime($row['last_updated'])) ?></td>
                <td>
                    <span class="badge <?= $badge_class ?>"><?= $badge_text ?></span>
                </td>
                <td>
                    <div class="flex gap-2">
                        <a href="edit_product.php?id=<?= $row['id'] ?>"
                           class="btn btn-secondary btn-sm btn-icon"
                           title="Edit <?= htmlspecialchars($row['product_name']) ?>"
                           aria-label="Edit <?= htmlspecialchars($row['product_name']) ?>">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm btn-icon"
                            onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['product_name'], ENT_QUOTES) ?>')"
                            title="Delete <?= htmlspecialchars($row['product_name']) ?>"
                            aria-label="Delete <?= htmlspecialchars($row['product_name']) ?>">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="10">
                    <div class="table-empty">
                        <i class="fas fa-box-open"></i>
                        <p>
                            <?php if ($search): ?>
                            No products found matching &ldquo;<?= htmlspecialchars($search) ?>&rdquo;.
                            <a href="home.php">Clear search</a>
                            <?php else: ?>
                            No products in inventory yet. <a href="add_product.php">Add your first product</a>.
                            <?php endif; ?>
                        </p>
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
            <?= $search ? '&nbsp;&bull;&nbsp; Filtered by &ldquo;' . htmlspecialchars($search) . '&rdquo;' : '' ?>
        </span>

        <?php if ($total_pages > 1): ?>
        <nav aria-label="Pagination" style="display:flex;gap:4px;align-items:center;">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
               class="btn btn-secondary btn-sm btn-icon" aria-label="Previous page">
                <i class="fas fa-chevron-left" style="font-size:11px;"></i>
            </a>
            <?php endif; ?>

            <?php
            $range_start = max(1, $page - 2);
            $range_end   = min($total_pages, $page + 2);
            for ($i = $range_start; $i <= $range_end; $i++):
            ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>"
               style="min-width:32px;justify-content:center;"
               <?= $i === $page ? 'aria-current="page"' : '' ?>>
                <?= $i ?>
            </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
               class="btn btn-secondary btn-sm btn-icon" aria-label="Next page">
                <i class="fas fa-chevron-right" style="font-size:11px;"></i>
            </a>
            <?php endif; ?>
        </nav>
        <?php else: ?>
        <span class="text-muted text-sm">Last refreshed: <?= date('d M Y, H:i') ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- UPDATE INVENTORY MODAL -->
<div class="modal-overlay" id="updateInvModal" role="dialog" aria-modal="true" aria-labelledby="updateInvTitle">
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-icon-wrap info">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="modal-title-group">
                <div class="modal-title" id="updateInvTitle">Update Inventory</div>
                <div class="modal-desc">
                    This will refresh all product timestamps and log a full inventory update
                    in the audit history. This action affects <strong>all active products</strong>.
                </div>
            </div>
        </div>
        <form method="POST">
            <input type="hidden" name="confirm_update_inventory" value="1">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="document.getElementById('updateInvModal').classList.remove('show')">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i>
                    Confirm Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-icon-wrap danger">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="modal-title-group">
                <div class="modal-title" id="deleteModalTitle">Delete Product</div>
                <div class="modal-desc">
                    Are you sure you want to delete <strong id="deleteProductName"></strong>?
                    This action cannot be undone and will be logged in the audit history.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary"
                onclick="document.getElementById('deleteModal').classList.remove('show')">
                Cancel
            </button>
            <a id="deleteConfirmBtn" href="#" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i>
                Delete Product
            </a>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteProductName').textContent = name;
        document.getElementById('deleteConfirmBtn').href = 'delete_product.php?id=' + id;
        document.getElementById('deleteModal').classList.add('show');
    }
</script>

<?php
$conn->close();
include 'includes/footer.php';
?>
