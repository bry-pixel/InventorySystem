<?php
$page_title = 'Add Sale';
require_once('includes/load.php');
page_require_level(3);


/* =========================================================
   PROCESS SALE
   ========================================================= */

if (isset($_POST['cart_data'])) {
    $cart_items = json_decode($_POST['cart_data'], true);
    if (!empty($cart_items)) {
        $errors = [];
        $success_count = 0;
        $txn_time = date("Y-m-d H:i:s");
        $txn_time_esc = $db->escape($txn_time);
        $current = current_user();
        $user_id_val = isset($current['id'])
            ? (int)$current['id']
            : null;

        /* -------------------------------------------------
           CREATE TRANSACTION
        ------------------------------------------------- */
        $create_txn_sql =
            "INSERT INTO transactions
            (txn_time, user_id, total)
            VALUES
            (
                '{$txn_time_esc}',
                " .
            ($user_id_val !== null
                ? "'{$user_id_val}'"
                : "NULL" ) .", 0)";


        if (!$db->query($create_txn_sql)) {
            $session->msg(
                'd',
                'Failed to create transaction.'
            );
            log_activity(
                'Sale Failed',
                'Failed to create transaction record',
                'add_sale.php'
            );

            redirect(
                $_SERVER['PHP_SELF'],
                false
            );
        }
        $txn_id = (int)$db->insert_id();
        $grand_total = 0.0;

        /* -------------------------------------------------
           PROCESS EACH CART ITEM
        ------------------------------------------------- */
        foreach ($cart_items as $item) {
            $product_id = (int)$item['id'];
            $quantity   = (float)$item['qty'];
            $price      = (float)$item['price'];

            /* Get Product */

            $product = find_by_id(
                'products',
                $product_id
            );

            if (!$product) {
                $errors[] =
                    "Product ID {$product_id} not found.";


                log_activity(
                    'Sale Error',
                    "Product ID {$product_id} not found during sale",
                    'add_sale.php'
                );
                continue;
            }


            /* -------------------------------------------------
               STOCK CHECK
            ------------------------------------------------- */
            if (
                $quantity >
                (float)$product['quantity']
            ) {

                $errors[] =
                    "Not enough stock for {$product['name']} — " .
                    "Available: {$product['quantity']} {$product['unit']}";

                log_activity(
                    'Stock Error',
                    "Not enough stock for {$product['name']}",
                    'add_sale.php'
                );

                continue;
            }


            /* -------------------------------------------------
               ESCAPE VALUES
            ------------------------------------------------- */

            $pid = $db->escape($product_id);
            $qty = $db->escape($quantity);
            $prc = $db->escape($price);


            /* -------------------------------------------------
               INSERT SALE
            ------------------------------------------------- */

            $sql =
                "INSERT INTO sales
                (
                    transaction_id,
                    product_id,
                    qty,
                    price,
                    date
                )
                VALUES
                (
                    '{$txn_id}',
                    '{$pid}',
                    '{$qty}',
                    '{$prc}',
                    '{$txn_time_esc}'
                )";


            if ($db->query($sql)) {

                /* Update inventory */

                update_product_qty(
                    $quantity,
                    $product_id
                );


                /* Calculate total */

                $grand_total +=
                    ($quantity * $price);


                $success_count++;

            } else {

                $errors[] =
                    "Failed to insert {$product['name']}: " .
                    $db->error;


                log_activity(
                    'Sale Error',
                    "Failed to insert sale for {$product['name']}",
                    'add_sale.php'
                );
            }
        }
        /* -------------------------------------------------
           UPDATE TRANSACTION TOTAL
        ------------------------------------------------- */

        $gt = $db->escape($grand_total);

        $db->query(
            "UPDATE transactions
             SET total = '{$gt}'
             WHERE id = '{$txn_id}'"
        );
        /* -------------------------------------------------
           DISPLAY ERRORS
        ------------------------------------------------- */

        if (!empty($errors)) {
            $session->msg(
                'd',
                implode('<br>', $errors)
            );
        }


        /* -------------------------------------------------
           SALE SUCCESS
        ------------------------------------------------- */

        if ($success_count > 0) {

            $session->msg(
                's',
                "{$success_count} sale(s) added successfully"
            );


            log_activity(
                'Sale Added',
                "{$success_count} product(s) sold. " .
                "Transaction #{$txn_id} | Total ₱" .
                number_format($grand_total, 2),
                'reciept.php?id=' . $txn_id
            );


            redirect(
                "reciept.php?id=" .
                (int)$txn_id,
                false
            );

        } else {

            /* -------------------------------------------------
               REMOVE EMPTY TRANSACTION
            ------------------------------------------------- */

            $db->query(
                "DELETE FROM transactions
                 WHERE id = '{$txn_id}'"
            );


            $session->msg(
                'd',
                "No sales were recorded. Transaction removed."
            );


            log_activity(
                'Sale Failed',
                'Transaction removed — no sales recorded',
                'add_sale.php'
            );


            redirect(
                $_SERVER['PHP_SELF'],
                false
            );
        }

    } else {

        /* -------------------------------------------------
           EMPTY CART
        ------------------------------------------------- */

        $session->msg(
            'd',
            "Cart is empty"
        );


        log_activity(
            'Sale Failed',
            'Attempted sale with empty cart',
            'add_sale.php'
        );
    }
}


/* =========================================================
   GET PRODUCTS
   ========================================================= */

$products = join_product_table();

?>

<?php include_once('layouts/header.php'); ?>


<!-- =========================================================
     CUSTOM CSS
========================================================= -->

<style>

:root {

    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #eff6ff;

    --success: #16a34a;
    --success-dark: #15803d;

    --danger: #dc2626;
    --danger-dark: #b91c1c;

    --warning: #f59e0b;

    --dark: #0f172a;
    --text: #334155;
    --muted: #64748b;

    --white: #ffffff;

    --border: #e2e8f0;

    --background: #f8fafc;

    --shadow-sm:
        0 2px 8px rgba(15, 23, 42, 0.06);

    --shadow-md:
        0 8px 25px rgba(15, 23, 42, 0.10);

    --shadow-lg:
        0 20px 45px rgba(15, 23, 42, 0.16);

    --radius-sm: 8px;
    --radius-md: 14px;
    --radius-lg: 20px;
}


/* =========================================================
   PAGE
========================================================= */

body {

    background:
        linear-gradient(
            135deg,
            #f8fafc,
            #eef4ff
        );

    color: var(--text);

    font-family:
        "Poppins",
        "Segoe UI",
        Arial,
        sans-serif;
}


/* =========================================================
   MAIN ROW
========================================================= */

.row {
    margin-left: -10px;
    margin-right: -10px;
}


.row > [class*="col-"] {
    padding-left: 10px;
    padding-right: 10px;
}


/* =========================================================
   SECTION HEADER
========================================================= */

.col-md-8 > h4,
.col-md-4 > h4 {
    margin: 0 0 18px;
    padding:16px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--white);
    background:linear-gradient( 135deg, #1e40af,#2563eb);
    border-radius: var(--radius-md);
    box-shadow:var(--shadow-md);
    font-size: 18px;
    font-weight: 600;
}
.col-md-8 > h4 i { font-size: 19px;
}


/* =========================================================
   PRODUCT GRID
========================================================= */

.product-grid {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(175px, 1fr)
        );

    gap: 18px;

    padding:
        5px 2px 20px;
}


