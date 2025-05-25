<?php
require 'config.php';

// Mark file as read when opened
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE documents SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        // If coming from notification, mark as viewed
        if (isset($_GET['from_notification'])) {
            $stmt = $pdo->prepare("UPDATE documents SET is_viewed = 1 WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        // Force refresh the page to show updated status
        header("Location: view.php");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Fetch all documents
$docs = [];
try {
    $stmt = $pdo->query("SELECT * FROM documents");
    $docs = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Documents</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navigation">
    <a href="dashboard.php" class="return-home-btn">Return Home</a>
</div>
    <table class="documents-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Filename</th>
                <th>Send to/Department</th>
                <th>uploaded_at</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docs as $doc): ?>
            <tr class="<?= $doc['is_read'] ? 'read' : 'unread' ?>">
                <td><?= htmlspecialchars($doc['title']) ?></td>
                <td><?= htmlspecialchars($doc['filename']) ?></td>
                <td><?= htmlspecialchars($doc['department']) ?></td>
                <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                <td class="status-<?= $doc['is_read'] ? 'read' : 'unread' ?>">
                    <?= $doc['is_read'] ? 'Read' : 'Unread' ?>
                </td>
                <td>
                    <a href="view.php?id=<?= $doc['id'] ?>" class="open-link">Open</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
    // Add click handler to force page reload after file opens
    document.querySelectorAll('.open-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Open in new tab
            window.open(this.href, '_blank');
            // Reload the page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            e.preventDefault();
        });
    });
    </script>
</body>
</html>