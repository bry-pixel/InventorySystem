<?php
  $page_title = 'All User';
  require_once('includes/load.php');
  page_require_level(1);

  $all_users = find_all_user();
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default shadow-sm">
      <div class="panel-heading clearfix d-flex justify-content-between align-items-center">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Users</span>
        </strong>

        <!-- Modal Button -->
        <button onclick="openModal('add_user.php')" 
                class="btn btn-info pull-right" 
                style="border-radius:20px; font-size:14px;">
          <i class="glyphicon glyphicon-plus"></i> Add New User
        </button>
      </div>

      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Name</th>
              <th>Username</th>
              <th class="text-center" style="width: 15%;">User Role</th>
              <th class="text-center" style="width: 10%;">Status</th>
              <th style="width: 20%;">Last Login</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_users as $a_user): ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk(ucwords($a_user['name']))?></td>
                <td><?php echo remove_junk(ucwords($a_user['username']))?></td>
                <td class="text-center"><?php echo remove_junk(ucwords($a_user['group_name']))?></td>
                <td class="text-center">
                  <?php if($a_user['status'] === '1'): ?>
                    <span class="label label-success">Active</span>
                  <?php else: ?>
                    <span class="label label-danger">Deactive</span>
                  <?php endif;?>
                </td>
                <td><?php echo date("M d, Y h:i A", strtotime($a_user['last_login']))?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_user.php?id=<?php echo (int)$a_user['id'];?>" 
                            class="btn btn-xs btn-warning" title="Edit">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_user.php?id=<?php echo (int)$a_user['id'];?>" 
                       class="btn btn-xs btn-danger" title="Remove" 
                       onclick="return confirm('Are you sure you want to delete this user?');">
                      <i class="glyphicon glyphicon-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Reusable Modal -->
<div id="globalModal" class="modal-overlay">
  <div class="modal-content">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <div id="modal-body">Loading...</div>
  </div>
</div>

<style>
/* Modal Overlay */
.modal-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  z-index: 9999;
}

/* Modal Box */
.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  width: 70%;
  max-width: 800px;
  max-height: 85%;
  overflow-y: auto;
  box-shadow: 0 6px 20px rgba(0,0,0,0.35);
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  animation: fadeIn 0.3s ease-out;
}

/* Close Button */
.modal-close {
  float: right;
  font-size: 22px;
  font-weight: bold;
  cursor: pointer;
  color: red;
}

/* Fade Animation */
@keyframes fadeIn {
  from {opacity: 0; transform: translate(-50%, -40%);}
  to {opacity: 1; transform: translate(-50%, -50%);}
}
</style>

<script>
// Open modal with given URL
function openModal(url) {
    const modal = document.getElementById("globalModal");
    const body  = document.getElementById("modal-body");
    modal.style.display = "block";
    body.innerHTML = "Loading...";

    fetch(url)
      .then(res => res.text())
      .then(html => { body.innerHTML = html; })
      .catch(() => { body.innerHTML = "Error loading content."; });
}

// Close modal
function closeModal() {
    document.getElementById("globalModal").style.display = "none";
}

// Close modal if user clicks outside
window.onclick = function(event) {
    const modal = document.getElementById("globalModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// ESC key closes modal
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") closeModal();
});
</script>

<?php include_once('layouts/footer.php'); ?>
