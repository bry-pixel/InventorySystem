<?php
  require_once('includes/load.php');
   page_require_level(1);
?>
<?php
  $delete_id = delete_by_id('users',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","User deleted.");
      log_activity(
        'Delete User',
        "Deleted user account (User ID: {$_GET['id']})",
        'users.php'
    );
      redirect('users.php');
  } else {
      $session->msg("d","User deletion failed Or Missing Prm.");
      log_activity(
        'Delete User Failed',
        "Failed to delete user account (User ID: {$_GET['id']})",
        'users.php'
    );
      redirect('users.php');
  }
?>
