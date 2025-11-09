<?php
$page_title = 'Add Sale';
require_once('includes/load.php');
page_require_level(3);

if (isset($_POST['cart_data'])) {
    $cart_items = json_decode($_POST['cart_data'], true);

    if (!empty($cart_items)) {
        $errors = [];
        $success_count = 0;

        $txn_time = date("Y-m-d H:i:s");
        $txn_time_esc = $db->escape($txn_time);
        $current = current_user();
        $user_id_val = isset($current['id']) ? (int)$current['id'] : null;

        if ($user_id_val === null) {
            $create_txn_sql = "INSERT INTO transactions (txn_time, user_id, total) VALUES ('{$txn_time_esc}', NULL, 0)";
        } else {
            $create_txn_sql = "INSERT INTO transactions (txn_time, user_id, total) VALUES ('{$txn_time_esc}', '{$user_id_val}', 0)";
        }

        if (!$db->query($create_txn_sql)) {
            $session->msg('d', 'Failed to create transaction.');
            log_activity('Sale Failed', 'Could not create transaction record', 'add_sale.php');
            redirect($_SERVER['PHP_SELF'], false);
        }

        $txn_id = (int)$db->insert_id();
        $grand_total = 0.0;

        foreach ($cart_items as $item) {
            $product_id = (int)$item['id'];
            $quantity   = (float)$item['qty'];
            $price      = (float)$item['price'];

            $product = find_by_id('products', $product_id);
            if (!$product) {
                $errors[] = "Product ID {$product_id} not found.";
                log_activity('Sale Error', "Product ID {$product_id} not found", 'add_sale.php');
                continue;
            }

            // Check stock
            if ($quantity > (float)$product['quantity']) {
                $errors[] = "Not enough stock for {$product['name']} — Available: {$product['quantity']} {$product['unit']}";
                log_activity('Stock Error', "Insufficient stock for {$product['name']}", 'add_sale.php');
                continue;
            }

            $pid = $db->escape($product_id);
            $qty = $db->escape($quantity);
            $prc = $db->escape($price);

            $sql = "INSERT INTO sales (transaction_id, product_id, qty, price, date) 
                    VALUES ('{$txn_id}', '{$pid}', '{$qty}', '{$prc}', '{$txn_time_esc}')";

            if ($db->query($sql)) {
                update_product_qty($quantity, $product_id);
                $grand_total += ($quantity * $price);
                $success_count++;
            } else {
                $errors[] = "Failed to insert {$product['name']}: " . $db->error;
                log_activity('Sale Error', "Failed inserting {$product['name']}", 'add_sale.php');
            }
        }

        $gt = $db->escape($grand_total);
        $db->query("UPDATE transactions SET total = '{$gt}' WHERE id = '{$txn_id}'");

        if (!empty($errors)) {
            $session->msg('d', implode('<br>', $errors));
        }

        if ($success_count > 0) {
            $session->msg('s', "{$success_count} sale(s) added successfully");
            log_activity(
                'Sale Added',
                "{$success_count} product(s) sold. Transaction #{$txn_id} | Total ₱" . number_format($grand_total, 2),
                'recieptV2.php?id=' . $txn_id
            );

            redirect("recieptV2.php?id=" . (int)$txn_id, false);
        } else {
            $db->query("DELETE FROM transactions WHERE id = '{$txn_id}'");
            $session->msg('d', "No sales were recorded. Transaction removed.");
            log_activity('Sale Failed', 'Transaction removed — no valid sale recorded', 'add_sale.php');
            redirect($_SERVER['PHP_SELF'], false);
        }
    } else {
        $session->msg('d', "Cart is empty");
        log_activity('Sale Failed', 'Attempted sale with empty cart', 'add_sale.php');
    }
}

$products = join_product_table();
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<div class="row">
    <!-- Products -->
    <div class="col-md-8">
        <h4 style="color:white;">
            <i class="glyphicon glyphicon-barcode"></i> <strong>Products</strong>
        </h4>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" 
                     onclick="addToCart(
                        <?php echo $product['id']; ?>, 
                        '<?php echo $product['name']; ?>', 
                        <?php echo $product['sale_price']; ?>, 
                        '<?php echo $product['unit']; ?>', 
                        <?php echo $product['quantity']; ?>
                    )">
                    <img src="uploads/products/<?php echo $product['image'] ?: 'no_image.png'; ?>" alt="Product">
                    <p><strong><?php echo $product['name']; ?></strong></p>
                    <p class="text-success">
                        ₱<?php echo number_format($product['sale_price'], 2); ?> / <?php echo $product['unit']; ?>
                    </p>
                    <small>Stock: <?php echo $product['quantity'] . ' ' . $product['unit']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart -->
    <div class="col-md-4">
        <div class="cart-container">
            <h4 class="text-center"><i class="fa-solid fa-cart-plus"></i> <strong>Cart</strong></h4>
            <p class="text-muted">Click on a product to add it to the cart.</p>
            <form method="post">
                <table class="table table-bordered cart-table" id="cartTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cartBody"></tbody>
                </table>
                <div class="text-right mb-3">
                    <h5>Total: ₱<span id="grandTotal">0.00</span></h5>
                </div>
                <input type="hidden" name="cart_data" id="cartData">
                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-m rounded-pill px-3">
                        <i class="fa fa-check"></i> Complete Sale
                    </button>
                    <a href="salesV2.php" class="btn btn-outline-primary rounded-pill px-3 btn-m">
                        <i class="fa fa-list"></i> Manage Sales
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, name, price, unit, stock) {
    let existing = cart.find(item => item.id === id);

    if (stock <= 0) {
        if (confirm(`${name} is OUT OF STOCK. Do you want to request more?`)) {
            fetch("request_stock.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `product_id=${id}`
            })
            .then(res => res.text())
            .then(msg => alert(msg))
            .catch(err => alert("Error: " + err));
        }
        return;
    }

    if (existing) {
        if (existing.qty + 1 > stock) {
            alert(`Not enough stock. Available: ${stock} ${unit}`);
            return;
        }
        existing.qty++;
        existing.total = existing.qty * price;
    } else {
        cart.push({ id: id, name: name, price: price, unit: unit, qty: 1, total: price, stock: stock });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
}

function renderCart() {
    let cartBody = document.getElementById('cartBody');
    cartBody.innerHTML = '';
    let grandTotal = 0;

    cart.forEach(item => {
        grandTotal += item.total;
        cartBody.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>
                    <input type="number" min="1" max="${item.stock}" value="${item.qty}" 
                           onchange="updateQty(${item.id}, this.value)"> ${item.unit}
                </td>
                <td>₱${item.total.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs" 
                        onclick='if(confirm(${JSON.stringify(`Remove ${item.name} from cart?`)})) removeFromCart(${item.id})'>
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
    document.getElementById('cartData').value = JSON.stringify(cart);
}

function updateQty(id, qty) {
    let item = cart.find(p => p.id === id);
    qty = parseFloat(qty);
    if (qty > item.stock) {
        alert(`Not enough stock. Available: ${item.stock} ${item.unit}`);
        qty = item.stock;
    }
    if (qty < 1) qty = 1;
    item.qty = qty;
    item.total = item.qty * item.price;
    renderCart();
}
</script>

<?php include_once('layouts/footer.php'); ?>
