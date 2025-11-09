<?php
$page_title = 'All Group';
require_once('includes/load.php');
page_require_level(1);

if(isset($_GET['modal']) && $_GET['modal'] == 'edit' && isset($_GET['id'])){
    $group = find_by_id('user_groups',(int)$_GET['id']);
    ?>
    <form id="editGroupForm" method="post">
        <input type="hidden" name="group_id" value="<?php echo (int)$group['id']; ?>">
        <div class="form-group">
            <label>Group Name</label>
            <input type="text" name="group-name" class="form-control" value="<?php echo remove_junk($group['group_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Group Level</label>
            <input type="number" name="group-level" class="form-control" value="<?php echo remove_junk($group['group_level']); ?>" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" <?php if($group['group_status']==1) echo 'selected'; ?>>Active</option>
                <option value="0" <?php if($group['group_status']==0) echo 'selected'; ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" name="update" class="btn btn-success btn-sm">Save Changes</button>
    </form>
    <script>
    document.getElementById('editGroupForm').addEventListener('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('ajax',1);
        var xhr = new XMLHttpRequest();
        xhr.open('POST','group.php?id=<?php echo (int)$group['id'];?>',true);
        xhr.onload = function(){
            if(xhr.status == 200){
                if(xhr.responseText.includes('success')) {
                    alert('Group updated successfully!');
                    closeModal('editGroupModal');
                    location.reload();
                } else {
                    alert('Error: '+xhr.responseText);
                }
            }
        };
        xhr.send(formData);
    });
    </script>
    <?php
    exit;
}

if(isset($_GET['modal']) && $_GET['modal'] == 'add'){
    ?>
    <form id="addGroupForm" method="post">
        <div class="form-group">
            <label>Group Name</label>
            <input type="text" name="group-name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Group Level</label>
            <input type="number" name="group-level" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary btn-sm">Add Group</button>
    </form>
    <script>
    document.getElementById('addGroupForm').addEventListener('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('ajax',1);
        var xhr = new XMLHttpRequest();
        xhr.open('POST','group.php',true);
        xhr.onload = function(){
            if(xhr.status == 200){
                if(xhr.responseText.includes('success')) {
                    alert('New group added successfully!');
                    closeModal('addGroupModal');
                    location.reload();
                } else {
                    alert('Error: '+xhr.responseText);
                }
            }
        };
        xhr.send(formData);
    });
    </script>
    <?php
    exit;
}

/* === Handle DB Updates === */
if(isset($_POST['update'])){
    $req_fields = array('group-name','group-level');
    validate_fields($req_fields);
    if(empty($errors)){
        $name = remove_junk($db->escape($_POST['group-name']));
        $level = remove_junk($db->escape($_POST['group-level']));
        $status = remove_junk($db->escape($_POST['status']));
        $group_id = (int)$db->escape($_POST['group_id']);
        $query  = "UPDATE user_groups SET group_name='{$name}', group_level='{$level}', group_status='{$status}' WHERE id='{$group_id}'";
        $result = $db->query($query);
        if($result && $db->affected_rows() >= 0){
            echo isset($_POST['ajax']) ? 'success' : $session->msg('s',"Group updated!");
        } else {
            echo isset($_POST['ajax']) ? 'failed' : $session->msg('d',"Update failed!");
        }
    } else { echo isset($_POST['ajax']) ? implode(", ", $errors) : $session->msg("d", $errors); }
    if(!isset($_POST['ajax'])) redirect('group.php', false);
    exit;
}

