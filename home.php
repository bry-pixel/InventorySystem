<?php
$page_title = 'Dashboard';
require_once('includes/load.php');

$current_user = function_exists('current_user') ? current_user() : null;
$user_id = isset($current_user['id']) ? (int)$current_user['id'] : 0;
$user_level = 0;
if (isset($current_user['user_level'])) {
  $user_level = (int)$current_user['user_level'];
} elseif (isset($current_user['level'])) {
  $user_level = (int)$current_user['level'];
}
$filter_by_user = ($user_level === 2 && $user_id > 0);
$today = date('Y-m-d');
$sql_sum = "SELECT COALESCE(SUM(total),0) AS total_sales, COUNT(id) AS total_txn
            FROM transactions
            WHERE DATE(txn_time) = '{$today}'";
if ($filter_by_user) {
    $sql_sum .= " AND user_id = {$user_id}";
}
$res_sum = $db->query($sql_sum);
$summary = $res_sum ? $db->fetch_assoc($res_sum) : ['total_sales' => 0, 'total_txn' => 0];

$sql_recent = "SELECT t.id, t.total, t.txn_time,
               COALESCE((SELECT SUM(s.qty) FROM sales s WHERE s.transaction_id = t.id),0) AS total_qty,
               COALESCE((SELECT COUNT(*) FROM sales s WHERE s.transaction_id = t.id),0) AS item_count
               FROM transactions t";
if ($filter_by_user) {
    $sql_recent .= " WHERE t.user_id = {$user_id}";
}
$sql_recent .= " ORDER BY t.txn_time DESC LIMIT 8";
$recent_sales = find_by_sql($sql_recent);


$low_stock = find_by_sql("SELECT id, name, quantity, unit FROM products WHERE quantity < 10 ORDER BY quantity ASC LIMIT 8");

include_once('layouts/header.php');
?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <!-- Summary card -->
  <div class="col-md-4">
    <div class="panel panel-info" style="border-radius:10px;">
      <div class="panel-heading text-center">
        <strong>
          <?php if ($filter_by_user): ?>Your Sales Today<?php else: ?>Today's Sales (All)<?php endif; ?>
        </strong>
      </div>
      <div class="panel-body text-center">
        <h3 style="margin:6px 0;">₱<?php echo number_format((float)$summary['total_sales'], 2); ?></h3>
        <p style="margin:0;"><?php echo (int)$summary['total_txn']; ?> Transactions</p>
      </div>
    </div>
  </div>

  <!-- Quick actions -->
  <div class="col-md-4">
    <div class="panel panel-success" style="border-radius:10px;">
      <div class="panel-heading text-center"><strong>Quick Actions</strong></div>
      <div class="panel-body text-center">
        <a href="add_saleV2.php" class="btn btn-primary btn-lg" style="margin:5px;">
          <i class="glyphicon glyphicon-shopping-cart"></i> Add Sale
          </a>
        <a href="salesV2.php" class="btn btn-default btn-lg" style="margin:5px;">
          <i class="glyphicon glyphicon-list"></i> Sales History
        </a>
      </div>
    </div>
  </div>

  <!-- Low stock -->
  <div class="col-md-4">
    <div class="panel panel-danger" style="border-radius:10px;">
      <div class="panel-heading text-center"><strong>Low Stock Product</strong></div>
      <div class="panel-body">
        <?php if (!empty($low_stock)): ?>
          <ul class="list-group" style="margin:0;">
            <?php foreach ($low_stock as $item): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo remove_junk($item['name']); ?>
                <span class="badge"><?php echo (int)$item['quantity'] . ' ' . remove_junk($item['unit']); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-center text-muted" style="margin:0;">No low stock products.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Recent transactions -->
<div class="row" style="margin-top:10px;">
  <div class="col-md-12">
    <div class="panel panel-default" style="border-radius:10px;">
      <div class="panel-heading"><strong><i class="glyphicon glyphicon-time"></i> Recent Transactions</strong></div>
      <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Invoice</th>
              <th class="text-center">Items</th>
              <th class="text-center">Total Qty</th>
              <th class="text-right">Total (₱)</th>
              <th style="width:180px;">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recent_sales)): ?>
              <?php foreach ($recent_sales as $i => $tx): ?>
                <tr>
                  <td><?php echo $i+1; ?></td>
                  <td><?php echo (int)$tx['id']; ?></td>
                  <td class="text-center"><?php echo (int)$tx['item_count']; ?></td>
                  <td class="text-center"><?php echo (int)$tx['total_qty']; ?></td>
                  <td class="text-right">₱<?php echo number_format((float)$tx['total'], 2); ?></td>
                  <td><?php echo date("M d, Y h:i A", strtotime($tx['txn_time'])); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted">No recent transactions.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
