<?php
$page_title = 'All Products';
require_once('includes/load.php');
include_once('layouts/header.php');
page_require_level(4);

$categories = find_by_sql("SELECT id, name FROM categories ORDER BY name ASC");

$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Sorting
$valid_columns = ['name','buy_price','sale_price','date','status'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'],$valid_columns) ? $_GET['sort'] : 'name';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

// Helper: show arrow
function sort_icon($column, $sort, $order) {
    if ($column == $sort) {
        return $order === 'ASC' ? " ↑" : " ↓";
    }
    return "";
}

// Helper: per-column next order
function next_order($column, $sort, $order) {
    if ($column == $sort) {
        return ($order === 'ASC') ? 'desc' : 'asc';
    }
    return 'asc'; 
}

$sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.sale_price, p.date,
               c.name AS categorie, m.file_name AS image, p.media_id, p.unit,
               CASE 
                    WHEN p.quantity <= 10 THEN 'Low'
                    WHEN p.quantity <= 100 THEN 'Medium'
                    ELSE 'High'
               END AS status
        FROM products p
        LEFT JOIN categories c ON c.id = p.categorie_id
        LEFT JOIN media m ON m.id = p.media_id
        WHERE 1=1 ";

if ($category > 0) {
    $sql .= " AND p.categorie_id = {$category} ";
}

// Special ORDER BY for status logical sorting
if ($sort === 'status') {
    $sql .= " ORDER BY 
                CASE 
                  WHEN p.quantity <= 10 THEN 1
                  WHEN p.quantity <= 100 THEN 2
                  ELSE 3
                END {$order}";
} else {
    $sql .= " ORDER BY p.{$sort} {$order}";
}

$products = find_by_sql($sql);
?>

<div class="row">
  <div class="col-md-12">
    <?= display_msg($msg); ?>
  </div>

  <div class="col-md-12">
    <div class="panel panel-default shadow-sm">

      <!-- Panel Header -->
      <div class="panel-heading d-flex justify-content-between align-items-center" 
           style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h3 class="panel-title" style="margin:0;">
          <span class="glyphicon glyphicon-barcode"></span> Products List
        </h3>

        <!-- Centered Live Search + Filter -->
        <div class="text-center" style="margin: 0;">
          <form method="GET" class="form-inline d-inline-block" style="display:inline-block;">
            <select name="category" class="form-control input-sm" style="margin-right:5px; font-size:14px;" onchange="this.form.submit()">
              <option value="0">All Categories</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($category==$cat['id'])?'selected':''; ?>>
                  <?= remove_junk($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="text" class="form-control input-m live-search" placeholder="Search product..." 
                   style="width:220px; margin-right:5px; font-size:14px;">
          </form>
        </div>

        <!-- Add Product Button -->
        <div class="pull-right">
          <a href="inventory_valuation.php" class="btn btn-primary btn-sm" 
             style="border-radius:20px; font-size:14px;">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory evaluation
          </a>
        </div>
      </div>

      <!-- Table -->
      <div class="panel-body">
        <?php if (!empty($products)): ?>
        <table class="table table-hover table-bordered table-striped" id="productsTable">
          <thead style="background:#f8f9fa;">
            <tr>
              <th style="width:50px;">#</th>
              <th>Photo</th>
              <th>
                <a href="?sort=name&order=<?= next_order('name',$sort,$order) ?>&category=<?= $category ?>">
                  Product Title<?= sort_icon('name',$sort,$order) ?>
                </a>
              </th>
              <th>Category</th>
              <th>In Stock</th>
              <th>
                <a href="?sort=buy_price&order=<?= next_order('buy_price',$sort,$order) ?>&category=<?= $category ?>">
                  Buying Price<?= sort_icon('buy_price',$sort,$order) ?>
                </a>
              </th>
              <th>
                <a href="?sort=sale_price&order=<?= next_order('sale_price',$sort,$order) ?>&category=<?= $category ?>">
                  Selling Price<?= sort_icon('sale_price',$sort,$order) ?>
                </a>
              </th>
              <th>
                <a href="?sort=date&order=<?= next_order('date',$sort,$order) ?>&category=<?= $category ?>">
                  Added On<?= sort_icon('date',$sort,$order) ?>
                </a>
              </th>
              <th>
                <a href="?sort=status&order=<?= next_order('status',$sort,$order) ?>&category=<?= $category ?>">
                  Status<?= sort_icon('status',$sort,$order) ?>
                </a>
              </th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td class="text-center"><strong><?= count_id(); ?></strong></td>
                <td class="text-center">
                  <?php if($product['media_id'] === '0'): ?>
                    <img src="uploads/products/no_image.png" class="img-thumbnail" style="width:50px; height:50px;">
                  <?php else: ?>
                    <img src="uploads/products/<?= $product['image']; ?>" class="img-thumbnail" style="width:50px; height:50px;">
                  <?php endif; ?>
                </td>
                <td><strong><?= remove_junk($product['name']); ?></strong></td>
                <td class="text-center"><?= remove_junk($product['categorie']); ?></td>
                <td class="text-center"><?= (int)$product['quantity'] . ' ' . $product['unit']; ?></td>
                <td class="text-center">₱<?= number_format($product['buy_price'], 2); ?></td>
                <td class="text-center">₱<?= number_format($product['sale_price'], 2); ?></td>
                <td class="text-center"><?= date("M d, Y", strtotime($product['date'])); ?></td>
                <td class="text-center">
                  <?php if ($product['status'] == 'Low'): ?>
                    <span class="label label-danger" style="font-size: small;">Low</span>
                  <?php elseif ($product['status'] == 'Medium'): ?>
                    <span class="label label-warning" style="font-size: small;">Medium</span>
                  <?php else: ?>
                    <span class="label label-success" style="font-size: small;">High</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <!-- No results row (hidden by default, shown by JS) -->
            <tr id="noResultsRow" style="display:none;">
              <td colspan="10" class="text-center text-muted">No results found.</td>
            </tr>
          </tbody>
        </table>
        <?php else: ?>
          <div class="alert alert-info text-center">No products found.</div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <nav aria-label="Transaction pagination">
              <ul class="pagination pagination-sm" id="paginationControls" style="margin:0;"></ul>
            </nav>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Standardized Reusable Modal -->
<div id="globalModal" class="modal-overlay">
  <div class="modal-content">
    <div id="modal-body">Loading...</div>
  </div>
</div>

<style>
/* Modal Overlay */
.modal-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  z-index: 9999;
}

/* Modal Box */
.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  width: 70%;
  max-width: 800px;
  max-height: 85%;
  overflow-y: auto;
  box-shadow: 0 6px 20px rgba(0,0,0,0.35);
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  animation: fadeIn 0.3s ease-out;
}

