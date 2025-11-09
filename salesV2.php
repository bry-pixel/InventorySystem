<?php
$page_title = 'Manage Sales';
require_once('includes/load.php');
page_require_level(4);

// Load all transactions
$transactions = find_transactions();
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default shadow-sm" style="border-radius:8px;">
      <div class="panel-heading clearfix">
        <strong><span class="glyphicon glyphicon-th"></span> All Transactions</strong>
        <div class="pull-right">
          <a href="sales_evaluation.php" class="btn btn-primary btn-sm">
            <i class="fa fa-line-chart"></i> Sales Evaluation
          </a>
        </div>
      </div>

      <div class="panel-body">
        <?php if (!empty($transactions)): ?>
          <div class="table-responsive">
            <table id="transactionsTable" class="table table-bordered table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th class="text-center sortable">Invoice #</th>
                  <th class="sortable">Date & Time</th>
                  <th class="text-center sortable">Items</th>
                  <th class="text-center sortable">Total Qty</th>
                  <th class="text-right sortable">Total Amount (₱)</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($transactions as $t): ?>
                  <tr>
                    <td class="text-center"><?= (int)$t['txn_id']; ?></td>
                    <td><?= date("M d, Y h:i A", strtotime($t['txn_time'])); ?></td>
                    <td class="text-center"><?= (int)$t['item_count']; ?></td>
                    <td class="text-center"><?= (float)$t['total_qty']; ?></td>
                    <td class="text-right">₱<?= number_format((float)$t['total_amount'], 2); ?></td>
                    <td class="text-center">
                      <button class="btn btn-info btn-xs" onclick="loadModal('transaction_details.php?id=<?= (int)$t['txn_id']; ?>')">
                        <span class="glyphicon glyphicon-list"></span> Details
                      </button>
                      <a class="btn btn-success btn-xs" target="_blank" href="recieptV2.php?id=<?= (int)$t['txn_id']; ?>">
                        <span class="glyphicon glyphicon-print"></span> Print
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="text-center mt-3">
            <nav aria-label="Transaction pagination">
              <ul class="pagination pagination-sm" id="paginationControls" style="margin:0;"></ul>
            </nav>
          </div>
        <?php else: ?>
          <div class="alert alert-info text-center">
            <span class="glyphicon glyphicon-info-sign"></span> No transactions found.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