/* =========================================================
   PRODUCT CARD
========================================================= */

.product-card {

    position: relative;

    display: flex;

    flex-direction: column;

    align-items: center;

    min-height: 260px;

    padding: 18px;

    background:
        var(--white);

    border:
        1px solid var(--border);

    border-radius:
        var(--radius-lg);

    box-shadow:
        var(--shadow-sm);

    cursor: pointer;

    transition:
        transform .25s ease,
        box-shadow .25s ease,
        border-color .25s ease;
}


.product-card:hover {

    transform:
        translateY(-5px);

    border-color:
        rgba(37, 99, 235, .35);

    box-shadow:
        var(--shadow-lg);
}


.product-card:active {

    transform:
        scale(.98);
}


/* =========================================================
   PRODUCT IMAGE
========================================================= */

.product-card img {

    width: 125px;

    height: 125px;

    object-fit: contain;

    margin-bottom: 14px;

    padding: 10px;

    background:
        #f8fafc;

    border-radius:
        14px;

    transition:
        transform .25s ease;
}


.product-card:hover img {

    transform:
        scale(1.05);
}


/* =========================================================
   PRODUCT NAME
========================================================= */

.product-card p {

    margin:
        4px 0;

    text-align:
        center;
}


.product-card p strong {

    display:
        block;

    color:
        var(--dark);

    font-size:
        15px;

    font-weight:
        600;

    line-height:
        1.4;
}


