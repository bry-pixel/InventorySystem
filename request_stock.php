<?php
require_once('includes/load.php');
page_require_level(3);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $user = current_user();
    $user_id = (int)$user['id'];

    $sql = "INSERT INTO stock_requests (product_id, requested_by, request_date, status)
            VALUES ('{$product_id}', '{$user_id}', NOW(), 'pending')";

    if ($db->query($sql)) {
        echo "Stock request for product ID {$product_id} has been sent!";
        log_activity(
            'New Stock Request',
            "User ID {$user_id} requested stock for product ID {$product_id}",
            'request_stock.php'
        );
    } else {
        echo "Error: Could not create request.";
        log_activity(
            'Stock Request Failed',
            "Failed to create stock request for product ID {$product_id} by user ID {$user_id}",
            'request_stock.php'
        );
    }
}
?>
