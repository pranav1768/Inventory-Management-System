<?php
require_once 'includes/db.php';
requireAdmin(); // Admin-only page

$conn      = getDBConnection();
$pageTitle = 'Edit Product – OFV IMS';
$errors    = [];

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: home.php"); exit(); }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status='active'");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$product) { header("Location: home.php"); exit(); }

$allowed_products = [
    '5.56 Bullet'       => ['unit' => 'Rounds',    'category' => 'Ammunition'],
    '7.62 Bullet'       => ['unit' => 'Rounds',    'category' => 'Ammunition'],
    'Primer'            => ['unit' => 'Pieces',    'category' => 'Ammunition Components'],
    'Calibur'           => ['unit' => 'Pieces',    'category' => 'Ammunition Components'],
    'Copper Brass'      => ['unit' => 'Kilograms', 'category' => 'Raw Materials'],
    'Packing Container' => ['unit' => 'Units',     'category' => 'Packaging'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['product_name'] ?? '');
    $code     = strtoupper(trim($_POST['product_code'] ?? ''));
    $qty      = intval($_POST['quantity'] ?? 0);
    $unit     = trim($_POST['unit'] ?? 'Units');
    $category = trim($_POST['category'] ?? '');
    $date     = trim($_POST['date_added'] ?? '');

    if (empty($name) || !array_key_exists($name, $allowed_products))
        $errors[] = 'Please select a valid product.';
    if (empty($code))  $errors[] = 'Product code is required.';
    if ($qty < 0)      $errors[] = 'Quantity cannot be negative.';
    if (empty($category)) $errors[] = 'Category is required.';

    if (empty($errors)) {
        $chk = $conn->prepare("SELECT id FROM products WHERE product_code = ? AND id != ?");
        $chk->bind_param("si", $code, $id);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors[] = "Product code <strong>" . htmlspecialchars($code) . "</strong> is already used by another product.";
        } else {
            $old_qty = $product['quantity'];
            $upd     = $conn->prepare("UPDATE products SET product_name=?, product_code=?, quantity=?, unit=?, category=?, date_added=? WHERE id=?");
            $upd->bind_param("ssisssi", $name, $code, $qty, $unit, $category, $date, $id);
            if ($upd->execute()) {
                logAction($conn, $id, $name, $code, 'UPDATE', $old_qty, $qty, $_SESSION['username'], 'Product details updated');
                $conn->close();
                header("Location: home.php?success=2");
                exit();
            } else {
                $errors[] = 'Update failed: ' . $conn->error;
            }
        }
    }
}

$productMetaJson  = json_encode($allowed_products);
$selected_product = $_POST['product_name'] ?? $product['product_name'];

$product_icons = [
    '5.56 Bullet'       => 'fas fa-circle',
    '7.62 Bullet'       => 'fas fa-dot-circle',
    'Primer'            => 'fas fa-bolt',
    'Calibur'           => 'fas fa-cog',
    'Copper Brass'      => 'fas fa-coins',
    'Packing Container' => 'fas fa-box',
];
?>
<?php include 'includes/header.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">Edit Product</h1>
        <p class="page-subtitle">
            Editing:&nbsp;
            <strong style="color:var(--color-text-primary);"><?= htmlspecialchars($product['product_name']) ?></strong>
            &nbsp;&bull;&nbsp;
            <span style="font-family:var(--font-mono);font-size:12px;color:var(--color-accent);">
                <?= htmlspecialchars($product['product_code']) ?>
            </span>
        </p>
    </div>
    <a href="home.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>
</div>

