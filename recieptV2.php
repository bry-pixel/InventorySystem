<?php
require_once('includes/load.php');
page_require_level(4);

$txn_id = (int)($_GET['id'] ?? 0);
if ($txn_id <= 0) {
    $session->msg('d', "No transaction provided.");
    redirect('sales.php');
}

$txn = find_by_id('transactions', $txn_id);
if (!$txn) {
    $session->msg('d', "Transaction not found.");
    redirect('sales.php');
}

$txn_time = $txn['txn_time'];
$items    = find_transaction_items($txn_id);

$total = 0;
foreach ($items as $i) {
    $total += (float)$i['price'] * (float)$i['qty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Receipt</title>
  <style>
    body {
      font-family: "Segoe UI", Roboto, Tahoma, sans-serif;
      background:rgb(109, 124, 138);
      margin: 0;
      padding: 20px;
      color: #111;
      background-image: url('bg image.jfif');
  background-size: cover;
    }
    .receipt {
      max-width: 360px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
    }
    .center { text-align: center; font-size: 14px; }
    .line { border-top: 1px dashed #888; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    td, th { padding: 6px; font-size: 13px; }
    th { text-align: left; border-bottom: 1px solid #444; }
    h2 { margin: 5px 0; font-size: 20px; color: #1f2937; }
    h3 { margin: 4px 0; font-size: 16px; color: #374151; }
    .total { font-weight: bold; font-size: 14px; text-align: right; margin: 8px 0; }
    .footer { margin-top: 15px; font-size: 12px; text-align: center; color: #555; }

    .btn {
      display: inline-block;
      font-size: 13px;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      margin: 5px;
      transition: transform 0.2s ease, background 0.3s ease;
    }
    .btn:hover { transform: translateY(-2px); }
    .btn-success {
      color: #fff;
      background: linear-gradient(135deg, #10b981, #059669);
      box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
      border: none;
    }
    .btn-success:hover { background: linear-gradient(135deg, #059669, #047857); }

    .btn-primary {
      color: #fff;
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
      border: none;
    }
    .btn-primary:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); }

    @media print {
      .no-print { display: none !important; }
       body { background: #fff; padding: 0; }
      .receipt { box-shadow: none; border: none; }
    }
  </style>
</head>
<body  onload="window.print()">
  <div class="receipt">
    <div class="center">
      <h2> Store Receipt</h2>
      <h3>Sales Invoice: #<?php echo $txn_id; ?></h3>
      <p>Date: <?php echo date("M d, Y  h:i A", strtotime($txn_time)); ?></p>
    </div>

    <div class="line"></div>
    <table>
      <thead>
        <tr>
          <th style="width:40%">Item</th>
          <th style="width:15%">Qty</th>
          <th style="width:20%">Price</th>
          <th style="width:25%; text-align:right;">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td><?php echo remove_junk($item['name']); ?></td>
          <td><?php echo $item['qty'] . ' ' . $item['unit']; ?></td>
          <td>₱<?php echo number_format($item['price'], 2); ?></td>
          <td style="text-align:right;">₱<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="line"></div>
    <p class="total">Grand Total: ₱<?php echo number_format($total, 2); ?></p>
    <div class="line"></div>

    <div class="footer">
      <p>We hope to see you again soon.</p>
    </div>
  </div>
  <!-- Buttons -->
  <div class="no-print" style="text-align:center; margin-top:15px;">
      <button onclick="window.print()" class="btn btn-success">🖨 Print Receipt</button>
      <a href="salesV2.php" class="btn btn-primary"> Back to Sales</a>
    </div>
</body>
</html>
<?php include_once('layouts/footer.php'); ?>
