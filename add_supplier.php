<?php
  require_once('includes/load.php');
  page_require_level(2); 

  if(isset($_POST['add_supplier'])){
    $req_fields = array('name','contact_person','phone','email','address');
    validate_fields($req_fields);

    if(empty($errors)){
      $name   = remove_junk($db->escape($_POST['name']));
      $contact= remove_junk($db->escape($_POST['contact_person']));
      $phone  = remove_junk($db->escape($_POST['phone']));
      $email  = remove_junk($db->escape($_POST['email']));
      $address= remove_junk($db->escape($_POST['address']));

      $query = "INSERT INTO suppliers (name,contact_person,phone,email,address) ";
      $query .=" VALUES ('{$name}','{$contact}','{$phone}','{$email}','{$address}')";
      if($db->query($query)){
        $session->msg('s',"Supplier added successfully.");
        log_activity(
          'Add Supplier',
          "Added new supplier: {$name} | Contact Person: {$contact} | Phone: {$phone} | Email: {$email}",
          'suppliers.php'
      );
        redirect('suppliers.php', false);
      } else {
        $session->msg('d',' Sorry failed to add supplier!');
        redirect('add_supplier.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_supplier.php',false);
    }
  }
?>

<div class="row">
 <div class="col-md-12">
   <div class="panel panel-default">
     <div class="panel-heading">
       <strong><span class="glyphicon glyphicon-plus"></span> Add New Supplier</strong>
     </div>
     <div class="panel-body">
       <form method="post" action="add_supplier.php">
          <div class="form-group">
              <label for="name">Supplier Name</label>
              <input type="text" class="form-control" name="name" required>
          </div>
          <div class="form-group">
              <label for="contact_person">Contact Person</label>
              <input type="text" class="form-control" name="contact_person">
          </div>
          <div class="form-group">
              <label for="phone">Phone</label>
              <input type="text" class="form-control" name="phone">
          </div>
          <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email">
          </div>
          <div class="form-group">
              <label for="address">Address</label>
              <textarea class="form-control" name="address"></textarea>
          </div>
          <button type="submit" name="add_supplier" class="btn btn-success">Save</button>
          <button onclick="window.parent.closeModal()"  class="btn btn-danger">Cancel</button>
       </form>
     </div>
   </div>
 </div>
</div>

<?php include_once('layouts/footer.php'); ?>
