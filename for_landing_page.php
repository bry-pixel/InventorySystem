<?php
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect('index.php', false);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inventory System</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  background: radial-gradient(circle at top, #1e3a8a, #020617);
  color: white;
  overflow-x: hidden;
}

/* NAVBAR */
.navbar {
  backdrop-filter: blur(12px);
  background: rgba(255,255,255,0.05);
}

.navbar-brand {
  font-weight: 700;
  letter-spacing: 1px;
}

/* HERO */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.hero h1 {
  font-size: 55px;
  font-weight: 700;
}

.hero p {
  opacity: 0.85;
  font-size: 18px;
}

.btn-glow {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  padding: 12px 25px;
  border-radius: 30px;
  box-shadow: 0 0 20px rgba(34,197,94,0.5);
  transition: 0.3s;
}

.btn-glow:hover {
  transform: scale(1.05);
}

/* GLASS CARD */
.glass {
  background: rgba(255,255,255,0.08);
  border-radius: 15px;
  backdrop-filter: blur(10px);
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

/* FEATURES */
.features {
  padding: 80px 0;
}

.feature-card {
  padding: 25px;
  border-radius: 15px;
  background: rgba(255,255,255,0.05);
  transition: 0.3s;
}

.feature-card:hover {
  transform: translateY(-8px);
  background: rgba(255,255,255,0.1);
}

.feature-card i {
  font-size: 35px;
  margin-bottom: 15px;
  color: #22c55e;
}

/* FOOTER */
.footer {
  text-align: center;
  padding: 20px;
  opacity: 0.7;
}

/* ANIMATION */
.fade-in {
  animation: fadeIn 1.2s ease-in-out;
}

@keyframes fadeIn {
  from {opacity:0; transform: translateY(20px);}
  to {opacity:1; transform: translateY(0);}
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand text-white" href="#">
      <i class="fa fa-box"></i> Inventory System
    </a>

    <div class="ms-auto">
      <a href="login.php" class="btn btn-outline-light rounded-pill px-4">Login</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<div class="container hero">
  <div class="row align-items-center">

    <!-- TEXT -->
    <div class="col-md-6 fade-in">
      <h1>Inventory<br>Management System</h1>
      <p>Manage products, track sales, monitor stock, and keep full audit logs — all in one powerful platform.</p>

      <div class="mt-4">
        <a href="index.php" class="btn btn-glow">
          <i class="fa fa-rocket"></i> Get Started
        </a>
      </div>
    </div>

    <!-- DASHBOARD PREVIEW -->
    <div class="col-md-6 fade-in">
      <div class="glass">
        <h5><i class="fa fa-chart-line"></i> Dashboard Preview</h5>
        <hr>
        <p>✔ Real-time stock tracking</p>
        <p>✔ Sales analytics</p>
        <p>✔ Activity logs</p>
        <p>✔ Supplier management</p>
      </div>
    </div>

  </div>
</div>

<!-- FEATURES -->
<div class="features container text-center">
  <h2 class="mb-5">Powerful Features</h2>

  <div class="row g-4">

    <div class="col-md-4">
      <div class="feature-card">
        <i class="fa fa-box"></i>
        <h5>Inventory Control</h5>
        <p>Track products, categories, and stock levels with ease.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-card">
        <i class="fa fa-cash-register"></i>
        <h5>Sales System</h5>
        <p>Fast and accurate transaction processing with receipts.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-card">
        <i class="fa fa-history"></i>
        <h5>Audit Logs</h5>
        <p>Track every add, edit, delete action with timestamps.</p>
      </div>
    </div>

  </div>
</div>

<!-- CTA -->
<div class="container text-center my-5">
  <div class="glass">
    <h3>Start Managing Smarter Today</h3>
    <p>Take control of your inventory and boost efficiency.</p>
    <a href="index.php" class="btn btn-glow mt-3">
      <i class="fa fa-sign-in-alt"></i> Login Now
    </a>
  </div>
</div>

<!-- FOOTER -->
<div class="footer">
  © <?php echo date('Y'); ?> Inventory System • All Rights Reserved
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
