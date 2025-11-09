<?php
$page_title = 'All categories';
require_once('includes/load.php');
page_require_level(2);

$all_categories = find_all('categories');
?>
<?php
if (isset($_POST['add_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));
  if (empty($errors)) {
    $sql  = "INSERT INTO categories (name)";
    $sql .= " VALUES ('{$cat_name}')";
    if ($db->query($sql)) {
      $session->msg("s", "Successfully Added New Category");
      log_activity(
        'Add Category',
        "Added new category: {$cat_name}",
        'categorie.php'
    );
      redirect('categorie.php', false);
    } else {
      $session->msg("d", "Sorry Failed to insert.");
      log_activity(
        'Add Category Failed',
        "Failed to add new category: {$cat_name}",
        'categorie.php'
    );
      
      redirect('categorie.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('categorie.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Category</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="categorie.php">
          <div class="form-group">
            <input type="text" class="form-control" name="categorie-name" placeholder="Category Name" required>
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary btn-block">Add Category</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Categories</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>Categories</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_categories as $cat): ?>
                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <!-- Edit button opens modal -->
                      <button type="button" class="btn btn-xs btn-warning"
                              onclick="openEditCategoryModal(<?php echo (int)$cat['id']; ?>)"
                              data-toggle="tooltip" title="Edit">
                        <span class="glyphicon glyphicon-edit"></span>
                      </button>
                      <a href="delete_categorie.php?id=<?php echo (int)$cat['id']; ?>"
                         class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove"
                         onclick="return confirm('Are you sure you want to delete this category?');">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Centered Modal -->
<div id="editCategoryModal" class="modal-overlay">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditCategoryModal()">&times;</span>
    <div id="editCategoryBody">Loading...</div>
  </div>
</div>

<style>
.modal-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  z-index: 9999;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 6px 20px rgba(0,0,0,0.35);
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  animation: fadeIn 0.3s ease-out;
}

.modal-close {
  float: right;
  font-size: 20px;
  font-weight: bold;
  cursor: pointer;
  color: red;
}

@keyframes fadeIn {
  from {opacity: 0; transform: translate(-50%, -40%);}
  to {opacity: 1; transform: translate(-50%, -50%);}
}
</style>

<script>
function openEditCategoryModal(catId) {
  document.getElementById("editCategoryModal").style.display = "block";
  document.getElementById("editCategoryBody").innerHTML = "Loading...";

  fetch("edit_categorie.php?id=" + catId + "&modal=1")
    .then(res => res.text())
    .then(html => document.getElementById("editCategoryBody").innerHTML = html)
    .catch(() => document.getElementById("editCategoryBody").innerHTML = "Error loading form.");
}


function closeEditCategoryModal() {
  document.getElementById("editCategoryModal").style.display = "none";
}

window.onclick = function(event) {
  let modal = document.getElementById("editCategoryModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>
