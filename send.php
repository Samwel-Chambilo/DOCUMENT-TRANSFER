<?php
require 'config.php';

// Handle document updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $newFilename = $_POST['filename'];
    
    $stmt = $pdo->prepare("UPDATE documents SET filename = ? WHERE id = ?");
    $stmt->execute([$newFilename, $id]);
    
    // Refresh the page to show changes
    header("Location: send.php");
    exit();
}

// Handle edit mode toggle
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;

// Fetch documents from database
$stmt = $pdo->query("SELECT id, filename, uploaded_at, is_read FROM documents");
$documents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Documents</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .status-read {
      color: green;
      font-weight: bold;
    }

    .status-unread {
      color: red;
      font-weight: bold;
    }

    .btn-edit, .btn-save, .btn-cancel {
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      margin: 0 2px;
    }

    .btn-edit {
      background-color: #3498db;
      color: white;
    }

    .btn-save {
      background-color: #2ecc71;
      color: white;
    }

    .btn-cancel {
      background-color: #e74c3c;
      color: white;
    }

    .edit-input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
  </style>
</head>
<body>
<div class="navigation">
    <a href="dashboard.php" class="return-home-btn">Return Home</a>
</div>
  <h2>Document List</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($documents as $index => $doc): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td>
            <?php if ($editId === $doc['id']): ?>
              <form method="POST" action="send.php" style="display: inline;">
                <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                <input type="text" name="filename" value="<?= htmlspecialchars($doc['filename']) ?>" class="edit-input">
            <?php else: ?>
              <a href="document.php?id=<?= $doc['id'] ?>"><?= htmlspecialchars($doc['filename']) ?></a>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
          <td class="<?= $doc['is_read'] ? 'status-read' : 'status-unread' ?>">
            <?= $doc['is_read'] ? 'Read' : 'Unread' ?>
          </td>
          <td>
            <?php if ($editId === $doc['id']): ?>
                <button type="submit" name="update" class="btn-save">Save</button>
                <a href="send.php" class="btn-cancel">Cancel</a>
              </form>
            <?php else: ?>
              <a href="send.php?edit=<?= $doc['id'] ?>" class="btn-edit">Edit</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>