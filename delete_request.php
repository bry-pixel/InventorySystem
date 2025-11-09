<?php
require_once('includes/load.php');
page_require_level(1);

if (isset($_GET['id']) && isset($_GET['status'])) {
    $request_id = (int)$_GET['id'];
    $new_status = $_GET['status'];

    if (!in_array($new_status, ['approved', 'rejected'])) {
        $session->msg('d', 'Invalid status value.');

        log_activity(
            'Invalid Request Status Attempt',
            "Attempted to set invalid status '{$new_status}' for request ID: {$request_id}",
            'manage_requests.php'
        );

        redirect('manage_requests.php', false);
    }

    $sql = "UPDATE stock_requests 
            SET status = '{$db->escape($new_status)}' 
            WHERE id = '{$db->escape($request_id)}'";

    if ($db->query($sql)) {
        $session->msg('s', "Request has been {$new_status}.");

        log_activity(
            'Update Request Status',
            "Request ID {$request_id} marked as '{$new_status}'.",
            'manage_requests.php'
        );
    } else {
        $session->msg('d', 'Failed to update request status.');

       log_activity(
            'Update Request Status Failed',
            "Database update failed for request ID {$request_id} (status: {$new_status}).",
            'manage_requests.php'
        );
    }
} else {
    $session->msg('d', 'No request ID or status provided.');

    log_activity(
        'Missing Request Parameters',
        'No request ID or status provided in update request attempt.',
        'manage_requests.php'
    );
}

redirect('manage_requests.php', false);
?>
