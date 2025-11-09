<?php
require_once('includes/load.php');
page_require_level(2);
$page_title = 'Suppliers';
$suppliers = find_all('suppliers');
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

@keyframes fadeIn {
  from {opacity: 0; transform: translate(-50%, -40%);}
  to {opacity: 1; transform: translate(-50%, -50%);}
}
</style>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-briefcase"></span>
          <span>Suppliers</span>
        </strong>
        <button class="btn btn-info pull-right" onclick="addSupplier()">+ Add Supplier</button>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Added On</th>
                <th class="text-center" style="width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($suppliers as $supplier): ?>
                <tr>
                  <td><?php echo remove_junk($supplier['name']); ?></td>
                  <td><?php echo remove_junk($supplier['contact_person']); ?></td>
                  <td><?php echo remove_junk($supplier['phone']); ?></td>
                  <td><?php echo remove_junk($supplier['email']); ?></td>
                  <td><?php echo remove_junk($supplier['address']); ?></td>
                  <td><?php echo date("M d, Y h:i A", strtotime($supplier['created_at'])); ?></td>
                  <td class="text-center">
                    <button class="btn btn-xs btn-warning" title="Edit" onclick="editSupplier(<?php echo (int)$supplier['id']; ?>)">
                      <span class="glyphicon glyphicon-edit"></span>
                    </button>
                    <a href="delete_supplier.php?id=<?php echo (int)$supplier['id']; ?>" 
                       class="btn btn-xs btn-danger" 
                       title="Delete" 
                       onclick="return confirm('Are you sure you want to delete this supplier?');">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($suppliers)): ?>
                <tr><td colspan="7" class="text-center text-muted">No suppliers found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reusable Modal -->
<div id="supplierModal" class="modal-overlay">
  <div class="modal-content">
    <div id="modal-body">Loading...</div>
  </div>
</div>

<script>
function addSupplier() {
  openModal("add_supplier.php");
}

function editSupplier(id) {
  openModal("edit_supplier.php?id=" + id);
}

function openModal(url) {
  let modal = document.getElementById("supplierModal");
  let body = document.getElementById("modal-body");
  modal.style.display = "block";
  body.innerHTML = "Loading...";

  let xhr = new XMLHttpRequest();
  xhr.open("GET", url, true);
  xhr.onload = function() {
    body.innerHTML = (this.status === 200) ? this.responseText : "Error loading form.";
  };
  xhr.send();
}

function closeModal() {
  document.getElementById("supplierModal").style.display = "none";
}

// Close modal when clicking outside content
window.onclick = function(event) {
  let modal = document.getElementById("supplierModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>
