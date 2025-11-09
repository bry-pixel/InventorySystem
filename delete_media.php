<?php
require_once('includes/load.php');
page_require_level(2);

$find_media = find_by_id('media', (int)$_GET['id']);

if (!$find_media) {
    $session->msg("d", "Missing media ID.");
    log_activity(
        'Invalid Media Delete',
        'Attempted to delete a media record with invalid or missing ID.',
        'media.php'
    );

    redirect('media.php');
}

$photo = new Media();

if ($photo->media_destroy($find_media['id'], $find_media['file_name'])) {
    $session->msg("s", "Photo has been deleted.");
    log_activity(
        'Delete Media',
        "Deleted media file: {$find_media['file_name']} (ID: {$find_media['id']})",
        'media.php'
    );

    redirect('media.php');
} else {
    $session->msg("d", "Photo deletion failed or missing parameter.");

    log_activity(
        'Delete Media Failed',
        "Failed to delete media file: {$find_media['file_name']} (ID: {$find_media['id']})",
        'media.php'
    );

    redirect('media.php');
}
?>