/* =========================================================
   PRODUCT PRICE
========================================================= */

.product-card .text-success {

    color:
        var(--primary) !important;

    font-size:
        17px;

    font-weight:
        700;

    margin-top:
        8px;
}


/* =========================================================
   STOCK
========================================================= */

.product-card small {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    margin-top:
        auto;

    padding:
        5px 10px;

    color:
        var(--muted);

    background:
        #f1f5f9;

    border-radius:
        20px;

    font-size:
        12px;

    font-weight:
        500;
}


/* =========================================================
   CART
========================================================= */

.cart-container {

    position:
        sticky;

    top:
        20px;

    padding:
        22px;

    background:
        var(--white);

    border:
        1px solid var(--border);

    border-radius:
        var(--radius-lg);

    box-shadow:
        var(--shadow-md);
}


/* =========================================================
   CART TITLE
========================================================= */

.cart-container h4 {

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        9px;

    margin:
        0 0 8px;

    color:
        var(--dark);

    font-size:
        20px;

    font-weight:
        700;
}


.cart-container h4 i {

    color:
        var(--primary);
}


/* =========================================================
   CART DESCRIPTION
========================================================= */

.cart-container > p {

    margin-bottom:
        20px;

    color:
        var(--muted);

    text-align:
        center;

    font-size:
        13px;
}


/* =========================================================
   CART TABLE
========================================================= */

.cart-table {

    width:
        100%;

    margin-bottom:
        15px;

    border:
        none !important;
}


.cart-table thead th {

    padding:
        12px 8px;

    color:
        var(--muted);

    background:
        #f8fafc !important;

    border:
        none !important;

    border-bottom:
        1px solid var(--border) !important;

    font-size:
        11px;

    font-weight:
        700;

    text-transform:
        uppercase;

    letter-spacing:
        .05em;
}


.cart-table tbody td {

    padding:
        13px 8px;

    vertical-align:
        middle;

    color:
        var(--text);

    border-top:
        1px solid #f1f5f9 !important;

    font-size:
        13px;
}


.cart-table tbody tr {

    transition:
        background .2s ease;
}


.cart-table tbody tr:hover {

    background:
        #f8fafc;
}


/* =========================================================
   QUANTITY
========================================================= */

.cart-table input[type="number"] {

    width:
        65px !important;

    padding:
        7px 5px;

    color:
        var(--dark);

    background:
        var(--white);

    border:
        1px solid var(--border);

    border-radius:
        8px;

    text-align:
        center;

    font-weight:
        600;

    outline:
        none;

    transition:
        border-color .2s ease,
        box-shadow .2s ease;
}


.cart-table input[type="number"]:focus {

    border-color:
        var(--primary);

    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, .12);
}


/* =========================================================
   REMOVE BUTTON
========================================================= */