<?php if ($errors): ?>
<div class="alert alert-error" role="alert">
    <i class="fas fa-exclamation-circle"></i>
    <div class="alert-content">
        <strong>Please correct the following errors:</strong>
        <ul style="margin:6px 0 0 16px;padding:0;">
            <?php foreach ($errors as $e): ?>
            <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="alert alert-info" role="status">
    <i class="fas fa-info-circle"></i>
    <div class="alert-content">
        You are editing an existing product record. All changes will be logged in the audit history.
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-pencil-alt"></i>
            Update Product Information
        </div>
        <span class="badge badge-warning">Editing existing record</span>
    </div>

    <div class="card-body">
        <form method="POST" id="editForm" novalidate>

            <!-- PRODUCT TYPE SELECTOR -->
            <div class="form-group">
                <label class="form-label">
                    Product Name <span class="required">*</span>
                </label>
                <div class="product-grid" id="productGrid" role="listbox" aria-label="Select product type">
                    <?php foreach ($allowed_products as $pname => $pmeta):
                        $is_selected = ($selected_product === $pname);
                        $icon_class  = $product_icons[$pname] ?? 'fas fa-box';
                    ?>
                    <div class="product-tile <?= $is_selected ? 'selected' : '' ?>"
                         onclick="selectProduct('<?= htmlspecialchars($pname, ENT_QUOTES) ?>')"
                         data-product="<?= htmlspecialchars($pname, ENT_QUOTES) ?>"
                         role="option"
                         aria-selected="<?= $is_selected ? 'true' : 'false' ?>"
                         tabindex="0"
                         onkeydown="if(event.key==='Enter'||event.key===' ')selectProduct('<?= htmlspecialchars($pname, ENT_QUOTES) ?>')">
                        <span class="product-tile-icon"><i class="<?= $icon_class ?>"></i></span>
                        <div class="product-tile-name"><?= htmlspecialchars($pname) ?></div>
                        <div class="product-tile-unit"><?= $pmeta['unit'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="product_name" id="productNameInput"
                       value="<?= htmlspecialchars($selected_product) ?>">
                <div class="form-hint">
                    <i class="fas fa-check" style="color:var(--color-accent);"></i>&nbsp;
                    Currently selected: <strong><?= htmlspecialchars($selected_product) ?></strong>
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--color-border);margin:8px 0 22px;">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="productCode">
                        Product Code <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="productCode"
                        name="product_code"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['product_code'] ?? $product['product_code']) ?>"
                        style="text-transform:uppercase;font-family:var(--font-mono);"
                        maxlength="50"
                        required
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="categoryInput">Category</label>
                    <input
                        type="text"
                        id="categoryInput"
                        name="category"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['category'] ?? $product['category']) ?>"
                        readonly
                        aria-readonly="true"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="qtyInput">
                        Quantity <span class="required">*</span>
                        &nbsp;<span id="unitBadge" class="badge badge-accent" style="font-size:11px;vertical-align:middle;">
                            <?= htmlspecialchars($_POST['unit'] ?? $product['unit']) ?>
                        </span>
                    </label>
                    <input
                        type="number"
                        id="qtyInput"
                        name="quantity"
                        class="form-control"
                        min="0"
                        value="<?= htmlspecialchars($_POST['quantity'] ?? $product['quantity']) ?>"
                        style="font-variant-numeric:tabular-nums;"
                        required
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="unitInput">Unit of Measure</label>
                    <input
                        type="text"
                        id="unitInput"
                        name="unit"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['unit'] ?? $product['unit']) ?>"
                        readonly
                        aria-readonly="true"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="dateAdded">Date Added</label>
                    <input
                        type="date"
                        id="dateAdded"
                        name="date_added"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['date_added'] ?? $product['date_added']) ?>"
                    >
                </div>
                <div class="form-group"></div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
                <a href="home.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
const productMeta = <?= $productMetaJson ?>;

function selectProduct(name) {
    document.getElementById('productNameInput').value = name;
    document.querySelectorAll('.product-tile').forEach(el => {
        const isThis = el.dataset.product === name;
        el.classList.toggle('selected', isThis);
        el.setAttribute('aria-selected', isThis ? 'true' : 'false');
    });
    const meta = productMeta[name];
    if (meta) {
        document.getElementById('unitInput').value       = meta.unit;
        document.getElementById('unitBadge').textContent = meta.unit;
        document.getElementById('categoryInput').value   = meta.category;
    }
}
</script>

<?php
$conn->close();
include 'includes/footer.php';
?>
