<?php include_once('includes/load.php'); ?>
<?php $user = current_user(); ?>
<?php $page_title = isset($page_title) && $page_title ? $page_title : 'Inventory System'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<link rel="stylesheet" href="libs/css/boostrap.css"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<link rel="stylesheet" href="libs/css/main.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
  
  #header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding: 10px 20px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    z-index: 1100;
  }
  #header .header-left {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  #header .logo {
    font-weight: bold;
    font-size: 18px;
  }
  #header .header-date {
    font-size: 14px;
  }

  /* Sidebar */
  #sidebar {
    width: 240px;
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease;
  }
  #sidebar.collapsed {
    transform: translateX(-100%);
  }

  .page {
    margin-top: 10px;
    padding: 20px;
    padding-left: 260px;
    transition: padding-left 0.3s ease;
  }

  body.sidebar-collapsed .page {
    padding-left: 0 !important;
  }

  body.sidebar-collapsed #header {
    left: 0;
  }

  /* Mobile overlay */
  @media (max-width: 768px) {
    #sidebar {
      transform: translateX(-100%);
    }
    #sidebar.active {
      transform: translateX(0);
    }
    .page {
      padding-left: 0 !important;
      padding-top: 70px;
    }
  }




  .chatbot-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
}

.chatbot-toggle {
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  font-size: 26px;
  cursor: pointer;
  box-shadow: 0 3px 8px rgba(0,0,0,0.3);
  transition: background 0.3s;
}
.chatbot-toggle:hover { background: #0056b3; }

.chatbot-box {
  display: none;
  flex-direction: column;
  width: 320px;
  height: 400px;
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  overflow: hidden;
}
.chatbot-header {
  background: #007bff;
  color: #fff;
  padding: 12px;
  text-align: center;
  font-weight: bold;
}
.chatbot-messages {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  font-size: 14px;
}
.chatbot-message {
  margin: 6px 0;
  padding: 8px 12px;
  border-radius: 10px;
  max-width: 80%;
}
.user-message { background: #e9f5ff; align-self: flex-end; }
.bot-message { background: #f1f1f1; align-self: flex-start; }
.chatbot-input {
  display: flex;
  border-top: 1px solid #ddd;
}
.chatbot-input input {
  flex: 1;
  border: none;
  padding: 10px;
  font-size: 14px;
}
.chatbot-input button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
}
.chatbot-input button:hover { background: #0056b3; }
</style>

<title><?php echo htmlspecialchars($page_title ?? '', ENT_QUOTES, 'UTF-8'); ?></title>
</head>

<body class="<?php echo $session->isUserLoggedIn(true) ? '' : 'login'; ?>">
<?php if ($session->isUserLoggedIn(true)): ?>
  
<header id="header">
  <div class="header-left">
    <div style="background: linear-gradient(135deg, #4165d8 0%, #0e21cc 100%); color: white; padding: 5px 10px; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
    <button type="button" id="sidebarToggle" class="btn btn-default btn-sm sidebar-toggle" aria-label="Toggle sidebar" title="Toggle sidebar">
      <span class="glyphicon glyphicon-menu-hamburger"></span>
    </button>
    <div class="logo" >Inventory System</div>
    </div>
    <div class="header-date">
      <span class="glyphicon glyphicon-calendar"></span>
      <span class="date"><?php echo date('l, jS F Y'); ?></span>
    </div>
  </div>

  <div class="header-right">
    <ul class="info-menu list-inline list-unstyled">
      <li class="profile">
        <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
          <img src="uploads/users/<?php echo isset($user['image']) && $user['image'] ? $user['image'] : 'no_image.png'; ?>" 
               alt="Profile image" class="img-circle img-inline" loading="lazy">
          <span><?php echo remove_junk(ucfirst($user['name'] ?? 'User')); ?> <i class="caret"></i></span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="profile.php?id=<?php echo (int)$user['id'];?>"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
          <li><a href="edit_account.php"><i class="glyphicon glyphicon-cog"></i> Settings</a></li>
          <li class="last"><a href="logout.php"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</header>

<div class="sidebar" id="sidebar">
  <?php if($user['user_level'] === '1'): ?>
    <?php include_once(__DIR__ . '/admin_menu.php');?>
  <?php elseif($user['user_level'] === '2'): ?>
    <?php include_once(__DIR__ . '/special_menu.php');?>
  <?php elseif($user['user_level'] === '3'): ?>
    <?php include_once(__DIR__ . '/user_menu.php');?>
    <?php elseif($user['user_level'] === '4'): ?>
    <?php include_once(__DIR__ . '/guest_menu.php');?>
  <?php endif;?>
</div>
<?php endif; ?>
<div class="chatbot-container">
  <button class="chatbot-toggle"><i class="glyphicon glyphicon-comment"></i></button>
  <div class="chatbot-box">
    <div class="chatbot-header">💬 Inventory Assistant</div>
    <div class="chatbot-messages" id="chatbotMessages"></div>
    <div class="chatbot-input">
      <input type="text" id="chatInput" placeholder="Type a message..." />
      <button id="sendBtn"><i class="glyphicon glyphicon-send"></i></button>
    </div>
  </div>
</div>

<script>
  (function(){
    function applySidebarState(collapsed){
      var body = document.body;
      var sb = document.getElementById('sidebar');
      if (!sb) return;
      if (collapsed) {
        body.classList.add('sidebar-collapsed');
        sb.classList.add('collapsed');
      } else {
        body.classList.remove('sidebar-collapsed');
        sb.classList.remove('collapsed');
      }
    }

    document.addEventListener('DOMContentLoaded', function(){
      var initCollapsed = localStorage.getItem('sidebarCollapsed') === '1';
      applySidebarState(initCollapsed);

      var toggle = document.getElementById('sidebarToggle');
      if (toggle) {
        toggle.addEventListener('click', function(){
          var willCollapse = !document.body.classList.contains('sidebar-collapsed');
          applySidebarState(willCollapse);
          localStorage.setItem('sidebarCollapsed', willCollapse ? '1' : '0');
        });
      }
    });
  })();


  //chatbot toggle
  document.addEventListener("DOMContentLoaded", function() {
  const toggleBtn = document.querySelector(".chatbot-toggle");
  const chatBox = document.querySelector(".chatbot-box");
  const messages = document.getElementById("chatbotMessages");
  const input = document.getElementById("chatInput");
  const sendBtn = document.getElementById("sendBtn");

  toggleBtn.addEventListener("click", () => {
    chatBox.style.display = chatBox.style.display === "flex" ? "none" : "flex";
  });

  function appendMessage(sender, text) {
    const msg = document.createElement("div");
    msg.className = `chatbot-message ${sender}-message`;
    msg.innerText = text;
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
  }

  async function sendMessage() {
    const userText = input.value.trim();
    if (!userText) return;
    appendMessage("user", userText);
    input.value = "";

    const res = await fetch("chatbot.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: userText })
    });
    const data = await res.json();
    appendMessage("bot", data.reply);
  }

  sendBtn.addEventListener("click", sendMessage);
  input.addEventListener("keypress", e => {
    if (e.key === "Enter") sendMessage();
  });
});
</script>

<div class="page">
  <div class="container-fluid">