.cart-table .btn-danger {

    width:
        34px;

    height:
        34px;

    padding:
        0;

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    color:
        var(--danger);

    background:
        #fef2f2;

    border:
        1px solid #fecaca;

    border-radius:
        9px;

    transition:
        all .2s ease;
}


.cart-table .btn-danger:hover {

    color:
        var(--white);

    background:
        var(--danger);

    border-color:
        var(--danger);

    transform:
        translateY(-1px);
}


/* =========================================================
   TOTAL
========================================================= */

.cart-container .text-right {

    margin:
        18px 0;

    padding:
        18px;

    background:
        linear-gradient(
            135deg,
            #eff6ff,
            #f8fafc
        );

    border:
        1px solid #dbeafe;

    border-radius:
        12px;

    text-align:
        right;
}


.cart-container .text-right h5 {

    margin:
        0;

    color:
        var(--muted);

    font-size:
        13px;

    font-weight:
        600;

    text-transform:
        uppercase;
}


#grandTotal {

    display:
        inline-block;

    margin-left:
        5px;

    color:
        var(--primary);

    font-size:
        25px;

    font-weight:
        800;
}


/* =========================================================
   ACTION BUTTONS
========================================================= */

.cart-container form > .text-center {

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    flex-wrap:
        wrap;

    gap:
        8px;
}


.cart-container .btn {

    min-height:
        40px;

    padding:
        9px 17px;

    border-radius:
        10px !important;

    font-size:
        13px;

    font-weight:
        600;

    transition:
        all .2s ease;
}


/* Complete Sale */

.cart-container .btn-success {

    background:
        linear-gradient(
            135deg,
            var(--success),
            var(--success-dark)
        );

    border:
        none;

    box-shadow:
        0 5px 12px
        rgba(22, 163, 74, .20);
}


.cart-container .btn-success:hover {

    transform:
        translateY(-2px);

    box-shadow:
        0 8px 18px
        rgba(22, 163, 74, .28);
}


/* Manage Sales */

.cart-container .btn-outline-primary {

    color:
        var(--primary);

    background:
        var(--white);

    border:
        1px solid #bfdbfe;
}


.cart-container .btn-outline-primary:hover {

    color:
        var(--white);

    background:
        var(--primary);

    border-color:
        var(--primary);

    transform:
        translateY(-2px);
}


/* =========================================================
   CONFIRMATION MODAL
========================================================= */

.custom-modal-overlay {

    position:
        fixed !important;

    z-index:
        99999 !important;

    inset:
        0;

    display:
        none;

    align-items:
        center !important;

    justify-content:
        center !important;

    width:
        100vw !important;

    height:
        100vh !important;

    padding:
        20px;

    background:
        rgba(15, 23, 42, .65);

    backdrop-filter:
        blur(5px);

    opacity:
        0;

    transition:
        opacity .2s ease;
}


.custom-modal-overlay.show {

    display:
        flex !important;

    opacity:
        1;
}


/* Modal */

.custom-modal {

    position:
        relative;

    width:
        100%;

    max-width:
        420px;

    padding:
        32px 28px;

    background:
        var(--white);

    border:
        1px solid rgba(255,255,255,.5);

    border-radius:
        22px;

    text-align:
        center;

    box-shadow:
        0 25px 60px
        rgba(0,0,0,.25);

    animation:
        modalShow .25s ease-out;
}


@keyframes modalShow {

    from {

        opacity:
            0;

        transform:
            translateY(15px)
            scale(.96);
    }

    to {

        opacity:
            1;

        transform:
            translateY(0)
            scale(1);
    }
}


/* Modal Icon */

.custom-modal-icon {

    width:
        65px;

    height:
        65px;

    margin:
        0 auto 18px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    color:
        var(--danger);

    background:
        #fef2f2;

    border:
        1px solid #fecaca;

    border-radius:
        50%;

    font-size:
        25px;
}


/* Modal Heading */

