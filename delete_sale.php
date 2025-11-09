<?php
require_once('includes/load.php');
page_require_level(3);

$d_sale = find_by_id('sales', (int)$_GET['id']);
if (!$d_sale) {
    $session->msg("d", "Missing sale ID.");

   log_activity(
        'Delete Sale Failed',
        'Attempted to delete sale, but sale ID was missing or invalid.',
        'sales.php'
    );

    redirect('sales.php');
}

// Fetch related product
$product = find_by_id('products', $d_sale['product_id']);
if ($product) {
    $new_qty = $product['quantity'] + $d_sale['qty'];
    if ($db->query("UPDATE products SET quantity='{$new_qty}' WHERE id='{$product['id']}'")) {
    }
}

$delete_id = delete_by_id('sales', (int)$d_sale['id']);
if ($delete_id) {
    $session->msg("s", "Sale deleted successfully.");

    log_activity(
        'Delete Sale',
        "Deleted sale record (Sale ID: {$d_sale['id']}) for product '{$product['name']}' | Quantity: {$d_sale['qty']}",
        'sales.php'
    );

    redirect('sales.php');
} else {
    $session->msg("d", "Sale deletion failed.");

    log_activity(
        'Delete Sale Failed',
        "Failed to delete sale record (Sale ID: {$d_sale['id']}) for product '{$product['name']}'.",
        'sales.php'
    );

    redirect('sales.php');
}
?>
