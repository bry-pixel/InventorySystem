<?php
require_once('includes/load.php');
page_require_level(2);

// Find the product
$product = find_by_id('products', (int)$_GET['id']);
if (!$product) {
    $session->msg("d", "Missing Product ID.");

    log_activity(
        'Invalid Product Delete',
        'Attempted to delete a product with an invalid or missing ID.',
        'product.php'
    );

    redirect('product.php');
}

// Attempt to delete
$delete_id = delete_by_id('products', (int)$product['id']);
if ($delete_id) {
    $session->msg("s", "Product deleted.");

    log_activity(
        'Delete Product',
        "Deleted product: {$product['name']} (ID: {$product['id']})",
        'product.php'
    );

    redirect('product.php');
} else {
    $session->msg("d", "Product deletion failed.");

    log_activity(
        'Delete Product Failed',
        "Failed to delete product: {$product['name']} (ID: {$product['id']})",
        'product.php'
    );

    redirect('product.php');
}
?>