.custom-modal h3 {

    margin:
        0 0 10px;

    color:
        var(--dark);

    font-size:
        22px;

    font-weight:
        700;
}


/* Modal Text */

.custom-modal p {

    margin:
        0 auto 25px;

    max-width:
        320px;

    color:
        var(--muted);

    font-size:
        14px;

    line-height:
        1.6;
}


#removeItemName {

    color:
        var(--dark);

    font-weight:
        700;
}


/* Modal Buttons */

.custom-modal-actions {

    display:
        flex;

    justify-content:
        center;

    gap:
        10px;
}


.custom-modal-actions .btn {

    min-width:
        105px;

    padding:
        9px 18px;

    border-radius:
        9px;

    font-weight:
        600;

    transition:
        all .2s ease;
}


.custom-modal-actions .btn-default {

    color:
        var(--text);

    background:
        #f1f5f9;

    border:
        1px solid var(--border);
}


.custom-modal-actions .btn-default:hover {

    background:
        #e2e8f0;
}


.custom-modal-actions .btn-danger {

    background:
        var(--danger);

    border-color:
        var(--danger);
}


.custom-modal-actions .btn-danger:hover {

    background:
        var(--danger-dark);

    border-color:
        var(--danger-dark);

    transform:
        translateY(-1px);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991px) {

    .cart-container {

        position:
            relative;

        top:
            auto;

        margin-top:
            20px;
    }


    .product-grid {

        grid-template-columns:
            repeat(
                auto-fill,
                minmax(160px, 1fr)
            );
    }
}


@media (max-width: 600px) {

    .product-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap:
            12px;
    }


    .product-card {

        min-height:
            235px;

        padding:
            12px;
    }


    .product-card img {

        width:
            95px;

        height:
            95px;
    }


    .product-card p strong {

        font-size:
            13px;
    }


    .product-card .text-success {

        font-size:
            15px;
    }


    .cart-container {

        padding:
            16px;
    }


    .cart-table thead th,
    .cart-table tbody td {

        padding:
            9px 5px;
    }


    #grandTotal {

        font-size:
            22px;
    }


    .custom-modal {

        padding:
            26px 20px;
    }
}


@media (max-width: 400px) {

    .product-grid {

        grid-template-columns:
            1fr;
    }


    .product-card {

        min-height:
            220px;
    }


    .custom-modal-actions {

        flex-direction:
            column;
    }


    .custom-modal-actions .btn {

        width:
            100%;
    }
}

</style>


<!-- =========================================================
     PRODUCTS + CART
========================================================= -->

