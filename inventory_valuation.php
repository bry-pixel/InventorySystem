<?php
$page_title = 'All Products + Inventory Evaluation';
require_once('includes/load.php');
page_require_level(4);
include_once('layouts/header.php');

$categories = find_by_sql("SELECT id, name FROM categories ORDER BY name ASC");

$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.sale_price, p.date,
               c.name AS categorie, m.file_name AS image, p.media_id, p.unit
        FROM products p
        LEFT JOIN categories c ON c.id = p.categorie_id
        LEFT JOIN media m ON m.id = p.media_id
        WHERE 1=1 ";

if ($category > 0) {
    $sql .= " AND p.categorie_id = {$category} ";
}

$sql .= " ORDER BY p.name ASC";
$products = find_by_sql($sql);

// ---------------- Inventory Evaluation Data ----------------
$total_products   = count($products);
$total_stock      = array_sum(array_column($products, 'quantity'));
$total_cost       = array_sum(array_map(fn($p) => $p['buy_price'] * $p['quantity'], $products));
$total_sales_val  = array_sum(array_map(fn($p) => $p['sale_price'] * $p['quantity'], $products));

$low_stock    = array_filter($products, fn($p) => $p['quantity'] <= 10 && $p['quantity'] > 0);
$out_of_stock = array_filter($products, fn($p) => $p['quantity'] == 0);

$fast_movers = array_slice($products, 0, 5);
$slow_movers = array_slice(array_reverse($products), 0, 5);
?>

<?php display_msg($msg); ?>

<!-- QUICK STATS / KPI CARDS -->
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-th"></i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"><?= $total_products; ?></h2>
                <p class="text-muted">Total Products</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-inbox"></i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"><?= $total_stock; ?></h2>
                <p class="text-muted">Total Stock Units</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue2"><i>₱</i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top">₱<?= number_format($total_cost,2); ?></h2>
                <p class="text-muted">Inventory Cost</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-piggy-bank"></i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top">₱<?= number_format($total_sales_val,2); ?></h2>
                <p class="text-muted">Sales Value</p>
            </div>
        </div>
    </div>
</div>

<!-- ALERTS -->
<div class="row">
  <div class="col-md-6">
    <?php if(!empty($low_stock)): ?>
    <div class=" alert-warning" style="padding:15px; border-radius:5px; margin-left:10px; margin-right:10px; margin-top:10px; margin-bottom:10px;
background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:5px; box-shadow:0 2px 4px rgba(0,0,0,0.1);
    ">
      <h4><i class="glyphicon glyphicon-alert"></i> Low Stock</h4>
      <ul>
        <?php foreach ($low_stock as $p): ?>
          <li><strong><?= $p['name']; ?></strong> – <?= $p['quantity'].' '.$p['unit']; ?> left</li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
  
  <div class="col-md-6">
    <?php if(!empty($out_of_stock)): ?>
    <div class="alert-danger" style="padding:15px; border-radius:5px;margin-left:10px; margin-right:10px; margin-top:10px; margin-bottom:10px;
background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:5px; box-shadow:0 2px 4px rgba(0,0,0,0.1);
    ">
      <h4><i class="glyphicon glyphicon-remove"></i> Out of Stock</h4>
      <ul>
        <?php foreach ($out_of_stock as $p): ?>
          <li><strong><?= $p['name']; ?></strong></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-flash"></i> Fast Movers</div>
      <div class="panel-body">
        <table class="table table-bordered table-condensed table-hover">
          <thead><tr><th>Product</th><th>Stock</th></tr></thead>
          <tbody>
          <?php foreach ($fast_movers as $p): ?>
            <tr><td><?= $p['name']; ?></td><td><?= $p['quantity'].' '.$p['unit']; ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-time"></i> Slow Movers</div>
      <div class="panel-body">
        <table class="table table-bordered table-condensed table-hover">
          <thead><tr><th>Product</th><th>Stock</th></tr></thead>
          <tbody>
          <?php foreach ($slow_movers as $p): ?>
            <tr><td><?= $p['name']; ?></td><td><?= $p['quantity'].' '.$p['unit']; ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
