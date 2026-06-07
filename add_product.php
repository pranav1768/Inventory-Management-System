<?php
require_once 'includes/db.php';
requireAdmin(); // Admin-only page

$pageTitle = 'Add Product – OFV IMS';
$errors    = [];
$success   = false;

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
    $date     = trim($_POST['date_added'] ?? date('Y-m-d'));

    if (empty($name) || !array_key_exists($name, $allowed_products))
        $errors[] = 'Please select a valid product from the list.';
    if (empty($code))     $errors[] = 'Product code is required.';
    if ($qty < 0)         $errors[] = 'Quantity cannot be negative.';
    if (empty($category)) $errors[] = 'Category is required.';
    if (empty($date))     $errors[] = 'Date added is required.';

    if (empty($errors)) {
        $conn = getDBConnection();
        $chk  = $conn->prepare("SELECT id FROM products WHERE product_code = ?");
        $chk->bind_param("s", $code);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors[] = "Product code <strong>" . htmlspecialchars($code) . "</strong> already exists in the system.";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (product_name, product_code, quantity, unit, category, date_added) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssisss", $name, $code, $qty, $unit, $category, $date);
            if ($stmt->execute()) {
                $new_id = $conn->insert_id;
                logAction($conn, $new_id, $name, $code, 'ADD', 0, $qty, $_SESSION['username'], 'New product added to inventory');
                $conn->close();
                header("Location: home.php?success=1");
                exit();
            } else {
                $errors[] = 'Database error: ' . $conn->error;
            }
        }
        $conn->close();
    }
}

$productMetaJson  = json_encode($allowed_products);
$selected_product = $_POST['product_name'] ?? '';

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
        <h1 class="page-title">Add New Product</h1>
        <p class="page-subtitle">Select a product type and enter the inventory details</p>
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

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-plus-circle"></i>
            Product Details
        </div>
        <span class="badge badge-accent">Step 1: Select product, then fill details</span>
    </div>

    <div class="card-body">
        <form method="POST" id="addForm" novalidate>

            <!-- STEP 1: PRODUCT SELECTOR -->
            <div class="form-group">
                <label class="form-label">
                    Product Name <span class="required">*</span>
                </label>
                <div class="product-grid" id="productGrid" role="listbox" aria-label="Select product">
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
                <div id="productHint" class="form-hint" aria-live="polite">
                    <?= $selected_product
                        ? '<i class="fas fa-check" style="color:var(--color-accent);"></i>&nbsp; Selected: <strong>' . htmlspecialchars($selected_product) . '</strong>'
                        : 'Click a product tile above to select it' ?>
                </div>
            </div>

            <!-- STEP 2: PRODUCT FIELDS (revealed on selection) -->
            <div id="formFields" style="<?= $selected_product ? '' : 'opacity:0.35;pointer-events:none;' ?>;transition:opacity 0.3s ease;">

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
                            placeholder="e.g. OFV-001"
                            value="<?= htmlspecialchars($_POST['product_code'] ?? '') ?>"
                            style="text-transform:uppercase;font-family:var(--font-mono);"
                            maxlength="50"
                            required
                        >
                        <div class="form-hint">Must be unique across all products</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="categoryInput">Category</label>
                        <input
                            type="text"
                            id="categoryInput"
                            name="category"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['category'] ?? ($allowed_products[$selected_product]['category'] ?? '')) ?>"
                            readonly
                            aria-readonly="true"
                        >
                        <div class="form-hint">Auto-filled based on product selection</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="qtyInput">
                            Quantity <span class="required">*</span>
                            &nbsp;<span id="unitBadge" class="badge badge-accent" style="font-size:11px;vertical-align:middle;">
                                <?= htmlspecialchars($_POST['unit'] ?? ($allowed_products[$selected_product]['unit'] ?? 'Units')) ?>
                            </span>
                        </label>
                        <input
                            type="number"
                            id="qtyInput"
                            name="quantity"
                            class="form-control"
                            min="0"
                            placeholder="0"
                            value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($_POST['unit'] ?? ($allowed_products[$selected_product]['unit'] ?? 'Units')) ?>"
                            readonly
                            aria-readonly="true"
                        >
                        <div class="form-hint">Auto-filled based on product selection</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="dateAdded">
                            Date Added <span class="required">*</span>
                        </label>
                        <input
                            type="date"
                            id="dateAdded"
                            name="date_added"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['date_added'] ?? date('Y-m-d')) ?>"
                            required
                        >
                    </div>
                    <div class="form-group"><!-- spacer --></div>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus-circle"></i>
                        Add to Inventory
                    </button>
                    <a href="home.php" class="btn btn-secondary">Cancel</a>
                </div>

            </div><!-- /formFields -->
        </form>
    </div>
</div>

<script>
const productMeta = <?= $productMetaJson ?>;

function selectProduct(name) {
    // Update hidden input
    document.getElementById('productNameInput').value = name;

    // Update tiles
    document.querySelectorAll('.product-tile').forEach(el => {
        const isThis = el.dataset.product === name;
        el.classList.toggle('selected', isThis);
        el.setAttribute('aria-selected', isThis ? 'true' : 'false');
    });

    // Auto-fill fields
    const meta = productMeta[name];
    if (meta) {
        document.getElementById('unitInput').value    = meta.unit;
        document.getElementById('unitBadge').textContent = meta.unit;
        document.getElementById('categoryInput').value = meta.category;
    }

    // Show hint
    document.getElementById('productHint').innerHTML =
        '<i class="fas fa-check" style="color:var(--color-accent);"></i>&nbsp; Selected: <strong>' + name + '</strong>';

    // Reveal fields
    const fields = document.getElementById('formFields');
    fields.style.opacity       = '1';
    fields.style.pointerEvents = 'auto';

    // Focus quantity field
    setTimeout(() => document.getElementById('qtyInput').focus(), 120);
}

// Form validation
document.getElementById('addForm').addEventListener('submit', function(e) {
    if (!document.getElementById('productNameInput').value) {
        e.preventDefault();
        document.querySelectorAll('.product-tile').forEach(el => {
            el.style.boxShadow = '0 0 0 2px var(--color-danger)';
        });
        setTimeout(() => {
            document.querySelectorAll('.product-tile').forEach(el => {
                el.style.boxShadow = '';
            });
        }, 2000);
        document.getElementById('productHint').innerHTML =
            '<i class="fas fa-exclamation-circle" style="color:var(--color-danger);"></i>&nbsp; <span style="color:var(--color-danger);">Please select a product first.</span>';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
 