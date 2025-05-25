document.getElementById('notif-icon').addEventListener('click', ()=>{
  fetch('mark_read.php').then(()=>{
    document.getElementById('notif-count').textContent = '0';
  });
});

// Optional: poll for new notifications
setInterval(()=>{
  fetch('fetch_notifications.php')
    .then(res=>res.json())
    .then(data=>{
      document.getElementById('notif-count').textContent = data.count;
    });
}, 5000);