/* Close Button */
.modal-close {
  float: right;
  font-size: 22px;
  font-weight: bold;
  cursor: pointer;
  color: red;
}

/* Fade Animation */
@keyframes fadeIn {
  from {opacity: 0; transform: translate(-50%, -40%);}
  to {opacity: 1; transform: translate(-50%, -50%);}
}
</style>

<script>
// Open modal with given URL
function openModal(url) {
    const modal = document.getElementById("globalModal");
    const body  = document.getElementById("modal-body");
    modal.style.display = "block";
    body.innerHTML = "Loading...";

    fetch(url)
      .then(res => res.text())
      .then(html => { body.innerHTML = html; })
      .catch(() => { body.innerHTML = "Error loading content."; });
}

// Close modal
function closeModal() {
    document.getElementById("globalModal").style.display = "none";
}

// Close modal if user clicks outside
window.onclick = function(event) {
    const modal = document.getElementById("globalModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// ESC key closes modal
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") closeModal();
});

// Live Search
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.querySelector("input.live-search");
  const tableRows = document.querySelectorAll("#productsTable tbody tr:not(#noResultsRow)");
  const noResultsRow = document.querySelector("#noResultsRow");

  if (searchInput) {
    searchInput.addEventListener("keyup", function() {
      const query = this.value.toLowerCase().trim();
      let matches = 0;

      tableRows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const match = text.includes(query);
        row.style.display = match ? "" : "none";
        if (match) matches++;
      });

      noResultsRow.style.display = matches === 0 ? "" : "none";
    });
  }
});

// Pagination for products table
document.addEventListener("DOMContentLoaded", function() {
  const rowsPerPage = 10;
  const table = document.getElementById("productsTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr:not(#noResultsRow)"));
  const paginationControls = document.getElementById("paginationControls");
  let currentPage = 1;
  let totalPages = Math.ceil(rows.length / rowsPerPage);
  const noResultsRow = document.getElementById("noResultsRow");
  if (rows.length === 0) {
    noResultsRow.style.display = "";
    paginationControls.style.display = "none";
    return;
  } else {
    noResultsRow.style.display = "none";
    paginationControls.style.display = "";
  }
  function renderTable() {
    tbody.querySelectorAll("tr").forEach(r => r.style.display = "none");
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.slice(start, end).forEach(r => r.style.display = "");
    renderPagination();
  }
  function renderPagination() {
    paginationControls.innerHTML = "";
    if (totalPages <= 1) return;
    const createPageItem = (page, text = null, disabled = false, active = false) => {
      const li = document.createElement("li");
      li.className = "page-item" + (disabled ? " disabled" : "") + (active ? " active" : "");
      const a = document.createElement("a");
      a.className = "page-link";
      a.href = "#";
      a.innerText = text || page;
      a.addEventListener("click", function(e) {
        e.preventDefault();
        if (!disabled && currentPage !== page) {
          currentPage = page;
          renderTable();
        }
      });
      li.appendChild(a);
      return li;
    };
    // Previous
    paginationControls.appendChild(createPageItem(currentPage - 1, "« Prev", currentPage === 1));
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      paginationControls.appendChild(createPageItem(i, null, false, currentPage === i));
    }
    // Next
    paginationControls.appendChild(createPageItem(currentPage + 1, "Next »", currentPage === totalPages));
  }
  renderTable();
});
</script>

<?php include_once('layouts/footer.php'); ?>
