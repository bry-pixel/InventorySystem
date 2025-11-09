```php
<?php
$page_title = 'Monthly Sales';
require_once('includes/load.php');
page_require_level(4);

$year = date('Y');
$sales = monthlySales($year);

include_once('layouts/header.php');
?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default shadow-sm" style="border-radius:10px; overflow:hidden;">
      
      <!-- Panel Header -->
      <div class="panel-heading bg-primary text-white" style="padding:15px;">
        <h3 class="panel-title" style="margin:0; font-size:18px; font-weight:600;">
          <span class="glyphicon glyphicon-stats"></span> Monthly Sales Report - <?php echo $year; ?>
        </h3>
      </div>

      <!-- Panel Body -->
      <div class="panel-body" style="background:#fff; padding:20px;">
        <?php if (!empty($sales)): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
              <thead class="bg-light">
                <tr>
                  <th class="text-center" style="width:50px;">#</th>
                  <th>Product Name</th>
                  <th class="text-center" style="width:15%;">Quantity Sold</th>
                  <th class="text-center" style="width:15%;">Total Sales</th>
                  <th class="text-center" style="width:20%;">Month</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $grand_total = 0;
                  foreach ($sales as $sale): 
                    $grand_total += $sale['total_saleing_price'];
                ?>
                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><strong><?php echo remove_junk($sale['name']); ?></strong></td>
                  <td class="text-center"><?php echo (int)$sale['total_qty'] . ' ' . remove_junk($sale['unit']); ?></td>
                  <td class="text-center">₱<?php echo number_format($sale['total_saleing_price'], 2); ?></td>
                  <td class="text-center"><?php echo date("F Y", strtotime($sale['date'] . "-01")); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr style="font-weight:bold; background:#f8f9fa;">
                  <td colspan="3" class="text-right">Grand Total:</td>
                  <td class="text-center text-success">₱<?php echo number_format($grand_total, 2); ?></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info text-center" style="margin:20px 0;">
            <span class="glyphicon glyphicon-info-sign"></span> 
            <strong>No sales data found for <?php echo $year; ?>.</strong>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
```
