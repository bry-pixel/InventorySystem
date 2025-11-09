<?php
require_once('includes/load.php');
page_require_level(2);

$product = find_by_id('products', (int)$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');

if (!$product) {
    echo "<div class='alert alert-danger'>Missing product ID.</div>";
    exit;
}

if (isset($_POST['product'])) {
    $req_fields = ['product-title','product-categorie','product-quantity','buying-price','saleing-price','unit'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name   = remove_junk($db->escape($_POST['product-title']));
        $p_cat    = (int)$_POST['product-categorie'];
        $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
        $unit     = remove_junk($db->escape($_POST['unit']));
        $p_buy    = remove_junk($db->escape($_POST['buying-price']));
        $p_sale   = remove_junk($db->escape($_POST['saleing-price']));
        $media_id = empty($_POST['product-photo']) ? '0' : remove_junk($db->escape($_POST['product-photo']));

        $query  = "UPDATE products SET ";
        $query .= "name ='{$p_name}', quantity ='{$p_qty}', unit='{$unit}', ";
        $query .= "buy_price ='{$p_buy}', sale_price ='{$p_sale}', ";
        $query .= "categorie_id ='{$p_cat}', media_id='{$media_id}' ";
        $query .= "WHERE id ='{$product['id']}'";

        $result = $db->query($query);
        if ($result && $db->affected_rows() === 1)
        {
            $session->msg('s',"Product updated successfully.");
            log_activity(
              'Edit Product',
              "Updated product '{$p_name}' (ID: {$product['id']}) | Qty: {$p_qty} {$unit} | Buy: ₱{$p_buy} | Sell: ₱{$p_sale}",
              'product.php'
          );
            redirect('product.php', false);
        } else {
            $session->msg('d',' Sorry failed to update product!');
            log_activity(
              'Edit Product Failed',
              "Failed to update product '{$p_name}' (ID: {$product['id']})",
              'product.php'
          );
            redirect('product.php?id=' . (int)$product['id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('product.php?id=' . (int)$product['id'], false);
    }
}
?>

<div class="panel panel-default shadow-sm" style="border-radius:10px;">
  <div class="panel-heading bg-primary text-white" style="border-radius:10px 10px 0 0;">
    <strong><i class="glyphicon glyphicon-edit"></i> Edit Product</strong>
  </div>
  <div class="panel-body p-4">
    <form method="post" action="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="clearfix">

      <!-- Product Name -->
      <div class="form-group mb-3">
        <label for="product-title">Product Name</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
          <input type="text" class="form-control" name="product-title" id="product-title" required
                 value="<?php echo remove_junk($product['name']);?>">
        </div>
      </div>

      <!-- Category & Image -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label>Category</label>
          <select class="form-control" name="product-categorie" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($all_categories as $cat): ?>
              <option value="<?php echo (int)$cat['id']; ?>"
                <?php if($product['categorie_id'] == $cat['id']) echo "selected"; ?>>
                <?php echo remove_junk($cat['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label>Product Image</label>
          <select class="form-control" name="product-photo">
            <option value="">-- Select Photo --</option>
            <?php foreach ($all_photo as $photo): ?>
              <option value="<?php echo (int)$photo['id']; ?>"
                <?php if($product['media_id'] == $photo['id']) echo "selected"; ?>>
                <?php echo $photo['file_name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Quantity, Unit, Prices -->
      <div class="row mb-3">
        <div class="col-md-3">
          <label>Quantity</label>
          <input type="number" class="form-control" name="product-quantity" min="0" required
                 value="<?php echo remove_junk($product['quantity']); ?>">
        </div>

        <div class="col-md-3">
          <label>Unit</label>
          <select name="unit" class="form-control" required>
            <option value="">--Select Unit--</option>
            <?php
              $units = ["pcs"=>"Pieces","box"=>"Box","kg"=>"Kilograms","L"=>"Liters","dozen"=>"Dozen",
                        "set"=>"Set","roll"=>"Roll","packs"=>"Packs","bottles"=>"Bottle",
                        "cans"=>"Can","bag"=>"Bag","sheets"=>"Sheet","unit"=>"Unit"];
              foreach($units as $key=>$val){
                $selected = ($product['unit'] == $key) ? "selected" : "";
                echo "<option value='{$key}' {$selected}>{$val}</option>";
              }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Buying Price</label>
          <div class="input-group">
            <span class="input-group-addon">₱</span>
            <input type="number" class="form-control" step="0.01" name="buying-price" required
                   value="<?php echo remove_junk($product['buy_price']);?>">
          </div>
        </div>

        <div class="col-md-3">
          <label>Selling Price</label>
          <div class="input-group">
            <span class="input-group-addon">₱</span>
            <input type="number" class="form-control" step="0.01" name="saleing-price" required
                   value="<?php echo remove_junk($product['sale_price']);?>">
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="form-group text-end mt-3" style="margin-top:20px; gap:10px;">
        
        <button type="submit" name="product" class="btn btn-primary">
          <i class="glyphicon glyphicon-ok"></i> Update Product
        </button>
        <button type="button" onclick="window.parent.closeModal()" class="btn btn-secondary">
          <i class="glyphicon glyphicon-remove"></i> Cancel
        </button>
      </div>

    </form>
  </div>
</div>
