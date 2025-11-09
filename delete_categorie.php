<?php
require_once('includes/load.php');
page_require_level(2);

// Fetch the category to delete
$categorie = find_by_id('categories', (int)$_GET['id']);
if (!$categorie) {
    $session->msg("d", "Missing Category ID.");
    redirect('categorie.php');
}

$delete_id = delete_by_id('categories', (int)$categorie['id']);

if ($delete_id) {
    log_activity(
        'Delete', 
        "Deleted category: {$categorie['name']}", 
        'categorie.php'
    );

    $session->msg("s", "Category deleted successfully.");
    redirect('categorie.php');
} else {
    log_activity(
        'Delete Failed', 
        "Failed to delete category: {$categorie['name']}", 
        'categorie.php'
    );

    $session->msg("d", "Category deletion failed.");
    redirect('categorie.php');
}
?>
