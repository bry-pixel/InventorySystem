<?php
$page_title = 'Edit Category';
require_once('includes/load.php');
page_require_level(2);

$categorie = find_by_id('categories', (int)$_GET['id']);
if (!$categorie) {
  $session->msg("d", "Missing category ID.");
  log_activity('Edit Category Failed', "Attempted to edit category but ID not found.", 'edit_categorie.php');
  redirect('categorie.php');
}

if (isset($_POST['edit_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);

  $cat_name = remove_junk($db->escape($_POST['categorie-name']));

  if (empty($errors)) {
    $sql = "UPDATE categories SET name='{$cat_name}' WHERE id='{$categorie['id']}'";
    $result = $db->query($sql);

    if ($result && $db->affected_rows() === 1) {
      $session->msg("s", "Category updated successfully.");
      log_activity(
        'Edit Category',
        "Category '{$categorie['name']}' renamed to '{$cat_name}'.",
        'edit_categorie.php'
      );
      redirect('categorie.php', false);
    } else {
      $session->msg("d", "Failed to update category.");
      log_activity(
        'Edit Category Failed',
        "Attempted to update category '{$categorie['name']}' but query failed or no changes made.",
        'edit_categorie.php'
      );
      redirect('categorie.php', false);
    }
  } else {
    $session->msg("d", $errors);
    log_activity(
      'Edit Category Failed',
      "Validation error while updating category '{$categorie['name']}': " . implode(', ', $errors),
      'edit_categorie.php'
    );
    redirect('categorie.php', false);
  }
}
?>

<div class="panel panel-default" style="border-radius:10px;">
  <div class="panel-heading bg-primary" style="border-radius:10px 10px 0 0; color:#fff;">
    <strong><i class="glyphicon glyphicon-edit"></i> Edit Category</strong>
  </div>
  <div class="panel-body">
    <?php echo display_msg($msg); ?>
    <form method="post" action="edit_categorie.php?id=<?php echo (int)$categorie['id']; ?>" class="clearfix">

      <div class="form-group">
        <label for="categorie-name">Category Name</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
          <input type="text" class="form-control" name="categorie-name"
                 value="<?php echo remove_junk(ucfirst($categorie['name'])); ?>" required>
        </div>
      </div>

      <div class="form-group text-right">
        <a href="categorie.php" class="btn btn-default">
          <i class="glyphicon glyphicon-arrow-left"></i> Back
        </a>
        <button type="submit" name="edit_cat" class="btn btn-primary">
          <i class="glyphicon glyphicon-ok"></i> Update Category
        </button>
      </div>

    </form>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
