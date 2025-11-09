<?php
require_once('includes/load.php');
page_require_level(1);

$supplier = find_by_id('suppliers', (int)$_GET['id']);

if (!$supplier) {
    $session->msg("d", "Missing Supplier ID.");

    log_activity(
        'Delete Supplier Failed',
        'Attempted to delete supplier, but Supplier ID was missing or invalid.',
        'suppliers.php'
    );

    redirect('suppliers.php');
}

$delete_id = delete_by_id('suppliers', (int)$supplier['id']);
if ($delete_id) {
    $session->msg("s", "Supplier deleted successfully.");

    log_activity(
        'Delete Supplier',
        "Deleted supplier record (ID: {$supplier['id']}) | Name: {$supplier['name']}.",
        'suppliers.php'
    );

    redirect('suppliers.php');
} else {
    $session->msg("d", "Supplier deletion failed.");

    log_activity(
        'Delete Supplier Failed',
        "Failed to delete supplier record (ID: {$supplier['id']}) | Name: {$supplier['name']}.",
        'suppliers.php'
    );

    redirect('suppliers.php');
}
?>
