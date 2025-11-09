<?php
  require_once('includes/load.php');

   page_require_level(1);
?>
<?php
  $delete_id = delete_by_id('user_groups',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","Group has been deleted.");
      log_activity(
        'Delete User Group',
        "Deleted user group ID: {$_GET['id']}",
        'group.php'
    );
      redirect('group.php');
  } else {
      $session->msg("d","Group deletion failed Or Missing Prm.");
      redirect('group.php');
  }
?>
