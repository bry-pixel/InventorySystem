<?php
require_once('includes/load.php');
page_require_level(4);

// =======================
//   SALES PER PRODUCT
// =======================
$product_sales = $db->query("
    SELECT p.name AS product_name, SUM(s.qty * s.price) AS total_sales
    FROM sales s
    JOIN products p ON s.product_id = p.id
    GROUP BY s.product_id
") or die("Error (Product Sales): " . $db->error);

// =======================
//   SALES PER CATEGORY
// =======================
$category_sales = $db->query("
    SELECT c.name AS category_name, SUM(s.qty * s.price) AS total_sales
    FROM sales s
    JOIN products p ON s.product_id = p.id
    JOIN categories c ON p.categorie_id = c.id
    GROUP BY c.id
") or die("Error (Category Sales): " . $db->error);

// =======================
//   MONTHLY SALES TREND
// =======================
$monthly_sales = $db->query("
    SELECT DATE_FORMAT(s.date, '%Y-%m') AS month, SUM(s.qty * s.price) AS total_sales
    FROM sales s
    GROUP BY month
    ORDER BY month
") or die("Error (Monthly Sales): " . $db->error);
?>

<?php include_once('layouts/header.php'); ?>

<style>
.dashboard-container {
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
.card-title {
  font-size: 1.50rem;
  font-weight: 600;
  color: #222;
  text-align: center;
  margin-bottom: 1.25rem;
}
canvas {
  max-height: 320px !important;
}
.page-title {
  text-align: center;
  font-weight: 700;
  color: white;
  margin-top: 15px;
  margin-bottom: 30px;
  font-size: 1.9rem;
}
</style>

<div class="dashboard-container">
  <div class="row g-4">
    <!-- Sales per Product -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Sales per Product</h5>
          <canvas id="productChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Sales per Category -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Sales per Category</h5>
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Monthly Sales Trend -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Monthly Sales Chart</h5>
          <canvas id="monthlyChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
<?php
$product_data = $product_sales->fetch_all(MYSQLI_ASSOC);
$category_data = $category_sales->fetch_all(MYSQLI_ASSOC);
$monthly_data = $monthly_sales->fetch_all(MYSQLI_ASSOC);
?>

const productLabels = <?= json_encode(array_column($product_data, 'product_name')) ?>;
const productData = <?= json_encode(array_column($product_data, 'total_sales')) ?>;

const categoryLabels = <?= json_encode(array_column($category_data, 'category_name')) ?>;
const categoryData = <?= json_encode(array_column($category_data, 'total_sales')) ?>;

const monthlyLabels = <?= json_encode(array_column($monthly_data, 'month')) ?>;
const monthlyData = <?= json_encode(array_column($monthly_data, 'total_sales')) ?>;

// Product Sales Chart
new Chart(document.getElementById('productChart'), {
  type: 'bar',
  data: {
    labels: productLabels,
    datasets: [{
      label: '₱',
      data: productData,
      backgroundColor: 'rgba(54, 162, 235, 0.7)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
      borderRadius: 5
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});

// Category Sales Chart
new Chart(document.getElementById('categoryChart'), {
  type: 'doughnut',
  data: {
    labels: categoryLabels,
    datasets: [{
      data: categoryData,
      backgroundColor: [
        '#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#20c997','#fd7e14','#e83e8c'
      ]
    }]
  },
  options: { responsive: true }
});

// Monthly Sales Chart
new Chart(document.getElementById('monthlyChart'), {
  type: 'line',
  data: {
    labels: monthlyLabels,
    datasets: [{
      label: '',
      data: monthlyData,
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13,110,253,0.15)',
      fill: true,
      tension: 0.35
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>

<?php include_once('layouts/footer.php'); ?>
