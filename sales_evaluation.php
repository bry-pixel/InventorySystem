<?php
$page_title = 'Sales Evaluation';
require_once('includes/load.php');
page_require_level(4);

function daterange_from_quick($quick) {
  $today = date('Y-m-d');
  switch ($quick) {
    //case today by hour
    case 'today':   return [$today, $today];
    case 'week':    return [date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week'))];
    case 'month':   return [date('Y-m-01'), date('Y-m-t')];
    case 'last30':  return [date('Y-m-d', strtotime('-30 days')), $today];
    default:        return [date('Y-m-d', strtotime('-30 days')), $today];
  }
}

$quick = $_GET['qf'] ?? '';
if ($quick) {
  [$start_date, $end_date] = daterange_from_quick($quick);
} else {
  $start_date = $_REQUEST['start-date'] ?? date('Y-m-d', strtotime('-30 days'));
  $end_date   = $_REQUEST['end-date'] ?? date('Y-m-d');
}

// --- Get Data ---
$summary      = se_get_sales_summary($start_date, $end_date);
$daily_series = se_get_daily_totals($start_date, $end_date);
$top_products = se_get_top_products($start_date, $end_date, 5);

include_once('layouts/header.php');
?>

<?php display_msg($msg); ?>

<!-- HEADER -->
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <div class="pull-left">
      <h4><i class="fa fa-line-chart"></i> Sales Evaluation</h4>
      <small class="text-muted text-center">Period: <b><?= $start_date; ?></b> → <b><?= $end_date; ?></b></small>
    </div>
    <div class="pull-right">
      <a href="?qf=today" class="btn btn-xs btn-primary">Today</a>
      <a href="?qf=week" class="btn btn-xs btn-primary">This Week</a>
      <a href="?qf=month" class="btn btn-xs btn-primary">This Month</a>
      <a href="?qf=last30" class="btn btn-xs btn-primary">Last 30 Days</a>
    </div>
  </div>

  <div class="panel-body">

    <!-- Filter Form -->
    <form class="form-inline mb-3" method="get" action="">
      <label>From</label>
      <input type="date" name="start-date" value="<?= $start_date; ?>" class="form-control mx-1"/>
      <label>To</label>
      <input type="date" name="end-date" value="<?= $end_date; ?>" class="form-control mx-1"/>
      <button class="btn btn-success" type="submit">Apply</button>
    </form>

    <!-- KPI CARDS -->
    <div class="row">
      <div class="col-md-3">
        <div class="panel panel-box clearfix">
          <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-piggy-bank"></i></div>
          <div class="panel-value pull-right">
            <h2 class="margin-top">₱<?= number_format($summary['total_sales'],2);?></h2>
            <p class="text-muted">Total Sales</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-box clearfix">
          <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-stats"></i></div>
          <div class="panel-value pull-right">
            <h2 class="margin-top"><?= (int)$summary['items_sold'];?></h2>
            <p class="text-muted">Items Sold</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-box clearfix">
          <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-list-alt"></i></div>
          <div class="panel-value pull-right">
            <h2 class="margin-top"><?= (int)$summary['transactions'];?></h2>
            <p class="text-muted">Transactions</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-box clearfix">
          <div class="panel-icon pull-left bg-blue2"><i class="glyphicon glyphicon-signal"></i></div>
          <div class="panel-value pull-right">
            <h2 class="margin-top">₱<?= number_format($summary['avg_sale'],2);?></h2>
            <p class="text-muted">Avg Sale</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Chart -->
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-heading"><i class="fa fa-area-chart"></i> Daily Sales Trend</div>
          <div class="panel-body" style="height:300px;">
            <canvas id="dailyChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading"><i class="fa fa-star"></i> Top Selling Products</div>
          <div class="panel-body">
            <table class="table table-striped table-bordered table-condensed table-hover">
              <thead><tr><th>Product</th><th>Qty</th><th>Total ₱</th></tr></thead>
              <tbody>
                <?php foreach ($top_products as $tp): ?>
                  <tr>
                    <td><?= remove_junk(first_character($tp['product_name'])); ?></td>
                    <td><?= (int)$tp['total_sold'].' '.$tp['unit']; ?></td>
                    <td><?= number_format($tp['total'],2); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="text-right">
<a href="sales.php" class="btn btn-default"> Back</a>
</div>
  </div>
</div>

<script>
  const dailyLabels = <?= json_encode(array_column($daily_series,'d')); ?>;
  const dailyValues = <?= json_encode(array_map('floatval', array_column($daily_series,'total'))); ?>;

  const ctx = document.getElementById('dailyChart').getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 250);
  gradient.addColorStop(0, 'rgba(13, 110, 253, 0.5)');
  gradient.addColorStop(1, 'rgba(13, 110, 253, 0.05)');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Sales (₱)',
        data: dailyValues,
        borderColor: '#0d6efd',
        backgroundColor: gradient,
        borderWidth: 2,
        pointBackgroundColor: '#0d6efd',
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
        tooltip: { callbacks: { label: ctx => '₱' + ctx.parsed.y.toLocaleString() } }
      },
      scales: {
        y: { beginAtZero: true, ticks: { callback: val => '₱' + val } },
        x: { grid: { color: 'rgba(200,200,200,0.1)' } }
      }
    }
  });
</script>

<?php include_once('layouts/footer.php'); ?>
