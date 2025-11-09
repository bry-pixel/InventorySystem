<?php
$page_title = 'All Purchases';
require_once('includes/load.php');
include_once('layouts/header.php');
page_require_level(4);

// Status Filter Options
$statuses = ['All', 'Pending', 'Received', 'Canceled'];
$status = isset($_GET['status']) && in_array($_GET['status'], $statuses) ? $_GET['status'] : 'All';

// Sorting
$valid_columns = ['product_name','supplier_name','quantity','purchase_date','status'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'],$valid_columns) ? $_GET['sort'] : 'purchase_date';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

// Helper: Show arrow icon
function sort_icon($column, $sort, $order) {
    if ($column == $sort) {
        return $order === 'ASC' ? " ↑" : " ↓";
    }
    return "";
}

// Helper: Determine next order
function next_order($column, $sort, $order) {
    if ($column == $sort) {
        return ($order === 'ASC') ? 'desc' : 'asc';
    }
    return 'asc';
}

// WHERE filter
$where = ($status !== 'All') ? "WHERE p.status='{$status}'" : "";

// SQL Query
$sql = "SELECT 
            p.id, p.product_id, p.supplier_id, p.quantity, p.purchase_date, p.status,
            pr.name AS product_name, s.name AS supplier_name
        FROM purchases p
        LEFT JOIN products pr ON pr.id = p.product_id
        LEFT JOIN suppliers s ON s.id = p.supplier_id
        {$where}
        ORDER BY {$sort} {$order}";

$purchases = find_by_sql($sql);
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
          <span class="glyphicon glyphicon-list"></span> Purchase Records
        </h3>

        <!-- Filter + Live Search -->
        <div class="text-center" style="margin:0;">
          <form method="GET" class="form-inline" style="display:inline-block;">
            <select name="status" class="form-control input-sm" style="margin-right:5px;" onchange="this.form.submit()">
              <?php foreach($statuses as $opt): ?>
                <option value="<?= $opt ?>" <?= ($status==$opt)?'selected':''; ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" class="form-control input-m live-search" placeholder="Search purchase..." 
                   style="width:220px; margin-right:5px; font-size:14px;">
          </form>
        </div>

        <!-- Add Purchase Button -->
    
      </div>

      <!-- Table -->
      <div class="panel-body">
        <?php if (!empty($purchases)): ?>
        <table class="table table-hover table-bordered table-striped" id="purchasesTable">
          <thead style="background:#f8f9fa;">
            <tr>
              <th style="width:50px;">#</th>
              <th>
                <a href="?sort=product_name&order=<?= next_order('product_name',$sort,$order) ?>&status=<?= $status ?>">
                  Product<?= sort_icon('product_name',$sort,$order) ?>
                </a>
              </th>
              <th>
                <a href="?sort=supplier_name&order=<?= next_order('supplier_name',$sort,$order) ?>&status=<?= $status ?>">
                  Supplier<?= sort_icon('supplier_name',$sort,$order) ?>
                </a>
              </th>
              <th class="text-center">
                <a href="?sort=quantity&order=<?= next_order('quantity',$sort,$order) ?>&status=<?= $status ?>">
                  Quantity<?= sort_icon('quantity',$sort,$order) ?>
                </a>
              </th>
              <th class="text-center">
                <a href="?sort=purchase_date&order=<?= next_order('purchase_date',$sort,$order) ?>&status=<?= $status ?>">
                  Date<?= sort_icon('purchase_date',$sort,$order) ?>
                </a>
              </th>
              <th class="text-center">
                <a href="?sort=status&order=<?= next_order('status',$sort,$order) ?>&status=<?= $status ?>">
                  Status<?= sort_icon('status',$sort,$order) ?>
                </a>
              </th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($purchases as $purchase): ?>
              <tr>
                <td class="text-center"><strong><?= count_id(); ?></strong></td>
                <td><?= remove_junk($purchase['product_name'] ?: '<i>Unknown</i>'); ?></td>
                <td><?= remove_junk($purchase['supplier_name'] ?: '<i>Unknown</i>'); ?></td>
                <td class="text-center"><?= (int)$purchase['quantity']; ?></td>
                <td class="text-center"><?= date("M d, Y", strtotime($purchase['purchase_date'])); ?></td>
                <td class="text-center">
                  <?php 
                    if ($purchase['status'] == 'Pending') {
                        echo "<span class='label label-warning' style='font-size:small;'>Pending</span>";
                    } elseif ($purchase['status'] == 'Received') {
                        echo "<span class='label label-success' style='font-size:small;'>Received</span>";
                    } else {
                        echo "<span class='label label-danger' style='font-size:small;'>Canceled</span>";
                    }
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr id="noResultsRow" style="display:none;">
              <td colspan="7" class="text-center text-muted">No results found.</td>
            </tr>
          </tbody>
        </table>
        <?php else: ?>
          <div class="alert alert-info text-center">No purchases found.</div>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="text-center mt-3">
          <nav aria-label="Transaction pagination">
            <ul class="pagination pagination-sm" id="paginationControls" style="margin:0;"></ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reusable Modal -->
<div id="globalModal" class="modal-overlay">
  <div class="modal-content">
    <div id="modal-body">Loading...</div>
  </div>
</div>

<style>
.modal-overlay {
  display: none; position: fixed; top: 0; left: 0;
  width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999;
}
.modal-content {
  background: #fff; border-radius: 12px; padding: 20px;
  width: 70%; max-width: 800px; max-height: 85%; overflow-y: auto;
  box-shadow: 0 6px 20px rgba(0,0,0,0.35);
  position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
  animation: fadeIn 0.3s ease-out;
}
.modal-close { float:right; font-size:22px; font-weight:bold; color:red; cursor:pointer; }
@keyframes fadeIn { from {opacity:0; transform:translate(-50%, -40%);} to {opacity:1; transform:translate(-50%, -50%);} }
</style>

<script>
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

function closeModal() { document.getElementById("globalModal").style.display = "none"; }
window.onclick = e => { if (e.target.id === "globalModal") closeModal(); };
document.addEventListener("keydown", e => { if (e.key === "Escape") closeModal(); });

// Live Search
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.querySelector("input.live-search");
  const tableRows = document.querySelectorAll("#purchasesTable tbody tr:not(#noResultsRow)");
  const noResultsRow = document.querySelector("#noResultsRow");

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
});

// Pagination
document.addEventListener("DOMContentLoaded", function() {
  const rowsPerPage = 18;
  const table = document.getElementById("purchasesTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr:not(#noResultsRow)"));
  const paginationControls = document.getElementById("paginationControls");
  let currentPage = 1;
  let totalPages = Math.ceil(rows.length / rowsPerPage);

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

    const createItem = (page, text = null, disabled = false, active = false) => {
      const li = document.createElement("li");
      li.className = (disabled ? "disabled" : "") + (active ? " active" : "");
      const a = document.createElement("a");
      a.href = "#";
      a.innerText = text || page;
      a.onclick = e => {
        e.preventDefault();
        if (!disabled && currentPage !== page) {
          currentPage = page;
          renderTable();
        }
      };
      li.appendChild(a);
      return li;
    };

    paginationControls.appendChild(createItem(currentPage - 1, "« Prev", currentPage === 1));
    for (let i = 1; i <= totalPages; i++) paginationControls.appendChild(createItem(i, null, false, currentPage === i));
    paginationControls.appendChild(createItem(currentPage + 1, "Next »", currentPage === totalPages));
  }

  renderTable();
});
</script>

<?php include_once('layouts/footer.php'); ?>
