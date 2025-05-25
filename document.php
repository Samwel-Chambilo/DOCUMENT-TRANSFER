<?php
require 'config.php';

if (!isset($_GET['id'])) {
    echo "No document selected.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    echo "Document not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($doc['title']) ?></title>
</head>
<body>
  <h2><?= htmlspecialchars($doc['title']) ?></h2>
  <p><strong>Filename:</strong> <?= htmlspecialchars($doc['filename']) ?></p>
  <p><strong>Uploaded By:</strong> <?= htmlspecialchars($doc['uploaded_by']) ?></p>
  <p><strong>Uploaded At:</strong> <?= htmlspecialchars($doc['uploaded_at']) ?></p>
  <p><a href="<?= htmlspecialchars($doc['filepath']) ?>" download>Download File</a></p>
</body>
</html>
