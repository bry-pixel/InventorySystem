<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if (empty($errors)) {
  $user = authenticate_v2($username, $password);
  if ($user) {
    
    if ($user['status'] === '0') {
      $session->msg('d', "Sorry! Your account has been deactivated.");
      log_activity(
        'Deactivated User Login Attempt',
        "Deactivated user {$user['username']} attempted to log in.",
        'auth.php'
      );
      redirect('index.php', false);
    }
  
    $session->login($user['id']);
    updateLastLogIn($user['id']);
    $session->msg('s', 'Welcome to Inventory Management System');
    log_activity(
      'User Login',
      "User {$user['username']} logged in.",
      'auth.php'
    );

    if ($user['user_level'] === '1') {
      redirect('admin.php', false);
    } elseif ($user['user_level'] === '2') {
      redirect('special.php', false);
    } elseif  ($user['user_level'] === '3') {
      redirect('home.php', false);
    }elseif  ($user['user_level'] === '4') {
      redirect('guest.php', false);
    }
  } else {
    $session->msg('d', 'Sorry Username/Password incorrect.');
    log_activity(
      'Failed Login Attempt',
      "Failed login attempt for username: {$username}.",
      'auth.php'
    );
    redirect('index.php', false);
  }
} else {
  $session->msg('d', $errors);
  redirect('index.php', false);
}
?>