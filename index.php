<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('index.php', false); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Inventory Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="libs/css/boostrap.css"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color:rgb(150, 197, 245);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-image: url('libs/images/bg image.jfif');
  background-size: cover;
}

.login-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(18px);
  border-radius: 16px;
  padding: 40px 35px;
  width: 100%;
  max-width: 400px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: fadeIn 0.6s ease-in-out;
}

.login-card h1 {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 5px;
  color: #2c3e50;
}
.login-card h4 {
  font-size: 15px;
  color: #7f8c8d;
  margin-bottom: 25px;
}

.input-group-addon {
  background: #f1f1f1;
  border: none;
}
.form-control {
  border-radius: 8px;
  border: 1px solid rgba(0,0,0,0.1);
  padding: 10px 15px;
  transition: all 0.3s ease;
}
.form-control:focus {
  border-color: #2575fc;
  box-shadow: 0 0 8px rgba(37,117,252,0.3);
}
.btn-primary {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  background: linear-gradient(135deg, #2575fc, #6a11cb);
  border: none;
  font-size: 16px;
  font-weight: bold;
  transition: all 0.3s ease;
}
.btn-primary:hover {
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(37,117,252,0.4);
}
.btn-primary:active {
  transform: scale(0.98);
  box-shadow: 0 4px 10px rgba(37,117,252,0.3);
}

.eye-toggle {
  cursor: pointer;
  background: #f1f1f1;
  border: none;
}

@media (max-width: 480px) {
  .login-card {
    padding: 30px 25px;
    width: 90%;
  }
  .login-card h1 {
    font-size: 24px;
  }
  .login-card h4 {
    font-size: 14px;
  }
}
</style>
</head>
<body>

<div class="login-card">
  <form method="post" action="auth.php" class="clearfix">
    <div class="text-center">
      <h1>Login Panel</h1>
      <h4>Inventory Management System</h4>
    </div>
    <?php echo display_msg($msg); ?>
    
    <!-- Username -->
    <div class="form-group">
      <label for="username" class="control-label">Username</label>
      <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
        <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
      </div>
    </div>

    <!-- Password -->
    <div class="form-group">
      <label for="password" class="control-label">Password</label>
      <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
        <span class="input-group-addon eye-toggle" onclick="togglePassword()">
          <i class="glyphicon glyphicon-eye-open" id="toggleIcon"></i>
        </span>
      </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Login</button>
    </div>
  </form>
</div>
<?php include_once('layouts/footer.php'); ?>
</body>
</html>
