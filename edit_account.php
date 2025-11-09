<?php
$page_title = 'Edit Account';
require_once('includes/load.php');
page_require_level(4);

$user = current_user();

/* === 1. Update Profile Photo === */
if (isset($_POST['submit'])) {
  if (!empty($_FILES['file_upload']['name'])) {
    $photo = new Media();
    $photo->upload($_FILES['file_upload']);
    if ($photo->process_user($user['id'])) {
      $session->msg('s', 'Photo has been updated.');
      log_activity(
        'Update Profile Photo',
        "User '{$user['username']}' updated their profile photo.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    } else {
      $session->msg('d', join($photo->errors));
      log_activity(
        'Update Profile Photo Failed',
        "User '{$user['username']}' failed to update their photo: " . join(', ', $photo->errors),
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    }
  } else {
    $session->msg('d', 'No file selected.');
    log_activity(
      'Update Profile Photo Failed',
      "User '{$user['username']}' attempted to update profile photo but no file was selected.",
      'edit_account.php'
    );

    redirect('edit_account.php', false);
  }
}

/* === 2. Update Account Info === */
if (isset($_POST['update'])) {
  $req_fields = ['name','username'];
  validate_fields($req_fields);

  if (empty($errors)) {
    $id       = (int)$user['id'];
    $name     = remove_junk($db->escape($_POST['name']));
    $username = remove_junk($db->escape($_POST['username']));

    $sql = "UPDATE users SET name ='{$name}', username ='{$username}' WHERE id='{$id}'";
    $result = $db->query($sql);

    if ($result && $db->affected_rows() === 1) {
      $session->msg('s', "Account updated successfully.");
      log_activity(
        'Update Account Info',
        "User '{$user['username']}' updated account info (Name: {$name}, Username: {$username}).",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    } else {
      $session->msg('d', "Failed to update account.");
      log_activity(
        'Update Account Info Failed',
        "User '{$user['username']}' attempted to update account but no changes were made or query failed.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    }
  } else {
    $session->msg("d", $errors);
    log_activity(
      'Update Account Info Failed',
      "User '{$user['username']}' failed validation when updating account: " . implode(', ', $errors),
      'edit_account.php'
    );

    redirect('edit_account.php', false);
  }
}

/* === 3. Handle Change Password === */
if (isset($_POST['update_password'])) {
  $req_fields = ['old-password','new-password','confirm-password'];
  validate_fields($req_fields);

  if (empty($errors)) {
    if (sha1($_POST['old-password']) !== $user['password']) {
      $session->msg('d', "Old password is incorrect.");

      // ❌ Log wrong old password attempt
      log_activity(
        'Change Password Failed',
        "User '{$user['username']}' entered incorrect old password.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    }

    if ($_POST['new-password'] !== $_POST['confirm-password']) {
      $session->msg('d', "New password and confirmation do not match.");

      // ❌ Log mismatch
      log_activity(
        'Change Password Failed',
        "User '{$user['username']}' new password confirmation did not match.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    }

    $id   = (int)$user['id'];
    $new  = remove_junk($db->escape(sha1($_POST['new-password'])));
    $sql  = "UPDATE users SET password ='{$new}' WHERE id='{$id}'";
    $result = $db->query($sql);

    if ($result && $db->affected_rows() === 1) {
      $session->logout();
      $session->msg('s', "Password changed successfully. Please login again.");
      log_activity(
        'Change Password',
        "User '{$user['username']}' successfully changed their password.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    } else {
      $session->msg('d', "Failed to change password.");
      log_activity(
        'Change Password Failed',
        "User '{$user['username']}' password update query failed.",
        'edit_account.php'
      );

      redirect('edit_account.php', false);
    }
  } else {
    $session->msg("d", $errors);
    log_activity(
      'Change Password Failed',
      "User '{$user['username']}' failed validation during password change: " . implode(', ', $errors),
      'edit_account.php'
    );

    redirect('edit_account.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>

  <!-- Profile Photo -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <i class="glyphicon glyphicon-camera"></i> Change My Photo
      </div>
      <div class="card-body text-center p-4">
        <img id="photoPreview" class="img-preview"
             src="uploads/users/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Photo" style="max-width:150px; border-radius:50%; margin-bottom:15px;">
        <form class="mt-3" action="edit_account.php" method="POST" enctype="multipart/form-data">
          <input type="file" name="file_upload" accept="image/*"
                 onchange="previewImage(event)" class="form-control mb-3">
          <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
          <button type="submit" name="submit" class="btn btn-warning btn-rounded" style="background: linear-gradient(135deg, #f59e0b, #b45309); color: white; margin-top:10px;">
            <i class="glyphicon glyphicon-refresh"></i> Change
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Info -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <i class="glyphicon glyphicon-edit"></i> Edit My Account
      </div>
      <div class="card-body p-4">
        <form method="post" action="edit_account.php?id=<?php echo (int)$user['id'];?>" class="clearfix">
          <div class="form-group mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name"
                   value="<?php echo remove_junk(ucwords($user['name'])); ?>">
          </div>
          <div class="form-group mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username"
                   value="<?php echo remove_junk($user['username']); ?>">
          </div>
          <div class="d-flex justify-content-between">
            <!-- Open Password Modal -->
            <button type="button" class="btn btn-danger btn-rounded" onclick="openPasswordModal()">
              <i class="glyphicon glyphicon-lock"></i> Change Password
            </button>
            <button type="submit" name="update" class="btn btn-primary btn-rounded">
              <i class="glyphicon glyphicon-save"></i> Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
  <div class="modal-content" style="background:#fff; margin:5% auto; padding:20px; border-radius:8px; width:400px; max-width:90%; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
    <h4 class="text-center">Change Password</h4>
    <form method="post" action="edit_account.php">
      <div class="form-group mb-3">
        <label>Old Password</label>
        <input type="password" name="old-password" class="form-control" required>
      </div>
      <div class="form-group mb-3">
        <label>New Password</label>
        <input type="password" name="new-password" class="form-control" required>
      </div>
      <div class="form-group mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm-password" class="form-control" required>
      </div>
      <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">

      <div class="modal-footer" style="text-align:right; margin-top:15px;">
        <button type="submit" name="update_password" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById('photoPreview').src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}

// Open modal
function openPasswordModal() {
  document.getElementById("passwordModal").style.display = "block";
}

// Close modal
function closePasswordModal() {
  document.getElementById("passwordModal").style.display = "none";
}

// Close when clicking outside
window.onclick = function(event) {
  var modal = document.getElementById("passwordModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>