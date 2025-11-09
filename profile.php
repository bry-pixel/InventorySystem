<?php
$page_title = 'My Profile';
require_once('includes/load.php');
include_once('layouts/header.php');
page_require_level(4);

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$user_id) {
  redirect('home.php', false);
}
$user_p = find_by_id('users', $user_id);
if (!$user_p) {
  redirect('home.php', false);
}
?>

<div class="row justify-content-center mt-5">
  <div class="col-md-6 col-lg-5">
    <div class="card profile-card">

      <!--  Profile Header -->
      <div class="profile-header">
        <img src="uploads/users/<?php echo htmlspecialchars($user_p['image']); ?>" alt="User Image">
        <h3><?php echo first_character($user_p['name']); ?></h3>
        <p class="opacity-75 mb-0">👤 User Profile</p>
      </div>

      <!--  Profile Body -->
      <div class="profile-body">
        <ul class="list-group list-group-flush mb-3">
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="glyphicon glyphicon-user text-primary"></i> Username:</span>
            <strong><?php echo htmlspecialchars($user_p['username']); ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="glyphicon glyphicon-king text-warning"></i> Role:</span>
            <strong><?php echo htmlspecialchars(find_by_id('user_groups', $user_p['user_level'])['group_name']); ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="glyphicon glyphicon-time text-success"></i> Last Login:</span>
            <strong><?php echo $user_p['last_login'] ? read_date($user_p['last_login']) : 'Never'; ?></strong>
          </li>
        </ul>

        <!--  Edit Profile-->
        <?php if ($user_p['id'] === $user['id']): ?>
          <a href="edit_account.php" class="btn btn-warning btn-edit">
            <i class="glyphicon glyphicon-edit"></i> Edit Profile
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>