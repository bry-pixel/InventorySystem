<?php
include_once('includes/load.php');
page_require_level(1);

$request_id = (int)($_GET['request_id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);
$status     = $_GET['status'] ?? 'approved';

if ($request_id <= 0 || $product_id <= 0) {
    $session->msg('d', 'Invalid request or product.');

    log_activity(
        'Invalid Request',
        "Attempted to update request with invalid IDs (request_id: {$request_id}, product_id: {$product_id})",
        'manage_requests.php'
    );

    redirect('manage_requests.php', false);
}

if (!in_array($status, ['approved', 'rejected'])) {
    $session->msg('d', 'Invalid status.');

    log_activity(
        'Invalid Status',
        "Attempted to update request #{$request_id} with invalid status '{$status}'",
        'manage_requests.php'
    );

    redirect('manage_requests.php', false);
}

if ($db->query("UPDATE stock_requests SET status='{$db->escape($status)}' WHERE id='{$db->escape($request_id)}'")) {
    $session->msg('s', "Request has been {$status}.");

    log_activity(
        ucfirst($status) === 'Approved' ? 'Request Approved' : 'Request Rejected',
        "Stock request #{$request_id} for product ID {$product_id} was {$status}",
        'manage_requests.php'
    );

    if ($status === 'approved') {
        redirect("add-purchaseV2.php?product_id={$product_id}", false);
    } else {
        redirect('manage_requests.php', false);
    }

} else {
    $session->msg('d', 'Failed to update request.');

    log_activity(
        'Request Update Failed',
        "Failed to update stock request #{$request_id} (status: {$status})",
        'manage_requests.php'
    );

    redirect('manage_requests.php', false);
}
?>
