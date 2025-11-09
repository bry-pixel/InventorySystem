<?php
$page_title = 'Stock Requests';
require_once('includes/load.php');
page_require_level(2);
include_once('layouts/header.php');

// Valid sortable columns
$valid_columns = ['product_name','user_name','request_date','status'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $valid_columns) ? $_GET['sort'] : 'request_date';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

// Helper functions
function sort_icon($column, $sort, $order) {
    if ($column == $sort) {
        return $order === 'ASC' ? " ↑" : " ↓";
    }
    return "";
}
function next_order($column, $sort, $order) {
    return ($column == $sort && $order === 'ASC') ? 'desc' : 'asc';
}

// Main query
$requests = find_by_sql("
    SELECT sr.*, p.name AS product_name, u.name AS user_name
    FROM stock_requests sr
    JOIN products p ON sr.product_id = p.id
    JOIN users u ON sr.requested_by = u.id
    ORDER BY {$sort} {$order}
");
?>

<div class="row">
  <div class="col-md-12">
    <?= display_msg($msg); ?>

    <div class="panel panel-default shadow-sm">
      <div class="panel-heading d-flex justify-content-between align-items-center" 
           style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h3 class="panel-title" style="margin:0;">
          <i class="glyphicon glyphicon-list-alt"></i> Stock Requests
        </h3>

        <!-- Search -->
        <div class="text-center" style="margin:0;">
          <input type="text" class="form-control input-sm live-search" 
                 placeholder="Search requests..." style="width:250px; font-size:14px;">
        </div>
      </div>

      <div class="panel-body">
        <?php if (!empty($requests)): ?>
          <table class="table table-hover table-bordered table-striped" id="requestsTable">
            <thead style="background:#f8f9fa;">
              <tr>
                <th>
                  <a href="?sort=product_name&order=<?= next_order('product_name',$sort,$order) ?>">
                    Product<?= sort_icon('product_name',$sort,$order) ?>
                  </a>
                </th>
                <th>
                  <a href="?sort=user_name&order=<?= next_order('user_name',$sort,$order) ?>">
                    Requested By<?= sort_icon('user_name',$sort,$order) ?>
                  </a>
                </th>
                <th>
                  <a href="?sort=request_date&order=<?= next_order('request_date',$sort,$order) ?>">
                    Date<?= sort_icon('request_date',$sort,$order) ?>
                  </a>
                </th>
                <th>
                  <a href="?sort=status&order=<?= next_order('status',$sort,$order) ?>">
                    Status<?= sort_icon('status',$sort,$order) ?>
                  </a>
                </th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($requests as $req): ?>
                <tr>
                  <td><?= remove_junk($req['product_name']); ?></td>
                  <td><?= remove_junk($req['user_name']); ?></td>
                  <td><?= date('M d, Y h:i A', strtotime($req['request_date'])); ?></td>
                  <td class="text-center">
                    <?php if ($req['status'] === 'pending'): ?>
                      <span class="label label-warning" style="font-size:small;">Pending</span>
                    <?php elseif ($req['status'] === 'approved'): ?>
                      <span class="label label-success" style="font-size:small;">Approved</span>
                    <?php elseif ($req['status'] === 'rejected'): ?>
                      <span class="label label-danger" style="font-size:small;">Rejected</span>
                    <?php else: ?>
                      <span class="label label-default" style="font-size:small;">Unknown</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if ($req['status'] === 'pending'): ?>
                      <a href="approve_request.php?product_id=<?= (int)$req['product_id']; ?>&request_id=<?= (int)$req['id']; ?>" 
                         class="btn btn-success btn-xs" title="Approve">
                        <i class="glyphicon glyphicon-ok"></i>
                        Approve
                      </a>
                      <a href="delete_request.php?id=<?= (int)$req['id']; ?>&status=rejected" 
                         class="btn btn-danger btn-xs" title="Reject"
                         onclick="return confirm('Reject this request?');">
                        <i class="glyphicon glyphicon-remove"></i> Reject
                      </a>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>

              <tr id="noResultsRow" style="display:none;">
                <td colspan="5" class="text-center text-muted">No matching requests found.</td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="text-center mt-3">
            <nav aria-label="Requests pagination">
              <ul class="pagination pagination-sm" id="paginationControls" style="margin:0;"></ul>
            </nav>
          </div>

        <?php else: ?>
          <div class="alert alert-info text-center">
            <i class="glyphicon glyphicon-info-sign"></i> No stock requests found.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
// Live Search
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.querySelector("input.live-search");
  const tableRows = document.querySelectorAll("#requestsTable tbody tr:not(#noResultsRow)");
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
  const rowsPerPage = 17;
  const table = document.getElementById("requestsTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr:not(#noResultsRow)"));
  const paginationControls = document.getElementById("paginationControls");
  let currentPage = 1;
  let totalPages = Math.ceil(rows.length / rowsPerPage);

  if (rows.length === 0) return;

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

    paginationControls.appendChild(createPageItem(currentPage - 1, "« Prev", currentPage === 1));
    for (let i = 1; i <= totalPages; i++) {
      paginationControls.appendChild(createPageItem(i, null, false, currentPage === i));
    }
    paginationControls.appendChild(createPageItem(currentPage + 1, "Next »", currentPage === totalPages));
  }

  renderTable();
});
</script>

<?php include_once('layouts/footer.php'); ?>
