<?php
require 'config.php';
// fetch count of unread docs
$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM documents WHERE is_read = 0");
$notif = $stmt->fetch()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
  <nav>
    
  <div class="notification-wrapper">
      <a href="view.php" class="notification-link" aria-label="Notifications" id="notification-link">
        <i class="fas fa-bell"></i>
        <span id="notif-count" class="notif-badge" style="display: <?php echo $notif > 0 ? 'inline-block' : 'none'; ?>;">
          <?php echo $notif; ?>
        </span>
      </a>
    </div>

  <ul>
    <li><a href="view.php">View Documents</a></li>
    <li></li>
    <li></li>
    <li> <a href="upload.php"> upload file </a></li>
    <li></li>
    <li></li>
    <li><a href="send.php">edit Document</a></li>
    <li></li>
    <li></li>
    
    <li>Notifications <span id="notif-count"><?php echo $notif; ?></span></li>
  </ul>
</nav>

<script>
  function fetchNotifications() {
    fetch('fetch_notifications.php')
      .then(res => res.json())
      .then(data => {
        const count = data.count;
        const badge = document.getElementById('notif-count');
        if (count > 0) {
          badge.style.display = 'inline-block';
          badge.textContent = count;
        } else {
          badge.style.display = 'none';
        }
      });
  }
  // Mark notifications as viewed (not read) when clicked
document.getElementById('notification-link').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent immediate navigation
    
    fetch('mark_notifications_viewed.php')
      .then(response => response.json())
      .then(data => {
          if(data.success) {
              document.getElementById('notif-count').style.display = 'none';
              window.location.href = 'view.php'; // Navigate after clearing
          }
      });
});

  // Initial call and refresh every 10 seconds
  fetchNotifications();
  setInterval(fetchNotifications, 10000);
  </script>
</body>

</html>
