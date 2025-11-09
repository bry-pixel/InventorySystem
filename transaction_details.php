<?php
require_once('includes/load.php');
page_require_level(4);

$txn_id = (int)($_GET['id'] ?? 0);
if ($txn_id <= 0) {
  echo '<div class="alert alert-danger text-center"><i class="glyphicon glyphicon-warning-sign"></i> Invalid transaction ID.</div>';
  exit;
}

$txn = find_by_id('transactions', $txn_id);
if (!$txn) {
  
  echo '<div class="alert alert-danger text-center"><i class="glyphicon glyphicon-warning-sign"></i> Transaction not found.</div>';
  exit;
}

$txn_time = $txn['txn_time'] ?? '';
$items = function_exists('find_transaction_items') ? find_transaction_items($txn_id) : [];
$total = 0;
foreach ($items as $i) {
  $total += (float)$i['price'] * (float)$i['qty'];
}
?>

<div class="panel panel-default shadow-sm" style="border-radius:10px;">
  <div class="panel-heading clearfix bg-primary text-white" style="border-radius:10px 10px 0 0;">
    <strong><i class="glyphicon glyphicon-list"></i> Transaction #<?php echo (int)$txn_id; ?></strong>
    <div class="pull-right">
      <small><i class="glyphicon glyphicon-time"></i> <?php echo date('M d, Y h:i A', strtotime($txn_time)); ?></small>
    </div>
  </div>

  <div class="panel-body">
    <?php if (!empty($items)): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th>Product</th>
              <th class="text-center" style="width: 15%;">Quantity</th>
              <th class="text-center" style="width: 15%;">Unit Price</th>
              <th class="text-right" style="width: 15%;">Line Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): 
              $line_total = (float)$item['qty'] * (float)$item['price']; ?>
              <tr>
                <td><?php echo remove_junk($item['name']); ?></td>
                <td class="text-center"><?php echo (float)$item['qty'] . ' ' . $item['unit']; ?></td>
                <td class="text-center">₱<?php echo number_format((float)$item['price'], 2); ?></td>
                <td class="text-right">₱<?php echo number_format($line_total, 2); ?></td>
              </tr>
            <?php endforeach; ?>
            <tr class="bg-light">
              <td colspan="3" class="text-right"><strong>Total</strong></td>
              <td class="text-right"><strong>₱<?php echo number_format($total, 2); ?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center mb-0">
        <i class="glyphicon glyphicon-info-sign"></i> No items found for this transaction.
      </div>
    <?php endif; ?>
  </div>
</div>
