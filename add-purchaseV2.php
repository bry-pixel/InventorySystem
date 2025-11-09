<?php
require_once('includes/load.php');
require 'vendor/autoload.php';
$page_title = 'Add Purchase';
page_require_level(2);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$emailUser = 'inventorys197@gmail.com';
$emailPass = 'ifce tkuq nmne cimo';  // Gmail App Password

if (isset($_POST['add_purchase'])) {
  $req_fields = array('product_id', 'supplier_id', 'quantity');
  validate_fields($req_fields);

  if (empty($errors)) {
    $product_id    = (int)$_POST['product_id'];
    $supplier_id   = (int)$_POST['supplier_id'];
    $quantity      = (int)$_POST['quantity'];
    $purchase_date = !empty($_POST['purchase_date']) ? $db->escape($_POST['purchase_date']) : date('Y-m-d');
    $description   = !empty($_POST['description']) ? $db->escape($_POST['description']) : 'N/A';

    if ($quantity <= 0) {
      $session->msg('d', "Quantity must be greater than zero.");
      redirect('add_purchase.php', false);
    }

    // Insert purchase
    $query  = "INSERT INTO purchases (product_id, supplier_id, quantity, purchase_date, description, status) ";
    $query .= "VALUES ('{$product_id}', '{$supplier_id}', '{$quantity}', '{$purchase_date}', '{$description}', 'Pending')";

    if ($db->query($query)) {
      $product  = find_by_id('products', $product_id);
      $supplier = find_by_id('suppliers', $supplier_id);

      log_activity(
        'Add Purchase',
        "Created purchase order for {$product['name']} ({$quantity} units) from {$supplier['name']}",
        'add_purchase.php'
      );

      // Send email if supplier email exists
      if ($supplier && !empty($supplier['email'])) {
        $mail = new PHPMailer(true);
        try {
          $mail->isSMTP();
          $mail->Host       = 'smtp.gmail.com';
          $mail->SMTPAuth   = true;
          $mail->Username   = $emailUser;
          $mail->Password   = $emailPass;
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port       = 587;

          $mail->setFrom($emailUser, 'Inventory System');
          $mail->addAddress($supplier['email'], $supplier['name']);
          $mail->addReplyTo($emailUser, 'Inventory Team');

          // Embed logo if exists
          $logoPath = "uploads/logo.jfif"; 
          if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'companylogo');
          }

          $preheader = "Purchase Order - {$product['name']} (Qty: {$quantity})";

          $mail->isHTML(true);
          $mail->Subject = "Purchase Order: {$product['name']} (Qty: {$quantity})";
          $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head><meta charset='UTF-8'></head>
            <body style='font-family:Arial, sans-serif; background:#f4f4f4;'>
              <span style='display:none'>{$preheader}</span>
              <div style='max-width:600px;margin:20px auto;background:#fff;padding:20px;border-radius:8px'>
                <h2 style='color:#0f5ea8'>New Purchase Order</h2>
                <p>Dear <strong>{$supplier['name']}</strong>,</p>
                <p>We would like to place the following purchase order:</p>
                <table border='1' cellpadding='10' cellspacing='0' width='100%'>
                  <tr><th>Product</th><td>{$product['name']}</td></tr>
                  <tr><th>Quantity</th><td>{$quantity}</td></tr>
                  <tr><th>Purchase Date</th><td>{$purchase_date}</td></tr>
                  " . ($description !== 'N/A' ? "<tr><th>Description</th><td>{$description}</td></tr>" : "") . "
                </table>
                <p>Please confirm availability and delivery schedule.</p>
                <p>Thank you,<br><strong>Inventory Team</strong></p>
              </div>
            </body>
            </html>
          ";

          $mail->AltBody = "Purchase Order\n\nProduct: {$product['name']}\nQuantity: {$quantity}\nPurchase Date: {$purchase_date}\n" .
                          ($description !== 'N/A' ? "Description: {$description}\n" : "") .
                          "\nThank you, Inventory Team";

          $mail->send();
          $session->msg('s', "Purchase added and email sent to supplier.");
          log_activity(
            'Email Sent',
            "Email sent to supplier {$supplier['name']} ({$supplier['email']}) for purchase of {$product['name']}",
            'add_purchase.php'
          );

        } catch (Exception $e) {
          $session->msg('w', "Purchase added, but email could not be sent. Error: {$mail->ErrorInfo}");
          log_activity(
            'Email Failed',
            "Email sending failed for supplier {$supplier['name']} ({$supplier['email']}) | Error: {$mail->ErrorInfo}",
            'add_purchase.php'
          );
        }
      } else {
        $session->msg('s', "Purchase added, but supplier email not found.");
        log_activity('Email Skipped', "Supplier {$supplier['name']} has no email address.", 'add_purchase.php');
      }

      redirect('manage_request.php', false);

    } else {
      $session->msg('d', 'Failed to add purchase!');
      log_activity('Add Purchase Failed', "Database insertion failed for product ID: {$product_id}", 'add_purchase.php');
      redirect('add_purchase.php', false);
    }

  } else {
    $session->msg("d", implode(", ", $errors));
    log_activity('Validation Error', "Purchase creation failed due to validation errors.", 'add_purchase.php');
    redirect('add_purchase.php', false);
  }
}

// Preselect dropdowns
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$selected_supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
 <div class="col-md-12">
   <?php echo display_msg($msg); ?>
   <div class="panel panel-default" style="width: 60%; margin: auto;">
     <div class="panel-heading">
       <strong><span class="glyphicon glyphicon-plus"></span> Add Purchase</strong>
     </div>
     <div class="panel-body">
       <form method="post" action="add_purchase.php">

          <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
              <option value="" disabled <?php echo $selected_product_id ? '' : 'selected'; ?>>-- Select a product --</option>
              <?php foreach(find_all('products') as $product): ?>
                <option value="<?php echo $product['id']; ?>" <?php if ($product['id'] == $selected_product_id) echo 'selected'; ?>>
                  <?php echo $product['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
              <option value="" disabled <?php echo $selected_supplier_id ? '' : 'selected'; ?>>-- Select a supplier --</option>
              <?php foreach(find_all('suppliers') as $supplier): ?>
                <option value="<?php echo $supplier['id']; ?>" <?php if ($supplier['id'] == $selected_supplier_id) echo 'selected'; ?>>
                  <?php echo $supplier['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="1" placeholder="Enter quantity" required>
          </div>

          <div class="form-group">
            <label for="purchase_date">Purchase Date</label>
            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo date('Y-m-d'); ?>" required>
          </div>

          <div class="form-group">
            <label for="description">Description <small class="text-muted">(Optional)</small></label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter notes or details"></textarea>
          </div>

          <div class="form-group">
            <button type="submit" name="add_purchase" class="btn btn-success">
              <span class="glyphicon glyphicon-ok"></span> Save
            </button>
            <a href="purchases.php" class="btn btn-danger">
              <span class="glyphicon glyphicon-remove"></span> Cancel
            </a>
          </div>

       </form>
     </div>
   </div>
 </div>
</div>
<?php include_once('layouts/footer.php'); ?>
