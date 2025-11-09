<?php
require_once('includes/load.php');
page_require_level(2);
$result = $db->query("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(qty * price) AS total_sales
    FROM sales
    GROUP BY month
    ORDER BY month ASC
");

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = [
        'month' => $row['month'],
        'total_sales' => (float)$row['total_sales']
    ];
}
$x = range(1, count($sales_data)); 
$y = array_column($sales_data, 'total_sales');

$n = count($x);
if ($n > 1) {
    $sum_x = array_sum($x);
    $sum_y = array_sum($y);
    $sum_xy = 0;
    $sum_x2 = 0;

    for ($i = 0; $i < $n; $i++) {
        $sum_xy += $x[$i] * $y[$i];
        $sum_x2 += $x[$i] * $x[$i];
    }

    $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
    $intercept = ($sum_y - $slope * $sum_x) / $n;

    // Predict next 3 months
    $future_months = [];
    for ($i = $n + 1; $i <= $n + 3; $i++) {
        $forecast = $slope * $i + $intercept;
        $month_label = date('Y-m', strtotime('+' . ($i - $n) . ' month', strtotime(end($sales_data)['month'] . '-01')));
        $future_months[] = ['month' => $month_label, 'forecast' => round($forecast, 2)];
    }
} else {
    $future_months = [];
}
?>

<?php include_once('layouts/header.php'); ?>

<style>
.dashboard-container {
  max-width: 1200px;
  margin: 0 auto;
}
.card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.07);
}
.card-body {
  padding: 2rem;
}
.page-title {
  text-align: center;
  font-weight: 700;
  color: #0d6efd;
  margin: 20px 0 30px;
}
canvas {
  max-height: 350px !important;
}
</style>

<div class="dashboard-container">
  <h2 class="page-title">📈 Demand Forecasting Dashboard</h2>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title text-center">Sales & Forecast (Next 3 Months)</h5>
      <canvas id="forecastChart"></canvas>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const pastMonths = <?= json_encode(array_column($sales_data, 'month')) ?>;
const pastSales = <?= json_encode(array_column($sales_data, 'total_sales')) ?>;
const futureMonths = <?= json_encode(array_column($future_months, 'month')) ?>;
const forecastSales = <?= json_encode(array_column($future_months, 'forecast')) ?>;

// Merge past + future for the x-axis
const allLabels = pastMonths.concat(futureMonths);

new Chart(document.getElementById('forecastChart'), {
  type: 'line',
  data: {
    labels: allLabels,
    datasets: [
      {
        label: 'Historical Sales',
        data: pastSales.concat(new Array(futureMonths.length).fill(null)),
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13,110,253,0.1)',
        fill: true,
        tension: 0.3
      },
      {
        label: 'Forecasted Sales',
        data: new Array(pastSales.length).fill(null).concat(forecastSales),
        borderColor: '#fd7e14',
        borderDash: [6, 6],
        backgroundColor: 'rgba(253,126,20,0.1)',
        fill: true,
        tension: 0.3
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: {
      y: { beginAtZero: true, title: { display: true, text: '₱ Sales' } },
      x: { title: { display: true, text: 'Month' } }
    }
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>