<div class="row">


    <!-- =====================================================
         PRODUCTS
    ====================================================== -->

    <div class="col-md-8">

        <h4>

            <i class="glyphicon glyphicon-barcode"></i>

            <strong>
                Products
            </strong>

        </h4>


        <div class="product-grid">

            <?php foreach ($products as $product): ?>

                <?php

                $product_id =
                    (int)$product['id'];

                $product_name =
                    htmlspecialchars(
                        $product['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                $product_price =
                    (float)$product['sale_price'];

                $product_unit =
                    htmlspecialchars(
                        $product['unit'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                $product_stock =
                    (float)$product['quantity'];

                $product_image =
                    !empty($product['image'])
                        ? $product['image']
                        : 'no_image.png';

                ?>

                <div
                    class="product-card"
                    data-id="<?php echo $product_id; ?>"
                    onclick="addToCart(
                        <?php echo $product_id; ?>,
                        <?php echo htmlspecialchars(
                            json_encode($product['name']),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>,
                        <?php echo $product_price; ?>,
                        <?php echo htmlspecialchars(
                            json_encode($product['unit']),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>,
                        <?php echo $product_stock; ?>
                    )"
                >

                    <img
                        src="uploads/products/<?php
                            echo htmlspecialchars(
                                $product_image,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        alt="<?php echo $product_name; ?>"
                    >


                    <p>

                        <strong>
                            <?php
                            echo $product_name;
                            ?>
                        </strong>

                    </p>


                    <p class="text-success">

                        ₱<?php
                        echo number_format(
                            $product_price,
                            2
                        );
                        ?>

                        /

                        <?php
                        echo $product_unit;
                        ?>

                    </p>


                    <small>

                        Stock:

                        <?php
                        echo $product_stock .
                            ' ' .
                            $product_unit;
                        ?>

                    </small>

                </div>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =====================================================
         CART
    ====================================================== -->

    <div class="col-md-4">
        <div class="cart-container">
            <h4>
                <i class="fa-solid fa-cart-plus"></i>
                <strong>
                    Cart
                </strong>
            </h4>
            <p class="text-muted">
                Click on a product to add it to the cart.
            </p>
            <form
                method="post"
                id="saleForm"
            >
                <!-- CART TABLE -->

                <div class="table-responsive">

                    <table
                        class="table table-bordered cart-table"
                        id="cartTable"
                    >

                        <thead>

                            <tr>

                                <th>
                                    Item
                                </th>

                                <th style="width: 120px;">
                                    Qty
                                </th>

                                <th>
                                    Total
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="cartBody">

                            <!-- Cart items generated by JavaScript -->

                        </tbody>

                    </table>

                </div>


                <!-- TOTAL -->

                <div class="text-right">

                    <h5>

                        Total:

                        ₱

                        <span id="grandTotal">
                            0.00
                        </span>

                    </h5>

                </div>


                <!-- CART DATA -->

                <input
                    type="hidden"
                    name="cart_data"
                    id="cartData"
                >


                <!-- ACTIONS -->

                <div class="text-center">

                    <button
                        type="submit"
                        class="btn btn-success btn-m"
                        id="completeSaleBtn"
                    >

                        <i class="fa fa-check"></i>

                        Complete Sale

                    </button>


                    <a
                        href="sales.php"
                        class="btn btn-outline-primary rounded-pill px-3 btn-m"
                    >

                        <i class="fa fa-list"></i>

                        Manage Sales

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     REMOVE CONFIRMATION MODAL
========================================================= -->

<div
    id="removeConfirmModal"
    class="custom-modal-overlay"
>

    <div class="custom-modal">


        <!-- ICON -->

        <div class="custom-modal-icon">

            <i class="glyphicon glyphicon-trash"></i>

        </div>


        <!-- TITLE -->

        <h3>
            Remove Item?
        </h3>


        <!-- MESSAGE -->

        <p>

            Are you sure you want to remove

            <strong id="removeItemName"></strong>

            from the cart?

        </p>


        <!-- BUTTONS -->

        <div class="custom-modal-actions">

            <button
                type="button"
                class="btn btn-default"
                onclick="closeRemoveModal()"
            >

                Cancel

            </button>


            <button
                type="button"
                class="btn btn-danger"
                onclick="confirmRemoveItem()"
            >

                <i class="glyphicon glyphicon-trash"></i>

                Remove

            </button>

        </div>

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

/* =========================================================
   CART VARIABLES
========================================================= */

let cart = [];

let itemToRemove = null;


/* =========================================================
   ADD TO CART
========================================================= */

function addToCart(
    id,
    name,
    price,
    unit,
    stock
) {

    stock = parseFloat(stock);
    price = parseFloat(price);


    /* Find existing item */

    let existing =
        cart.find(
            item => item.id === id
        );


    /* -----------------------------------------------------
       EXISTING PRODUCT
    ----------------------------------------------------- */

    if (existing) {

        if (
            existing.qty + 1 >
            stock
        ) {

            showNotification(
                `Not enough stock. Available: ${stock} ${unit}`,
                'warning'
            );

            return;
        }


        existing.qty++;

        existing.total =
            existing.qty *
            existing.price;

    }


    /* -----------------------------------------------------
       NEW PRODUCT
    ----------------------------------------------------- */

    else {

        if (stock <= 0) {

            showNotification(
                `Out of stock for ${name}`,
                'danger'
            );

            return;
        }


        cart.push({

            id:
                id,

            name:
                name,

            price:
                price,

            unit:
                unit,

            qty:
                1,

            total:
                price,

            stock:
                stock

        });

    }


    renderCart();
}


/* =========================================================
   OPEN REMOVE MODAL
========================================================= */

function openRemoveModal(id) {

    const item =
        cart.find(
            item => item.id === id
        );


    if (!item) {
        return;
    }


    itemToRemove = id;


    document.getElementById(
        'removeItemName'
    ).textContent = item.name;


    document.getElementById(
        'removeConfirmModal'
    ).classList.add('show');
}


/* =========================================================
   CLOSE REMOVE MODAL
========================================================= */

function closeRemoveModal() {

    itemToRemove = null;


    document.getElementById(
        'removeConfirmModal'
    ).classList.remove('show');
}


/* =========================================================
   CONFIRM REMOVE
========================================================= */

function confirmRemoveItem() {

    if (
        itemToRemove === null
    ) {
        return;
    }


    cart =
        cart.filter(
            item =>
                item.id !==
                itemToRemove
        );


    itemToRemove = null;


    document.getElementById(
        'removeConfirmModal'
    ).classList.remove('show');


    renderCart();
}


/* =========================================================
   RENDER CART
========================================================= */

function renderCart() {

    const cartBody =
        document.getElementById(
            'cartBody'
        );


    cartBody.innerHTML = '';


    let grandTotal = 0;


    /* -----------------------------------------------------
       EMPTY CART
    ----------------------------------------------------- */

    if (cart.length === 0) {

        cartBody.innerHTML = `

            <tr>

                <td
                    colspan="4"
                    style="
                        text-align:center;
                        padding:30px 10px;
                        color:#94a3b8;
                    "
                >

                    <i
                        class="fa fa-shopping-cart"
                        style="
                            font-size:30px;
                            display:block;
                            margin-bottom:10px;
                        "
                    ></i>

                    Cart is empty

                </td>

            </tr>

        `;

    }


    /* -----------------------------------------------------
       CART ITEMS
    ----------------------------------------------------- */

    cart.forEach(item => {

        grandTotal +=
            item.total;


        cartBody.innerHTML += `

            <tr>

                <td>

                    <strong>
                        ${escapeHtml(item.name)}
                    </strong>

                </td>


                <td>

                    <input
                        type="number"
                        min="1"
                        max="${item.stock}"
                        value="${item.qty}"
                        onchange="
                            updateQty(
                                ${item.id},
                                this.value
                            )
                        "
                    >

                    <small
                        style="
                            color:#94a3b8;
                            display:block;
                            margin-top:3px;
                        "
                    >
                        ${escapeHtml(item.unit)}
                    </small>

                </td>


                <td>

                    <strong>
                        ₱${item.total.toFixed(2)}
                    </strong>

                </td>


                <td>

                    <button
                        type="button"
                        class="btn btn-danger btn-xs"
                        onclick="
                            openRemoveModal(
                                ${item.id}
                            )
                        "
                        title="Remove item"
                    >

                        <span
                            class="
                                glyphicon
                                glyphicon-trash
                            "
                        ></span>

                    </button>

                </td>

            </tr>

        `;

    });


    /* -----------------------------------------------------
       UPDATE TOTAL
    ----------------------------------------------------- */

    document.getElementById(
        'grandTotal'
    ).innerText =
        grandTotal.toFixed(2);


    /* -----------------------------------------------------
       UPDATE HIDDEN INPUT
    ----------------------------------------------------- */

    document.getElementById(
        'cartData'
    ).value =
        JSON.stringify(cart);


    /* -----------------------------------------------------
       UPDATE COMPLETE SALE BUTTON
    ----------------------------------------------------- */

    const completeSaleBtn =
        document.getElementById(
            'completeSaleBtn'
        );


    if (cart.length === 0) {

        completeSaleBtn.disabled =
            true;

        completeSaleBtn.style.opacity =
            '.55';

        completeSaleBtn.style.cursor =
            'not-allowed';

    } else {

        completeSaleBtn.disabled =
            false;

        completeSaleBtn.style.opacity =
            '1';

        completeSaleBtn.style.cursor =
            'pointer';
    }
}


/* =========================================================
   UPDATE QUANTITY
========================================================= */

function updateQty(
    id,
    qty
) {

    let item =
        cart.find(
            p => p.id === id
        );


    if (!item) {
        return;
    }


    qty =
        parseFloat(qty);


    /* Invalid quantity */

    if (
        isNaN(qty) ||
        qty < 1
    ) {

        qty = 1;
    }


    /* Exceeds stock */

    if (
        qty >
        item.stock
    ) {

        showNotification(
            `Not enough stock. Available: ${item.stock} ${item.unit}`,
            'warning'
        );

        qty =
            item.stock;
    }


    item.qty =
        qty;


    item.total =
        item.qty *
        item.price;


    renderCart();
}


/* =========================================================
   HTML ESCAPE
========================================================= */

function escapeHtml(text) {

    const div =
        document.createElement(
            'div'
        );

    div.textContent =
        text;

    return div.innerHTML;
}


/* =========================================================
   SIMPLE NOTIFICATION
========================================================= */

function showNotification(
    message,
    type = 'warning'
) {

    const old =
        document.querySelector(
            '.sale-notification'
        );


    if (old) {
        old.remove();
    }


    const notification =
        document.createElement(
            'div'
        );


    notification.className =
        'sale-notification';


    notification.textContent =
        message;


    let background =
        '#f59e0b';


    if (type === 'danger') {
        background =
            '#dc2626';
    }


    notification.style.cssText = `

        position: fixed;

        top: 25px;

        right: 25px;

        z-index: 100000;

        max-width: 350px;

        padding: 14px 18px;

        color: white;

        background: ${background};

        border-radius: 10px;

        box-shadow:
            0 10px 30px
            rgba(0,0,0,.18);

        font-size: 13px;

        font-weight: 600;

        animation:
            notificationShow .25s ease;

    `;


    document.body.appendChild(
        notification
    );


    setTimeout(
        () => {

            notification.style.opacity =
                '0';

            notification.style.transform =
                'translateX(20px)';

            notification.style.transition =
                '.25s ease';

            setTimeout(
                () => notification.remove(),
                250
            );

        },
        3000
    );
}


/* =========================================================
   CLOSE MODAL BY CLICKING OUTSIDE
========================================================= */

document
    .getElementById(
        'removeConfirmModal'
    )
    .addEventListener(
        'click',
        function(event) {

            if (
                event.target === this
            ) {

                closeRemoveModal();

            }

        }
    );


/* =========================================================
   CLOSE MODAL WITH ESC
========================================================= */

document.addEventListener(
    'keydown',
    function(event) {

        if (
            event.key === 'Escape'
        ) {

            closeRemoveModal();

        }

    }
);


/* =========================================================
   PREVENT EMPTY SALE
========================================================= */

document
    .getElementById(
        'saleForm'
    )
    .addEventListener(
        'submit',
        function(event) {

            if (cart.length === 0) {

                event.preventDefault();

                showNotification(
                    'Please add at least one product to the cart.',
                    'warning'
                );

                return false;
            }

        }
    );


/* =========================================================
   INITIALIZE
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function() {

        renderCart();

    }
);

</script>


<style>

@keyframes notificationShow {

    from {

        opacity:
            0;

        transform:
            translateX(20px);
    }

    to {

        opacity:
            1;

        transform:
            translateX(0);
    }
}

</style>


<?php include_once('layouts/footer.php'); ?>
