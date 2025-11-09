<?php
  $page_title = 'Add User Group';
  require_once('includes/load.php');
  page_require_level(1);

  if (isset($_POST['add'])) {
    $req_fields = ['group-name', 'group-level', 'status'];
    validate_fields($req_fields);

    $name  = remove_junk($db->escape($_POST['group-name']));
    $level = (int) remove_junk($db->escape($_POST['group-level']));
    $status = (int) remove_junk($db->escape($_POST['status']));

    if (find_by_groupName($name) === false) {
      $session->msg('d', '<b>Sorry!</b> Group Name already exists.');
      redirect('add_group.php', false);
    } elseif (find_by_groupLevel($level) === false) {
      $session->msg('d', '<b>Sorry!</b> Group Level already exists.');
      redirect('add_group.php', false);
    }

    if (empty($errors)) {
      $query  = "INSERT INTO user_groups (group_name, group_level, group_status) ";
      $query .= "VALUES ('{$name}', '{$level}', '{$status}')";

      if ($db->query($query)) {
        $session->msg('s', " Group <b>{$name}</b> has been created successfully!");
        log_activity(
          'Add User Group',
          "Created new user group: {$name} | Level: {$level} | Status: " . ($status ? 'Active' : 'Inactive'),
          'group.php'
        );
        redirect('add_group.php', false);
      } else {
        $session->msg('d', ' Failed to create group, please try again.');
        redirect('add_group.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_group.php', false);
    }
  }
?>

<?php include_once('layouts/header.php'); ?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-info text-white text-center">
          <h4><i class="glyphicon glyphicon-user"></i> Add New User Group</h4>
        </div>
        <div class="card-body">
          <?php echo display_msg($msg); ?>
          <form method="post" action="add_group.php">
            <div class="form-group mb-3">
              <label for="name" class="form-label">Group Name</label>
              <input type="text" class="form-control" name="group-name" required placeholder="Enter group name">
            </div>

            <div class="form-group mb-3">
              <label for="level" class="form-label">Group Level</label>
              <input type="number" class="form-control" name="group-level" min="1" required placeholder="Enter group level">
              <small class="form-text text-muted">Lower number = higher access.</small>
            </div>

            <div class="form-group mb-4">
              <label for="status" class="form-label">Status</label>
              <select class="form-control" name="status" required>
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

            <div class="d-grid">
              <button type="submit" name="add" class="btn btn-info btn-lg">
                <i class="glyphicon glyphicon-plus"></i> Create Group
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
