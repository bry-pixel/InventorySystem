<?php
$page_title = 'Add User';
require_once('includes/load.php');
page_require_level(1);
$groups = find_all('user_groups');

// Handle form submit
if (isset($_POST['add_user'])) {
    $req_fields = array('full-name','username','password','level');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name       = remove_junk($db->escape($_POST['full-name']));
        $username   = remove_junk($db->escape($_POST['username']));
        $password   = remove_junk($db->escape($_POST['password']));
        $user_level = (int)$db->escape($_POST['level']);
        $password   = sha1($password);

        $query  = "INSERT INTO users (name,username,password,user_level,status)";
        $query .= " VALUES ('{$name}','{$username}','{$password}','{$user_level}','1')";
        
        if ($db->query($query)) {

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo "<script>
                        alert('✅ User created successfully!');
                        window.parent.closeModal();
                        window.parent.location.reload();
                      </script>";
                      log_activity(
                        'Add User',
                        "Created new user: {$name} | Username: {$username} | Level: {$user_level}",
                        'users.php'
                    );
                exit;
            } else {
                $session->msg('s',"User account has been created!");
                redirect('users.php', false);
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo "<div class='alert alert-danger'>❌ Failed to create account.</div>";
                exit;
            } else {
                $session->msg('d',' Sorry failed to create account!');
                redirect('add_user.php', false);
            }
        }
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo "<div class='alert alert-danger'>". join(", ", $errors) ."</div>";
            exit;
        } else {
            $session->msg("d", $errors);
            redirect('add_user.php', false);
        }
    }
}

?>

<?php echo display_msg($msg); ?>

<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>Add New User</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-12">
        <form method="post" action="add_user.php" id="addUserForm">
          <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" name="full-name" placeholder="Full Name" required>
          </div>
          <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
          <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="form-group">
            <label for="level">User Role</label>
            <select class="form-control" name="level" required>
              <?php foreach ($groups as $group ):?>
                <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
