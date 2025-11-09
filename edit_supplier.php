<?php
  require_once('includes/load.php');
  page_require_level(2);

  $supplier = find_by_id('suppliers',(int)$_GET['id']);
  if(!$supplier){
    $session->msg("d","Missing supplier ID.");
    redirect('suppliers.php');
  }

  if(isset($_POST['update_supplier'])){
    $req_fields = array('name','contact_person','phone','email','address');
    validate_fields($req_fields);

    if(empty($errors)){
      $name   = remove_junk($db->escape($_POST['name']));
      $contact= remove_junk($db->escape($_POST['contact_person']));
      $phone  = remove_junk($db->escape($_POST['phone']));
      $email  = remove_junk($db->escape($_POST['email']));
      $address= remove_junk($db->escape($_POST['address']));

      $query = "UPDATE suppliers SET ";
      $query .= "name='{$name}', contact_person='{$contact}', phone='{$phone}', ";
      $query .= "email='{$email}', address='{$address}' ";
      $query .= "WHERE id='{$supplier['id']}'";

      if($db->query($query)){
        $session->msg('s',"Supplier updated successfully.");
        log_activity(
          'Edit Supplier',
          "Updated supplier record (ID: {$supplier['id']}) | Name: {$name}, Contact Person: {$contact}, Phone: {$phone}, Email: {$email}, Address: {$address}",
          'suppliers.php'
      );
        redirect('suppliers.php', false);
      } else {
        $session->msg('d',' Sorry failed to update supplier!');
        log_activity(
          'Edit Supplier Failed',
          "Failed to update supplier record (ID: {$supplier['id']})",
          'suppliers.php'
      );
        redirect('supplier.php?id='.$supplier['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('supplier.php?id='.$supplier['id'],false);
    }
  }
?>

<div class="row">
 <div class="col-md-12">
   <?php echo display_msg($msg); ?>
   <div class="panel panel-default">
     <div class="panel-heading">
       <strong><span class="glyphicon glyphicon-edit"></span> Edit Supplier</strong>
     </div>
     <div class="panel-body">
       <form method="post" action="edit_supplier.php?id=<?php echo (int)$supplier['id']; ?>">
          <div class="form-group">
              <label for="name">Supplier Name</label>
              <input type="text" class="form-control" name="name" value="<?php echo remove_junk($supplier['name']); ?>" required>
          </div>
          <div class="form-group">
              <label for="contact_person">Contact Person</label>
              <input type="text" class="form-control" name="contact_person" value="<?php echo remove_junk($supplier['contact_person']); ?>">
          </div>
          <div class="form-group">
              <label for="phone">Phone</label>
              <input type="text" class="form-control" name="phone" value="<?php echo remove_junk($supplier['phone']); ?>">
          </div>
          <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email" value="<?php echo remove_junk($supplier['email']); ?>">
          </div>
          <div class="form-group">
              <label for="address">Address</label>
              <textarea class="form-control" name="address"><?php echo remove_junk($supplier['address']); ?></textarea>
          </div>
          <button type="submit" name="update_supplier" class="btn btn-success">Update</button>
          <a href="suppliers.php" class="btn btn-danger">Cancel</a>
       </form>
     </div>
   </div>
 </div>
</div>

<?php include_once('layouts/footer.php'); ?>
