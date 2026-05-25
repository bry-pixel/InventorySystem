<?php
ob_start();
require_once('includes/load.php');

if($session->isUserLoggedIn(true)) {
    redirect('log in V2.php', false);
}

$page_title = 'Login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo remove_junk($page_title); ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

:root{
  --primary:#2563eb;
  --primary-dark:#1d4ed8;
  --primary-light:#60a5fa;
  --secondary:#0f172a;
  --bg:#eff6ff;
  --card:#ffffff;
  --text:#1e293b;
  --muted:#64748b;
  --border:#dbeafe;
  --shadow:0 25px 60px rgba(37,99,235,0.18);
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:var(--bg);
  overflow:hidden;
  position:relative;
}

/* Background circles */
body::before,
body::after{
  content:'';
  position:absolute;
  border-radius:50%;
  z-index:0;
}

body::before{
  width:550px;
  height:550px;
  top:-220px;
  right:-120px;
  background:rgba(37,99,235,0.10);
}

body::after{
  width:450px;
  height:450px;
  bottom:-180px;
  left:-120px;
  background:rgba(96,165,250,0.14);
}

/* Main container */
.login-container{
  width:1120px;
  max-width:95%;
  min-height:690px;
  background:var(--card);
  border-radius:30px;
  overflow:hidden;
  display:grid;
  grid-template-columns:1fr 1fr;
  box-shadow:var(--shadow);
  position:relative;
  z-index:2;
}

/* Left side */
.left-panel{
  padding:70px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  background:#fff;
}

.brand{
  margin-bottom:12px;
}

.brand h1{
  font-size:42px;
  font-weight:700;
  color:var(--secondary);
}

.brand h1 span{
  color:var(--primary);
}

.subtitle{
  color:var(--muted);
  margin-bottom:45px;
  font-size:15px;
  line-height:1.7;
}

/* Alerts */
.alert{
  padding:14px 18px;
  border-radius:14px;
  margin-bottom:25px;
  font-size:14px;
}

.alert-danger{
  background:#fee2e2;
  color:#b91c1c;
}

.alert-success{
  background:#dcfce7;
  color:#15803d;
}

/* Form */
.form-group{
  margin-bottom:24px;
}

.form-group label{
  display:block;
  margin-bottom:10px;
  font-weight:500;
  color:var(--text);
}

.input-wrapper{
  position:relative;
}

.input-wrapper input{
  width:100%;
  height:58px;
  border:2px solid #e2e8f0;
  border-radius:16px;
  padding:0 18px;
  font-size:15px;
  outline:none;
  transition:0.3s ease;
  background:#fff;
}

.input-wrapper input:focus{
  border-color:var(--primary);
  box-shadow:0 0 0 5px rgba(37,99,235,0.12);
}

.forgot{
  display:inline-block;
  margin-top:-5px;
  margin-bottom:30px;
  text-decoration:none;
  color:var(--primary);
  font-size:14px;
  font-weight:500;
}

/* Login button */
.login-btn{
  width:100%;
  height:58px;
  border:none;
  border-radius:16px;
  background:linear-gradient(135deg,var(--primary),var(--primary-dark));
  color:#fff;
  font-size:17px;
  font-weight:600;
  cursor:pointer;
  transition:0.3s ease;
  box-shadow:0 14px 30px rgba(37,99,235,0.28);
}

.login-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 18px 35px rgba(37,99,235,0.35);
}

/* Footer info card */
.system-card{
  margin-top:35px;
  background:#f8fbff;
  border:1px solid var(--border);
  border-radius:20px;
  padding:22px;
}

.system-card h4{
  color:var(--primary-dark);
  margin-bottom:10px;
  font-size:16px;
}

.system-card p{
  color:var(--muted);
  font-size:13px;
  line-height:1.8;
}

/* Right side */
.right-panel{
  background:linear-gradient(135deg,#2563eb,#1e40af);
  position:relative;
  display:flex;
  justify-content:center;
  align-items:center;
  overflow:hidden;
}

.right-panel::before{
  content:'';
  position:absolute;
  width:520px;
  height:520px;
  background:rgba(255,255,255,0.08);
  border-radius:50%;
  top:-200px;
  right:-120px;
}

.right-panel::after{
  content:'';
  position:absolute;
  width:380px;
  height:380px;
  background:rgba(255,255,255,0.06);
  border-radius:50%;
  bottom:-130px;
  left:-90px;
}

/* Dashboard preview */
.preview-card{
  width:85%;
  max-width:520px;
  background:rgba(255,255,255,0.12);
  backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.15);
  border-radius:30px;
  padding:38px;
  color:#fff;
  z-index:2;
}

.preview-card h2{
  font-size:34px;
  margin-bottom:18px;
  line-height:1.3;
}

.preview-card p{
  font-size:15px;
  line-height:1.9;
  opacity:0.92;
  margin-bottom:32px;
}

/* Stats */
.stats{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:18px;
}

.stat-box{
  background:rgba(255,255,255,0.10);
  border-radius:18px;
  padding:20px;
}

.stat-box h3{
  font-size:26px;
  margin-bottom:6px;
}

.stat-box span{
  font-size:13px;
  opacity:0.85;
}

/* Mobile */
@media(max-width:950px){

  .login-container{
    grid-template-columns:1fr;
  }

  .right-panel{
    display:none;
  }

  .left-panel{
    padding:45px 30px;
  }

}

</style>
</head>

<body>

<div class="login-container">

  <!-- LEFT -->
  <div class="left-panel">

    <div class="brand">
      <h1><span>●</span> Inventory System</h1>
    </div>

    <p class="subtitle">
      Advanced inventory and stock management platform with
      real-time monitoring, analytics, and secure access.
    </p>

    <?php echo display_msg($msg); ?>

    <form method="post" action="auth.php">

      <div class="form-group">
        <label>Username</label>
        <div class="input-wrapper">
          <input type="text" name="username" placeholder="Enter your username" required>
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-wrapper">
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
      </div>

      <a href="#" class="forgot">Forgot password?</a>

      <button type="submit" class="login-btn">
        Login to Dashboard
      </button>

    </form>

    <div class="system-card">
      <h4>Inventory Management System</h4>
      <p>
        Track products, manage stock levels, monitor sales,
        generate reports, and optimize inventory operations
        from one centralized dashboard.
      </p>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="right-panel">

    <div class="preview-card">

      <h2>Smart Inventory<br>Monitoring System</h2>

      <p>
        Modern inventory management solution with barcode support,
        sales analytics, purchase tracking, and secure role-based access.
      </p>

      <div class="stats">

        <div class="stat-box">
          <h3>99%</h3>
          <span>Inventory Accuracy</span>
        </div>

        <div class="stat-box">
          <h3>24/7</h3>
          <span>Live Monitoring</span>
        </div>

        <div class="stat-box">
          <h3>Real-Time</h3>
          <span>Stock Tracking</span>
        </div>

        <div class="stat-box">
          <h3>Secure</h3>
          <span>User Authentication</span>
        </div>

      </div>

    </div>

  </div>

</div>

</body>
</html>
