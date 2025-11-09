<?php
$page_title = 'Special User Dashboard';
require_once('includes/load.php'); 
page_require_level(4); 
$low_stock_products = find_by_sql("SELECT name, quantity, unit FROM products WHERE quantity <= 10");
$top_products = get_top_products(5);
$lowest_selling = get_lowest_selling_products(5);
$chart_data = get_months_chart_data();
$recent_sales = find_recent_sale_added('5');
$recent_products = find_recent_product_added('5');
$c_categorie = count_by_id('categories');
$c_product   = count_by_id('products');
$c_sale      = count_by_id('sales');
$total_sales = find_by_sql("SELECT SUM(price*qty) AS revenue FROM sales");
$total_sales = $total_sales[0]['revenue'] ?? 0;
$total_items = find_by_sql("SELECT SUM(qty) AS total_items FROM sales");
$total_items = $total_items[0]['total_items'] ?? 0;

?>
<?php include_once('layouts/header.php'); ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">



<!-- QUICK STATS -->
<div class="row">

    <a href="productV2.php" style="color:black;">
        <div class="col-md-3">
            <div class="panel panel-box clearfix">
                <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-th"></i></div>
                <div class="panel-value pull-right">
                    <h2 class="margin-top"><?= $c_product['total'] ?></h2>
                    <p class="text-muted">Products</p>
                </div>
            </div>
        </div>
    </a>

    <a href="salesV2.php" style="color:black;">
        <div class="col-md-3">
            <div class="panel panel-box clearfix">
                <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-list-alt"></i></div>
                <div class="panel-value pull-right">
                    <h2 class="margin-top"><?= $c_sale['total'] ?></h2>
                    <p class="text-muted">Transactions</p>
                </div>
            </div>
        </div>
    </a>

    <div style="color:black;">
        <div class="col-md-3">
             <div class="panel panel-box clearfix custom-teal">
            <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-stats"></i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"><?= $total_items ?></h2>
                <p>Items Sold</p>
            </div>
        </div>
      </div>
    </div>


    <!-- Total Sales -->
     <a href="sales_evaluation.php" style="color:black;">
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-piggy-bank"></i></div>
            <div class="panel-value pull-right">
                <h2 class="margin-top">₱<?= number_format($total_sales,2) ?></h2>
                <p>Total Sales</p>
            </div>
        </div>
    </div> 
</div>
</a>


<!-- CHART-->
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-bar-chart"></i> Monthly Sales Chart</div>
      <div class="panel-body" style="height:300px;">
        <canvas id="salesChart" style="max-height:250px;"></canvas>
      </div>
    </div>
  </div>

  <!-- Latest Sales -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-th"></i> Latest Sales</div>
      <div class="panel-body">
        <table class="table table-hover table-striped table-bordered table-condensed table-responsive">
          <thead>
            <tr>
              <th>#</th>
              <th>Product</th>
              <th>Sale</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as $recent_sale): ?>
            <tr>
              <td><?= count_id(); ?></td>
              <td><a href="edit_sale.php?id=<?= (int)$recent_sale['id']; ?>"><?= remove_junk(first_character($recent_sale['name'])); ?></a></td>
              <td>₱<?= remove_junk(first_character($recent_sale['price'])); ?></td>
              <td><?= date("M d, Y", strtotime($recent_sale['date'])); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Top Selling Products -->
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-th"></i> Top Selling Products</div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed table-hover">
          <thead><tr><th>Product</th><th>Total Sold</th></tr></thead>
          <tbody>
            <?php foreach ($top_products as $tp): ?>
            <tr>
              <td><?= remove_junk(first_character($tp['name'])); ?></td>
              <td><?= (int)$tp['total_sold'] . ' ' . $tp['unit']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Lowest Selling Products -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-th"></i> Lowest Selling Products</div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed table-hover">
          <thead><tr><th>Product</th><th>Total Sold</th></tr></thead>
          <tbody>
            <?php foreach ($lowest_selling as $ls): ?>
            <tr>
              <td><?= remove_junk(first_character($ls['name'])); ?></td>
              <td><?= (int)$ls['total_sold'] . ' ' . $ls['unit']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div> 
    <!-- Recently Added Products -->
    <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="glyphicon glyphicon-th"></i> Recently Added Products</div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as $recent_product): ?>
            <div class="list-group-item clearfix">
              <h4 class="list-group-item-heading">
                <?php if($recent_product['media_id'] === '0'): ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.png">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?= $recent_product['image']; ?>">
                <?php endif; ?>
                <?= remove_junk(first_character($recent_product['name'])); ?>
                <span class="label label-info pull-right">₱<?= (int)$recent_product['sale_price']; ?></span>
              </h4>
              <span class="list-group-item-text pull-right"><?= remove_junk(first_character($recent_product['categorie'])); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
  <script>
const months = <?= json_encode($chart_data['months']); ?>;
const sales  = <?= json_encode($chart_data['sales']); ?>;

const ctx = document.getElementById('salesChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 250);
gradient.addColorStop(0, 'rgba(0, 123, 255, 0.5)');
gradient.addColorStop(1, 'rgba(0, 123, 255, 0.05)');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: months,
    datasets: [{
      label: 'Monthly Revenue',
      data: sales,
      borderColor: '#007bff',
      backgroundColor: gradient,
      borderWidth: 2,
      pointBackgroundColor: '#007bff',
      pointRadius: 3,
      tension: 0.3,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => '₱' + ctx.parsed.y.toLocaleString()
        }
      }
    },
    scales: {
      y: { ticks: { callback: val => '₱' + val } },
      x: { grid: { color: 'rgba(200,200,200,0.1)' } }
    }
  }
});

</script>
<?php include_once('layouts/footer.php'); ?>
