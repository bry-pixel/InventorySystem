<?php
require_once('includes/load.php');
require 'vendor/autoload.php';
$page_title = 'Add Purchase';
page_require_level(2);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Gmail / SMTP credentials
$emailUser = 'inventorys197@gmail.com';
$emailPass = 'ifce tkuq nmne cimo'; 

if (isset($_POST['add_purchase'])) {
  $req_fields = array('product_id','supplier_id','quantity');
  validate_fields($req_fields);

  if (empty($errors)) {
    $product_id    = (int)$_POST['product_id'];
    $supplier_id   = (int)$_POST['supplier_id'];
    $quantity      = (int)$_POST['quantity'];
    $purchase_date = !empty($_POST['purchase_date']) 
                        ? $db->escape($_POST['purchase_date']) 
                        : date('Y-m-d');
    $description   = !empty($_POST['description']) ? $db->escape($_POST['description']) : 'N/A';

    if ($quantity <= 0) {
      $session->msg('d', "Quantity must be greater than zero.");
      log_activity('Add Failed', "Attempted to add purchase with invalid quantity: {$quantity}", 'add_purchase.php');
      redirect('add_purchase.php', false);
    }

    // Insert into DB
    $query  = "INSERT INTO purchases (product_id,supplier_id,quantity,purchase_date,description,status) ";
    $query .= "VALUES ('{$product_id}','{$supplier_id}','{$quantity}','{$purchase_date}','{$description}','Pending')";

    if ($db->query($query)) {
      $product  = find_by_id('products', $product_id);
      $supplier = find_by_id('suppliers', $supplier_id);

      log_activity(
        'Add',
        "Added purchase: Product {$product['name']} (Qty: {$quantity}) from Supplier {$supplier['name']}",
        'add_purchase.php'
      );

      if ($supplier && !empty($supplier['email'])) {
        $mail = new PHPMailer(true);
        try {
          // SMTP setup
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

          // Optional logo
          $logoPath = "uploads/logo.jfif"; 
          if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'companylogo');
          }

          $preheader = "Purchase Order - {$product['name']} (Qty: {$quantity})";

          // HTML email
          $mail->isHTML(true);
          $mail->Subject = "Purchase Order: {$product['name']} (Qty: {$quantity})";
          $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset='UTF-8'>
              <style>
                body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
                .container { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
                .header { background:#0f5ea8; padding:20px; text-align:center; color:#fff; }
                .header img { max-height:60px; }
                .content { padding:20px; }
                .content h2 { color:#28a745; margin-bottom:15px; }
                .order-table { width:100%; border-collapse:collapse; margin:20px 0; }
                .order-table th, .order-table td { border:1px solid #ddd; padding:10px; text-align:left; }
                .order-table th { background:#f8f8f8; }
                .footer { background:#f1f1f1; text-align:center; padding:15px; font-size:12px; color:#666; }
              </style>
            </head>
            <body>
              <span style='display:none'>{$preheader}</span>
              <div class='container'>
                <div class='header'>
                  " . (file_exists($logoPath) ? "<img src='cid:companylogo' alt='Company Logo'>" : "<h2>Inventory System</h2>") . "
                  <h1>New Purchase Order</h1>
                </div>
                <div class='content'>
                  <p>Dear <strong>{$supplier['name']}</strong>,</p>
                  <p>We would like to place the following purchase order. Please review and confirm availability:</p>
                  <table class='order-table'>
                    <tr><th>Product</th><td>{$product['name']}</td></tr>
                    <tr><th>Quantity</th><td>{$quantity}</td></tr>
                    <tr><th>Purchase Date</th><td>{$purchase_date}</td></tr>
                    " . ($description !== 'N/A' ? "<tr><th>Description</th><td>{$description}</td></tr>" : "") . "
                  </table>
                  <p>Please confirm availability and delivery schedule at your earliest convenience.</p>
                  <p>Thank you,<br><strong>Inventory Team</strong></p>
                </div>
                <div class='footer'>
                  <p>Please reply once corfirm</p>
                </div>
              </div>
            </body>
            </html>
          ";

          $mail->AltBody =
            "New Purchase Order\n\n" .
            "Product: {$product['name']}\n" .
            "Quantity: {$quantity}\n" .
            "Purchase Date: {$purchase_date}\n" .
            ($description !== 'N/A' ? "Description: {$description}\n" : "") .
            "\nPlease confirm availability.\n\nThank you, Inventory Team";

          $mail->send();
          log_activity(
            'Email Sent',
            "Purchase order email sent to supplier {$supplier['name']} ({$supplier['email']}) for product {$product['name']}.",
            'add_purchase.php'
          );

          $session->msg('s', "Purchase added and email sent to supplier.");
        } catch (Exception $e) {
          log_activity(
            'Email Failed',
            "Purchase added, but email failed to send to supplier {$supplier['name']}. Error: {$mail->ErrorInfo}",
            'add_purchase.php'
          );

          $session->msg('w', "Purchase added, but email could not be sent. Error: {$mail->ErrorInfo}");
        }
      } else {
        log_activity(
          'Email Skipped',
          "Purchase added, but supplier {$supplier['name']} has no email address.",
          'add_purchase.php'
        );

        $session->msg('s', "Purchase added, but supplier email not found.");
      }

      redirect('purchases.php', false);
    } else {
      log_activity('Add Failed', "Database error while adding purchase (Product ID: {$product_id})", 'add_purchase.php');
      $session->msg('d','Failed to add purchase!');
      redirect('add_purchase.php', false);
    }
  } else {
    log_activity('Validation Failed', "Purchase form validation failed: " . json_encode($errors), 'add_purchase.php');
    $session->msg("d", $errors);
    redirect('add_purchase.php', false);
  }
}

// Preselect dropdowns
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$selected_supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
?>
<div class="row">
 <div class="col-md-12">
   <?php echo display_msg($msg); ?>
   <div class="panel panel-default">
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
