<?php
/**
 * Operator Update Stock
 * Allows an operator to update ONLY their section's product quantity.
 * The change is instantly visible on the admin dashboard.
 */
require_once 'includes/db.php';
requireOperator();

$conn         = getDBConnection();
$pageTitle    = 'Update Stock – OFV IMS';
$product_code = $_SESSION['product_code'] ?? '';
$section_name = $_SESSION['section']       ?? '';
$errors       = [];

// Fetch the operator's product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_code = ? AND status='active'");
$stmt->bind_param("s", $product_code);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: operator_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_type = trim($_POST['update_type'] ?? '');       // 'set' | 'add' | 'subtract'
    $qty_input   = intval($_POST['quantity_input'] ?? 0);
    $remarks     = trim($_POST['remarks'] ?? '');

    if (!in_array($update_type, ['set','add','subtract'])) $errors[] = 'Please select an update type.';
    if ($qty_input < 0) $errors[] = 'Quantity value cannot be negative.';

    if (empty($errors)) {
        $old_qty = $product['quantity'];
        $new_qty = $old_qty;

        if ($update_type === 'set')      $new_qty = $qty_input;
        elseif ($update_type === 'add')  $new_qty = $old_qty + $qty_input;
        elseif ($update_type === 'subtract') {
            $new_qty = $old_qty - $qty_input;
            if ($new_qty < 0) $errors[] = 'Cannot subtract more than current stock (' . number_format($old_qty) . ' ' . $product['unit'] . ').';
        }

        if (empty($errors)) {
            $upd = $conn->prepare("UPDATE products SET quantity=? WHERE id=?");
            $upd->bind_param("ii", $new_qty, $product['id']);
            $upd->execute();
            $upd->close();

            logAction(
                $conn,
                $product['id'],
                $product['product_name'],
                $product_code,
                'OPERATOR_UPDATE',
                $old_qty,
                $new_qty,
                $_SESSION['username'],
                $remarks ?: 'Stock updated by section operator'
            );

            $conn->close();
            header("Location: operator_dashboard.php?success=5");
            exit();
        }
    }
}

$section_styles = [
    'OFV-556' => ['class'=>'s-556','color'=>'#D97706','icon'=>'fas fa-circle'],
    'OFV-762' => ['class'=>'s-762','color'=>'#7C3AED','icon'=>'fas fa-dot-circle'],
    'OFV-PRM' => ['class'=>'s-prm','color'=>'#0891B2','icon'=>'fas fa-bolt'],
    'OFV-CAL' => ['class'=>'s-cal','color'=>'#059669','icon'=>'fas fa-cog'],
    'OFV-PKG' => ['class'=>'s-pkg','color'=>'#DB2777','icon'=>'fas fa-box'],
];
$style = $section_styles[$product_code] ?? ['class'=>'s-556','color'=>'#2563EB','icon'=>'fas fa-box'];
?>
<?php include 'includes/header.php'; ?>

<!-- SECTION BANNER -->
<div class="section-banner <?= $style['class'] ?>">
    <i class="<?= $style['icon'] ?>" style="color:<?= $style['color'] ?>;font-size:24px;"></i>
    <div>
        <div class="section-banner-title"><?= htmlspecialchars($section_name) ?> &mdash; Update Stock</div>
        <div class="section-banner-sub">Changes are applied immediately and visible to admin</div>
    </div>
</div>

<div class="page-header">
    <div class="page-header-text">
        <h1 class="page-title">Update Stock Quantity</h1>
        <p class="page-subtitle">
            Product: <strong style="color:var(--color-text-primary);"><?= htmlspecialchars($product['product_name']) ?></strong>
            &nbsp;&bull;&nbsp;
            <span style="font-family:var(--font-mono);font-size:12px;color:var(--color-accent);"><?= htmlspecialchars($product_code) ?></span>
            &nbsp;&bull;&nbsp;
            Current stock: <strong style="color:var(--color-text-primary);"><?= number_format($product['quantity']) ?> <?= htmlspecialchars($product['unit']) ?></strong>
        </p>
    </div>
    <a href="operator_dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<?php if ($errors): ?>
