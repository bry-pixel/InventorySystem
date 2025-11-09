<?php
  $page_title = 'Edit Transaction';
  require_once('includes/load.php');
  page_require_level(2);

  $txn_id = (int)($_GET['id'] ?? 0);
  if ($txn_id <= 0) {
    $session->msg('d', 'No transaction provided.');
    redirect('sales.php', false);
  }
  $txn = find_by_id('transactions', $txn_id);
  if (!$txn) {
    $session->msg('d', 'Transaction not found.');
    log_activity(
        'Edit Transaction Failed',
        "Attempted to edit non-existent transaction (Transaction ID: {$txn_id})",
        'sales.php'
  );
    redirect('sales.php', false);
  }
  $txn_time = $txn['txn_time'];

  $items = find_transaction_items($txn_id);

  if (isset($_POST['update_transaction'])) {
    global $db;

    $ids    = $_POST['sale_id'] ?? [];
    $qtys   = $_POST['qty'] ?? [];
    $prices = $_POST['price'] ?? [];

    $errors = [];
    $updated_count = 0;

    if (count($ids) !== count($qtys) || count($ids) !== count($prices)) {
      $session->msg('d', 'Invalid form submission.');
      redirect("sales.php", false);
    }

    for ($i = 0; $i < count($ids); $i++) {
      $sale_id   = (int)$ids[$i];
      $new_qty   = (float)$qtys[$i];
      $new_price = (float)$prices[$i];

      $sale = find_by_id('sales', $sale_id);
      if (!$sale) {
        $errors[] = "Sale ID {$sale_id} not found.";
        continue;
      }
      $product = find_by_id('products', (int)$sale['product_id']);
      if (!$product) {
        $errors[] = "Product for sale ID {$sale_id} not found.";
        continue;
      }

      $old_qty = (float)$sale['qty'];
      $delta   = $new_qty - $old_qty;

      if ($delta > 0) {
        $available = (float)$product['quantity'];
        if ($available < $delta) {
          $errors[] = "Insufficient stock for {$product['name']} to increase by {$delta}. Available: {$available}";
          continue;
        }
      }

      $q = $db->escape($new_qty);
      $p = $db->escape($new_price);
      $update_sale_sql = "UPDATE sales SET qty='{$q}', price='{$p}' WHERE id='{$sale_id}'";

      if ($db->query($update_sale_sql)) {
        if ($delta > 0) {
          $d = $db->escape($delta);
          $db->query("UPDATE products SET quantity = quantity - '{$d}' WHERE id='{$sale['product_id']}'");
        } elseif ($delta < 0) {
          $d = $db->escape(abs($delta));
          $db->query("UPDATE products SET quantity = quantity + '{$d}' WHERE id='{$sale['product_id']}'");
        }
        $updated_count++;
      } else {
        $errors[] = "Failed to update item {$product['name']}: " . $db->error;
      }
    }

    if (!empty($errors)) {
      $session->msg('d', implode('<br>', $errors));
    }
    if ($updated_count > 0) {
      $session->msg('s', "{$updated_count} item(s) updated successfully.");
      log_activity(
        'Edit Transaction',
        "Updated transaction {$txn_id}. {$updated_count} item(s) modified.",
        'edit_transaction.php'
      );
    }

    redirect("sales.php", false);
  }
?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Edit Transaction</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_transaction.php?id=<?php echo (int)$txn_id; ?>">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <?php
                  $product = find_by_id('products', (int)$item['product_id']);
                  if (!$product) continue;
                ?>
                <tr>
                  <td>
                    <?php echo remove_junk(ucwords($product['name'])); ?>
                    <input type="hidden" name="sale_id[]" value="<?php echo (int)$item['id']; ?>">
                  </td>
                  <td>
                    <input type="number" step="0.01" class="form-control" name="qty[]" value="<?php echo (float)$item['qty']; ?>" required>
                  </td>
                  <td>
                    <input type="number" step="0.01" class="form-control" name="price[]" value="<?php echo (float)$item['price']; ?>" readonly>
                  </td>
                  <td>
                    <?php echo number_format((float)$item['qty'] * (float)$item['price'], 2); ?>
                  </td>
                  <td>
                  <a href="delete_sale.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-danger btn-sm" style="margin-top:15px;" onclick="return confirm('Are you sure you want to delete this item?');"><span class="glyphicon glyphicon-trash"></span></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <button type="submit" name="update_transaction" class="btn btn-primary">Update Transaction</button>
          <a href="sales.php" class="btn btn-default">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
      </div>
<?php include_once('layouts/footer.php'); ?>