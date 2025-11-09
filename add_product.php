<?php
$page_title = 'Add Product';
require_once('includes/load.php');
page_require_level(2);

$all_categories = find_all('categories');
$all_photo = find_all('media');

if (isset($_POST['add_product'])) {
    $req_fields = array('product-title','product-categorie','product-quantity','buying-price','saleing-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name   = remove_junk($db->escape($_POST['product-title']));
        $p_cat    = remove_junk($db->escape($_POST['product-categorie']));
        $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
        $unit     = remove_junk($db->escape($_POST['unit']));
        $p_buy    = remove_junk($db->escape($_POST['buying-price']));
        $p_sale   = remove_junk($db->escape($_POST['saleing-price']));
        $media_id = empty($_POST['product-photo']) ? '0' : remove_junk($db->escape($_POST['product-photo']));

        $query = "INSERT INTO products (name, quantity, unit, buy_price, sale_price, categorie_id, media_id) 
                  VALUES ('{$p_name}','{$p_qty}','{$unit}','{$p_buy}','{$p_sale}','{$p_cat}','{$media_id}')";

        if ($db->query($query)) {
            $session->msg('s', "Product added successfully.");
            log_activity(
                'Add Product',
                "Added new product: {$p_name} | Qty: {$p_qty} {$unit} | Buy: ₱{$p_buy} | Sell: ₱{$p_sale}",
                'product.php'
            );

            redirect('product.php', false);
        } else {
            $session->msg('d', 'Sorry, failed to add product.');
            log_activity(
                'Add Product Failed',
                "Failed to add product: {$p_name} | Category ID: {$p_cat}",
                'product.php'
            );

            redirect('product.php', false);
        } 
    } else {
        $session->msg("d", implode(", ", $errors));
        log_activity(
            'Add Product Validation Error',
            "Validation failed: " . implode(", ", $errors),
            'add_product.php'
        );

        redirect('product.php', false);
    }
}
?>

<div class="panel panel-default" style="border-radius:10px;">
  <div class="panel-heading bg-primary" style="border-radius:10px 10px 0 0; color:#fff;">
    <strong><i class="glyphicon glyphicon-plus"></i> Add New Product</strong>
  </div>
  <div class="panel-body">
    <form method="post" action="add_product.php" class="clearfix">

      <!-- Product Name -->
      <div class="form-group">
        <label for="product-title">Product Name</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
          <input type="text" class="form-control" name="product-title" placeholder="Enter product name" required>
        </div>
      </div>

      <!-- Category & Image -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Category</label>
            <select class="form-control" name="product-categorie" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo (int)$cat['id'] ?>"><?php echo $cat['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <!-- Upload image -->
        <div class="col-md-6">
          <div class="form-group">
            <label>Product Image</label>
            <select class="form-control" name="product-photo">
              <option value="">-- Select Photo --</option>
              <?php foreach ($all_photo as $photo): ?>
                <option value="<?php echo (int)$photo['id'] ?>"><?php echo $photo['file_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Quantity -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" name="product-quantity" min="1" placeholder="0" required>
          </div>
        </div>
        <!-- Unit -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Unit Measurement</label>
            <select name="unit" class="form-control" required>
              <option value="">--Select Unit--</option>
              <option value="pcs">Pieces</option>
              <option value="box">Box</option>
              <option value="kg">Kilograms</option>
              <option value="L">Liters</option>
              <option value="dozen">Dozen</option>
              <option value="set">Set</option>
              <option value="roll">Roll</option>
              <option value="packs">Packs</option>
              <option value="bottles">Bottles</option>
              <option value="cans">Cans</option>
              <option value="bags">Bags</option>
              <option value="sheets">Sheets</option>
              <option value="unit">Unit</option>
            </select>
          </div>
        </div>
        <!-- Buying Price -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Buying Price</label>
            <div class="input-group">
              <span class="input-group-addon">₱</span>
              <input type="number" class="form-control" step="0.01" name="buying-price" placeholder="0.00" required>
            </div>
          </div>
        </div>
        <!-- Selling Price -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Selling Price</label>
            <div class="input-group">
              <span class="input-group-addon">₱</span>
              <input type="number" class="form-control" step="0.01" name="saleing-price" placeholder="0.00" required>
            </div>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="form-group text-right">
        <button  class="btn btn-default" onclick="window.parent.closeModal()" >Cancel
        </button>
        <button type="submit" name="add_product" class="btn btn-primary">
          <i class="glyphicon glyphicon-ok"></i> Save Product
        </button>
      </div>

    </form>
  </div>
</div>