#detailsModal.custom-modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.custom-modal .custom-dialog {
  background: #fff;
  border-radius: 8px;
  max-width: 950px;
  width: 100%;
  max-height: 85vh;
  overflow: hidden;
  box-shadow: 0 6px 24px rgba(0,0,0,0.2);
  display: flex;
  flex-direction: column;
}
.custom-modal .custom-header {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.custom-modal .custom-body {
  padding: 16px;
  overflow:auto;
  flex: 1 1 auto;
}
.custom-modal .custom-close {
  background: transparent;
  border: none;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  color:#333;
}
</style>

<div id="detailsModal" class="custom-modal" role="dialog" aria-hidden="true" aria-labelledby="detailsModalLabel">
  <div class="custom-dialog" role="document" aria-modal="true">
    <div class="custom-header">
      <h5 id="detailsModalLabel" style="margin:0;">Transaction</h5>
      <div>
        <button class="btn btn-default btn-xs" id="modalPrintBtn" style="margin-right:8px; display:none;">
          <span class="glyphicon glyphicon-print"></span> Print
        </button>
        <button class="custom-close" id="modalCloseBtn" aria-label="Close">&times;</button>
      </div>
    </div>
    <div class="custom-body" id="modal-body">Loading...</div>
  </div>
</div>

<script>

(function(){
  const modal = document.getElementById('detailsModal');
  const modalBody = document.getElementById('modal-body');
  const closeBtn = document.getElementById('modalCloseBtn');
  const printBtn = document.getElementById('modalPrintBtn');
  let lastFocusedElement = null;

  function openModal() {
    lastFocusedElement = document.activeElement;
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      closeBtn.focus();
    }, 50);
  }

  function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    modalBody.innerHTML = 'Loading...';
    if (lastFocusedElement && lastFocusedElement.focus) lastFocusedElement.focus();
    printBtn.style.display = 'none';
  }

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
  });

  printBtn.addEventListener('click', function(){
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`<html><head><title>Print</title></head><body>${modalBody.innerHTML}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 300);
  });

  window.loadModal = function(url) {
    openModal();
    modalBody.innerHTML = `<div style="text-align:center; padding:28px;"><svg width="40" height="40" viewBox="0 0 50 50"><path fill="#0d6efd" d="M43.935,25.145c0-10.318-8.372-18.69-18.69-18.69S6.554,14.827,6.554,25.145h4.068c0-8.066,6.556-14.622,14.568-14.622s14.568,6.556,14.568,14.622H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/></path></svg></div>`;
    fetch(url, { credentials: 'same-origin' })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
      })
      .then(html => {
        modalBody.innerHTML = html;
        const printLink = modalBody.querySelector('a.print-link, a[href*="reciept.php"]');
        if (printLink) {
          printBtn.style.display = 'inline-block';
          printBtn.onclick = () => {
            const href = printLink.href || printLink.getAttribute('href');
            window.open(href, '_blank');
          };
        } else {
          printBtn.style.display = 'none';
        }
      })
      .catch(err => {
        console.error(err);
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading content.</div>';
      });
  };

})();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const rowsPerPage = 16;
  const table = document.getElementById('transactionsTable');
  if (!table) return;
  const tbody = table.querySelector('tbody');
  let rows = Array.from(tbody.querySelectorAll('tr'));
  const totalPages = Math.ceil(rows.length / rowsPerPage);
  const paginationControls = document.getElementById("paginationControls");

  let currentSort = { index: null, direction: 1 };

  function renderPage(pageNum) {
    const start = (pageNum - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.forEach((row, i) => row.style.display = (i >= start && i < end) ? '' : 'none');

    paginationControls.innerHTML = '';
    const prev = `<li class="page-item ${pageNum === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#">« Prev</a>
                  </li>`;
    paginationControls.insertAdjacentHTML('beforeend', prev);

    for (let i = 1; i <= totalPages; i++) {
      paginationControls.insertAdjacentHTML('beforeend',
        `<li class="page-item ${i === pageNum ? 'active' : ''}">
           <a class="page-link" href="#">${i}</a>
         </li>`);
    }

    const next = `<li class="page-item ${pageNum === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#">Next »</a>
                  </li>`;
    paginationControls.insertAdjacentHTML('beforeend', next);

    [...paginationControls.querySelectorAll('a')].forEach((a, idx) => {
      a.addEventListener('click', e => {
        e.preventDefault();
        if (idx === 0 && pageNum > 1) renderPage(pageNum - 1);
        else if (idx === totalPages + 1 && pageNum < totalPages) renderPage(pageNum + 1);
        else if (idx > 0 && idx <= totalPages) renderPage(idx);
      });
    });
  }

  function sortTable(index) {
    const ths = table.querySelectorAll('th.sortable');
    ths.forEach(th => th.classList.remove('sort-asc','sort-desc'));

    if (currentSort.index === index) {
      currentSort.direction *= -1;
    } else {
      currentSort.index = index;
      currentSort.direction = 1;
    }
    ths[index].classList.add(currentSort.direction === 1 ? 'sort-asc' : 'sort-desc');

    rows.sort((a,b) => {
      const aText = a.cells[index].innerText.trim().replace(/[₱,]/g,'');
      const bText = b.cells[index].innerText.trim().replace(/[₱,]/g,'');
      const aVal = isNaN(aText) ? aText.toLowerCase() : parseFloat(aText);
      const bVal = isNaN(bText) ? bText.toLowerCase() : parseFloat(bText);
      return (aVal < bVal ? -1 : aVal > bVal ? 1 : 0) * currentSort.direction;
    });

    tbody.innerHTML = '';
    rows.forEach(r => tbody.appendChild(r));
    renderPage(1);
  }

  table.querySelectorAll('th.sortable').forEach((th, idx) => {
    th.style.cursor = "pointer";
    th.addEventListener('click', () => sortTable(idx));
  });

  renderPage(1);
});
</script>

<?php include_once('layouts/footer.php'); ?>