if(isset($_POST['add'])){
    $req_fields = array('group-name','group-level');
    validate_fields($req_fields);
    if(empty($errors)){
        $name = remove_junk($db->escape($_POST['group-name']));
        $level = remove_junk($db->escape($_POST['group-level']));
        $status = remove_junk($db->escape($_POST['status']));
        $query  = "INSERT INTO user_groups (group_name,group_level,group_status) VALUES ('{$name}','{$level}','{$status}')";
        $result = $db->query($query);
        if($result){
            echo isset($_POST['ajax']) ? 'success' : $session->msg('s',"Group added!");
        } else {
            echo isset($_POST['ajax']) ? 'failed' : $session->msg('d',"Add failed!");
        }
    } else { echo isset($_POST['ajax']) ? implode(", ", $errors) : $session->msg("d", $errors); }
    if(!isset($_POST['ajax'])) redirect('group.php', false);
    exit;
}

$all_groups = find_all('user_groups');
?>
<?php include_once('layouts/header.php'); ?>

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
  width: 300px;
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

<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default" style="box-shadow:0 4px 12px rgba(0,0,0,0.1); border-radius:8px; overflow:hidden;">
      <div class="panel-heading">
        <h3 class="panel-title" style="display:inline-block;">
          <span class="glyphicon glyphicon-th"></span> User Groups
        </h3>
        <button onclick="openModal('addGroupModal','group.php?modal=add')" 
                class="btn btn-primary btn-sm pull-right" style="border-radius:20px;">
          <span class="glyphicon glyphicon-plus"></span> Add New Group
        </button>
      </div>

      <div class="panel-body">
        <table class="table table-hover table-bordered" style="background:white;">
          <thead style="background:#f5f5f5; font-weight:bold; text-transform:uppercase;">
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Group Name</th>
              <th class="text-center" style="width: 20%;">Group Level</th>
              <th class="text-center" style="width: 15%;">Status</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_groups as $a_group): ?>
              <tr>
                <td class="text-center"><strong><?php echo count_id();?></strong></td>
                <td><?php echo remove_junk(ucwords($a_group['group_name']))?></td>
                <td class="text-center"><?php echo remove_junk(ucwords($a_group['group_level']))?></td>
                <td class="text-center">
                  <?php if($a_group['group_status'] === '1'): ?>
                    <span class="label label-success" style="font-size:12px; padding:5px 10px;">Active</span>
                  <?php else: ?>
                    <span class="label label-danger" style="font-size:12px; padding:5px 10px;">Inactive</span>
                  <?php endif;?>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-warning" 
                            onclick="openModal('editGroupModal','group.php?modal=edit&id=<?php echo (int)$a_group['id'];?>')" 
                            title="Edit" style="border-radius:4px;">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </button>
                    <a href="delete_group.php?id=<?php echo (int)$a_group['id'];?>" 
                       class="btn btn-xs btn-danger" title="Remove" style="border-radius:4px;"  
                       onclick="return confirm('Are you sure you want to delete this group?');">
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

<!-- Add Group Modal -->
<div id="addGroupModal" class="modal-overlay">
  <div class="modal-content">
    <span class="modal-close" onclick="closeModal('addGroupModal')">&times;</span>
    <h4 class="text-center">Add User Group</h4>
    <div id="addGroupBody">Loading...</div>
  </div>
</div>

<!-- Edit Group Modal -->
<div id="editGroupModal" class="modal-overlay">
  <div class="modal-content">
    <span class="modal-close" onclick="closeModal('editGroupModal')">&times;</span>
    <h4 class="text-center">Edit User Group</h4>
    <div id="editGroupBody">Loading...</div>
  </div>
</div>

<script>
function openModal(modalId, url) {
  document.getElementById(modalId).style.display = "block";
  var bodyId = modalId === "addGroupModal" ? "addGroupBody" : "editGroupBody";
  document.getElementById(bodyId).innerHTML = "Loading...";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", url, true);
  xhr.onload = function() {
    document.getElementById(bodyId).innerHTML = (this.status==200) ? this.responseText : "Error loading form.";
  };
  xhr.send();
}
function closeModal(modalId){ document.getElementById(modalId).style.display = "none"; }
window.onclick = function(event){
  if(event.target.classList.contains('modal-overlay')){
    event.target.style.display = "none";
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>
