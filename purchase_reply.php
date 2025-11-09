<?php
require_once('includes/load.php');
$page_title = 'Purchase Reply';
include_once('layouts/header.php');

// Get parameters
$purchase_id = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;
$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

// Validate IDs
if (!$purchase_id || !$supplier_id) {
  echo "<div class='alert alert-danger text-center mt-5'>⚠️ Invalid access. Missing purchase or supplier ID.</div>";
  include_once('layouts/footer.php');
  exit;
}

// Fetch records
$purchase = find_by_id('purchases', $purchase_id);
$supplier = find_by_id('suppliers', $supplier_id);

if (!$purchase || !$supplier) {
  echo "<div class='alert alert-danger text-center mt-5'>❌ Invalid purchase or supplier record.</div>";
  include_once('layouts/footer.php');
  exit;
}

// Handle form submission
if (isset($_POST['submit_reply'])) {
  $reply_message = remove_junk($db->escape($_POST['reply_message']));

  if (empty($reply_message)) {
    $session->msg('d', "Reply message cannot be empty.");
  } else {
    $query  = "INSERT INTO purchase_replies (purchase_id, supplier_id, reply_message) ";
    $query .= "VALUES ('{$purchase_id}', '{$supplier_id}', '{$reply_message}')";

    if ($db->query($query)) {
      // Log and message
      log_activity('Reply Received', "Supplier {$supplier['name']} replied to Purchase #{$purchase_id}", 'purchase_reply.php');
      $session->msg('s', "✅ Your reply has been submitted successfully. Thank you!");
    } else {
      $session->msg('d', "❌ Failed to submit your reply. Please try again.");
    }
  }
}

// Fetch last reply (optional display)
$last_reply = find_by_sql("
  SELECT reply_message, replied_at 
  FROM purchase_replies 
  WHERE purchase_id = {$purchase_id} 
  ORDER BY replied_at DESC 
  LIMIT 1
");
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="row justify-content-center mt-5">
  <div class="col-md-8 col-lg-6">
    <?php echo display_msg($msg); ?>
    <div class="panel panel-default shadow-sm" style="border-radius:8px;">
      <div class="panel-heading" style="background:#007bff;color:white;">
        <strong><i class="glyphicon glyphicon-envelope"></i> Purchase Reply</strong>
      </div>
      <div class="panel-body">
        <p><strong>Supplier:</strong> <?php echo remove_junk($supplier['name']); ?></p>
        <p><strong>Purchase ID:</strong> #<?php echo (int)$purchase['id']; ?></p>
        <p><strong>Quantity:</strong> <?php echo remove_junk($purchase['quantity']); ?></p>
        <p><strong>Request Date:</strong> <?php echo read_date($purchase['purchase_date']); ?></p>
        <hr>

        <?php if (!empty($last_reply)): ?>
          <div class="alert alert-info">
            <strong>Last Reply:</strong> <?php echo remove_junk($last_reply[0]['reply_message']); ?>
            <br><small><em>Sent on <?php echo date('M d, Y h:i A', strtotime($last_reply[0]['replied_at'])); ?></em></small>
          </div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="form-group">
            <label for="reply_message">Your Reply / Confirmation</label>
            <textarea name="reply_message" id="reply_message" class="form-control" rows="5" placeholder="Type your confirmation, ETA, or message..." required></textarea>
          </div>
          <div class="form-group text-center mt-3">
            <button type="submit" name="submit_reply" class="btn btn-success btn-lg">
              <i class="glyphicon glyphicon-send"></i> Send Reply
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
