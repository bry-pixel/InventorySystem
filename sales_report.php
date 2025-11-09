```php
<?php
$page_title = 'Sales Report';
require_once('includes/load.php');
page_require_level(4);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="panel panel-default shadow-sm" style="border-radius:10px; overflow:hidden;">
      
      <!-- Panel Header -->
      <div class="panel-heading bg-primary text-white" style="padding:15px;">
        <h3 class="panel-title" style="margin:0; font-size:18px; font-weight:600;">
          <span class="glyphicon glyphicon-stats"></span> Generate Sales Report
        </h3>
      </div>

      <!-- Panel Body -->
      <div class="panel-body" style="background:#fdfdfd; padding:25px;">
        <form method="post" action="sale_report_process.php">
          
          <!-- Date Range -->
          <div class="form-group mb-4">
            <label for="date-range" class="form-label" style="font-weight:500;">Select Date Range</label>
            <div class="input-group" style="display:flex; gap:10px;">
              <input type="date" class="form-control" name="start-date" id="start-date" required>
              <span class="input-group-addon" style="display:flex; align-items:center; padding:0 10px;">
                <i class="glyphicon glyphicon-menu-right"></i>
              </span>
              <input type="date" class="form-control" name="end-date" id="end-date" required>
            </div>
            <small class="text-muted d-block mt-2">Choose the start and end dates to filter sales records.</small>
          </div>

          <!-- Action Buttons -->
          <div class="form-group text-right mt-4">
            <button type="reset" class="btn btn-default" style="border-radius:30px; padding:8px 20px;">
              <span class="glyphicon glyphicon-refresh"></span> Reset
            </button>
            <button type="submit" name="submit" class="btn btn-primary" style="border-radius:30px; padding:8px 20px;">
              <span class="glyphicon glyphicon-stats"></span> Generate Report
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
