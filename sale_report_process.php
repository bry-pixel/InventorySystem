<?php
$page_title = 'Sales Report';
require_once('includes/load.php');
page_require_level(4);

if (isset($_POST['submit'])) {
    $req_dates = ['start-date', 'end-date'];
    validate_fields($req_dates);

    if (empty($errors)) {
        $start_date = remove_junk($db->escape($_POST['start-date']));
        $end_date   = remove_junk($db->escape($_POST['end-date']));
        $results    = find_sale_by_dates($start_date, $end_date);
    } else {
        $session->msg("d", $errors);
        redirect('sales_report.php', false);
    }
} else {
    $session->msg("d", "Please select a valid date range.");
    redirect('sales_report.php', false);
}
?>


<link rel="stylesheet" href="libs/css/boostrap.css"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<link rel="stylesheet" href="libs/css/main.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "SF Pro Display", Arial, sans-serif;
  background: #f8f9fa;
  background-image:url('bg image.jfif');
  background-size: cover;
  background-attachment: fixed;
}
.panel {
  border-radius: 12px;
  overflow: hidden;
}
.panel-heading {
  font-size: 1.25rem;
  font-weight: bold;
}
.table th {
  background: #f1f1f1;
}
.table tfoot td {
  background: #fafafa;
}
.btn {
  border-radius: 30px;
  padding: 10px 25px;
  transition: 0.3s ease-in-out;
}
.btn:hover {
  opacity: 0.9;
}
@media print {
  .no-print { display: none !important; }
  body { font-size: 12px; background: #fff; }
  table { font-size: 11px; }
  .panel { border: none; box-shadow: none; }
  #header, 
    .sidebar,
    .info-menu,
    .no-print {
      display: none !important;
    }
    body {
      background: #fff !important;
    }
    .page, .container-fluid {
      margin: 0;
      padding: 0;
      width: 100%;
    }
}
</style>

<div class="container mt-5" style="margin-top:40px;">
  <?php if (!empty($results)): ?>
    <div class="panel panel-primary shadow-sm">
      <div class="panel-heading text-center no-print">
        <h3 class="panel-title">
          <span class="glyphicon glyphicon-list-alt"></span> Sales Report
        </h3>
      </div>

      <div class="panel-body p-4">
        <!-- Report Header -->
        <div class="text-center mb-4">
          <h2><strong>Inventory Management System</strong></h2>
          <p class="text-muted">
            <span class="glyphicon glyphicon-calendar"></span>
            Report Period: 
            <strong><?= htmlspecialchars($start_date); ?></strong> 
            to 
            <strong><?= htmlspecialchars($end_date); ?></strong>
          </p>
        </div>

        <!-- Report Table -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">Date</th>
                <th>Product Title</th>
                <th class="text-right">Buying Price</th>
                <th class="text-right">Selling Price</th>
                <th class="text-center">Total Qty</th>
                <th class="text-right">Total Sales</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $result): ?>
              <tr>
                <td class="text-center"><?= date("M d, Y", strtotime($result['sale_date'])); ?></td>
                <td><strong><?= remove_junk(ucfirst($result['name'])); ?></strong></td>
                <td class="text-right">₱<?= number_format((float)$result['buy_price'], 2); ?></td>
                <td class="text-right">₱<?= number_format((float)$result['sale_price'], 2); ?></td>
                <td class="text-center"><?= (int)$result['total_sales'] . ' ' . $result['unit']; ?></td>
                <td class="text-right"><strong>₱<?= number_format((float)$result['total_saleing_price'], 2); ?></strong></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr style="font-weight:bold;">
                <td colspan="4"></td>
                <td>Grand Total</td>
                <td>₱<?= number_format(total_price($results)[0], 2); ?></td>
              </tr>
              <tr style="font-weight:bold;">
                <td colspan="4"></td>
                <td>Profit</td>
                <td>₱<?= number_format(total_price($results)[1], 2); ?></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Actions -->
        <div class="text-center mt-3 no-print">
          <a href="sales_report.php" class="btn btn-primary">
            <span class="glyphicon glyphicon-arrow-left"></span> Back to Report
          </a>
          <button onclick="window.print()" class="btn btn-success">
            <span class="glyphicon glyphicon-print"></span> Print Report
          </button>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger text-center">
      <span class="glyphicon glyphicon-warning-sign"></span> No sales found for the selected date range.
    </div>
    <div class="text-center no-print">
      <a href="sales_report.php" class="btn btn-default">Go Back</a>
    </div>
  <?php endif; ?>
</div>

<?php include_once('layouts/footer.php'); ?>
<?php if (isset($db)) { $db->db_disconnect(); } ?>