<div class="alert alert-error" role="alert">
    <i class="fas fa-exclamation-circle"></i>
    <div class="alert-content">
        <strong>Please fix the following:</strong>
        <ul style="margin:6px 0 0 16px;">
            <?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

    <!-- UPDATE FORM -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-edit"></i> Stock Update Form</div>
            <span class="badge badge-accent">Operator only</span>
        </div>
        <div class="card-body">
            <form method="POST" id="updateForm" novalidate>

                <!-- UPDATE TYPE -->
                <div class="form-group">
                    <label class="form-label">Update Type <span class="required">*</span></label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;" role="radiogroup" aria-label="Update type">
                        <?php
                        $types = [
                            'set'      => ['Set exact quantity',  'fas fa-equals',     'Replace current stock with a new value'],
                            'add'      => ['Add to stock',        'fas fa-plus',        'Increase stock by this amount'],
                            'subtract' => ['Subtract from stock', 'fas fa-minus',       'Decrease stock by this amount'],
                        ];
                        foreach ($types as $val => [$label, $icon, $desc]):
                            $sel = ($_POST['update_type'] ?? '') === $val;
                        ?>
                        <label class="product-tile <?= $sel?'selected':'' ?>" style="cursor:pointer;text-align:left;padding:14px;">
                            <input type="radio" name="update_type" value="<?= $val ?>" <?= $sel?'checked':'' ?> style="display:none;" onchange="selectUpdateType('<?= $val ?>')">
                            <div style="font-size:20px;margin-bottom:8px;color:var(--color-accent);"><i class="<?= $icon ?>"></i></div>
                            <div class="product-tile-name" style="text-align:left;"><?= $label ?></div>
                            <div class="product-tile-unit" style="text-align:left;margin-top:4px;text-transform:none;font-size:11px;"><?= $desc ?></div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- QUANTITY INPUT -->
                <div class="form-group">
                    <label class="form-label" for="qtyInput">
                        Quantity Value <span class="required">*</span>
                        &nbsp;<span class="badge badge-neutral"><?= htmlspecialchars($product['unit']) ?></span>
                    </label>
                    <input
                        type="number"
                        id="qtyInput"
                        name="quantity_input"
                        class="form-control"
                        min="0"
                        placeholder="Enter quantity"
                        value="<?= htmlspecialchars($_POST['quantity_input'] ?? '') ?>"
                        style="font-size:18px;font-weight:700;font-variant-numeric:tabular-nums;"
                        required
                    >
                    <div class="form-hint" id="qtyPreview">
                        Current stock: <strong><?= number_format($product['quantity']) ?> <?= htmlspecialchars($product['unit']) ?></strong>
                    </div>
                </div>

                <!-- REMARKS -->
                <div class="form-group">
                    <label class="form-label" for="remarksInput">Remarks / Reason</label>
                    <textarea
                        id="remarksInput"
                        name="remarks"
                        class="form-control"
                        rows="3"
                        placeholder="Optional: enter a reason or note for this update..."
                        style="resize:vertical;"
                    ><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
                    <div class="form-hint">This note will appear in the admin audit history.</div>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Submit Update
                    </button>
                    <a href="operator_dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- CURRENT STOCK INFO PANEL -->
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-info-circle"></i> Current Status</div>
            </div>
            <div class="card-body" style="text-align:center;padding:28px 20px;">
                <div style="font-size:52px;font-weight:800;color:var(--color-text-primary);letter-spacing:-0.04em;font-variant-numeric:tabular-nums;line-height:1;" id="currentQtyDisplay">
                    <?= number_format($product['quantity']) ?>
                </div>
                <div style="font-size:13px;color:var(--color-text-muted);margin-top:6px;font-weight:600;">
                    <?= htmlspecialchars($product['unit']) ?> in stock
                </div>
                <div style="margin-top:16px;">
                    <?php
                    $q = $product['quantity'];
                    if ($q >= 100)    echo '<span class="badge badge-success" style="font-size:13px;padding:5px 14px;">In Stock</span>';
                    elseif ($q >= 50) echo '<span class="badge badge-warning" style="font-size:13px;padding:5px 14px;">Medium Stock</span>';
                    else              echo '<span class="badge badge-danger"  style="font-size:13px;padding:5px 14px;">Low Stock</span>';
                    ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-calculator"></i> Live Preview</div>
            </div>
            <div class="card-body" style="padding:20px;">
                <div style="font-size:12.5px;color:var(--color-text-muted);margin-bottom:6px;">Result after update:</div>
                <div id="previewResult" style="font-size:28px;font-weight:700;color:var(--color-accent);font-variant-numeric:tabular-nums;">
                    &mdash;
                </div>
                <div style="font-size:11.5px;color:var(--color-text-subtle);margin-top:4px;" id="previewUnit">
                    Select update type and enter quantity above
                </div>
            </div>
        </div>

        <div class="alert alert-info mb-0" style="margin-bottom:0;">
            <i class="fas fa-shield-alt"></i>
            <div class="alert-content" style="font-size:12.5px;">
                <strong>Admin visibility</strong>
                Every stock change you make is immediately reflected on the admin dashboard and logged in the audit history.
            </div>
        </div>
    </div>
</div>

<script>
const currentQty  = <?= (int)$product['quantity'] ?>;
const unitLabel   = '<?= addslashes($product['unit']) ?>';
let selectedType  = '<?= htmlspecialchars($_POST['update_type'] ?? '') ?>';

function selectUpdateType(val) {
    selectedType = val;
    document.querySelectorAll('.product-tile').forEach(el => {
        const inp = el.querySelector('input[type=radio]');
        el.classList.toggle('selected', inp && inp.value === val);
    });
    updatePreview();
}

function updatePreview() {
    const qtyEl   = document.getElementById('qtyInput');
    const preview = document.getElementById('previewResult');
    const hint    = document.getElementById('previewUnit');
    const val     = parseInt(qtyEl.value) || 0;
    let result;

    if (!selectedType) { preview.textContent='—'; hint.textContent='Select update type first'; return; }
    if (isNaN(val))     { preview.textContent='—'; hint.textContent='Enter a number above'; return; }

    if (selectedType==='set')      result = val;
    else if(selectedType==='add')  result = currentQty + val;
    else                           result = currentQty - val;

    preview.textContent    = result.toLocaleString('en-IN');
    preview.style.color    = result < 50 ? 'var(--color-danger)' : result < 100 ? 'var(--color-warning)' : 'var(--color-success)';
    hint.textContent       = unitLabel + ' in stock after update';
}

document.getElementById('qtyInput').addEventListener('input', updatePreview);

// Init if coming back from validation error
if (selectedType) selectUpdateType(selectedType);
</script>

<?php $conn->close(); include 'includes/footer.php'; ?>
