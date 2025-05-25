<?php
require 'config.php';

// Notification System
$stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE department = 'IT' AND is_read = 0");
$stmt->execute();
$unreadCount = $stmt->fetchColumn();

// Upload Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'];
    $department = $_POST['department'] ?? 'IT'; // Default to IT if not selected
    $filename = $_FILES['document']['name'];
    $tmpName = $_FILES['document']['tmp_name'];
    $uploadDir = 'uploads/';

    // Validate file type (example: only allow PDFs)
    // Generate unique filename to prevent overwrites
    $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
    $newFilename = uniqid() . '.' . $fileExt;
    $destination = $uploadDir . $newFilename;
    
    // Basic security checks
    $maxSize = 10 * 1024 * 1024; // 10MB max file size
    $fileSize = $_FILES['document']['size'];
    
    if ($fileSize > $maxSize) {
        $uploadError = "File size exceeds 10MB limit.";
    } elseif (move_uploaded_file($tmpName, $destination)) {
        $stmt = $pdo->prepare("INSERT INTO documents (title, filename, filepath, department, uploaded_at) 
                              VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $title,
            $filename, // Original filename
            $destination, // Server path with unique filename
            $department
        ]);
        header("Location: IT.php?upload=success");
        exit();
    } else {
        $uploadError = "File upload failed. Please try again.";
    }
}


// 3. Edit Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $newTitle = $_POST['title'];
    
    $stmt = $pdo->prepare("UPDATE documents SET title = ? WHERE id = ?");
    $stmt->execute([$newTitle, $id]);
}

// Fetch IT department documents
$stmt = $pdo->prepare("SELECT * FROM documents WHERE department = 'IT'");
$documents = $stmt->execute() ? $stmt->fetchAll() : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Department</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Notification Bell */
        .notification-bell {
            position: relative;
            display: inline-block;
            margin-right: 20px;
        }
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 12px;
        }
        
        /* Upload Form */
        .upload-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        /* Document Table */
        .document-table {
            width: 100%;
            border-collapse: collapse;
        }
        .document-table th, .document-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .edit-input {
            width: 100%;
            padding: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>IT Department Portal</h1>
        <div class="notification-bell">
            <i class="fas fa-bell fa-2x"></i>
            <?php if ($unreadCount > 0): ?>
                <span class="badge" id="notificationCount"><?= $unreadCount ?></span>
            <?php endif; ?>
        </div>
    </header>

    <!-- 2. Upload Section -->
    
    <section class="upload-form">
    <h2>Upload Document</h2>
    <?php if (isset($uploadError)): ?>
        <div class="error"><?= $uploadError ?></div>
    <?php elseif (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
        <div class="success">File uploaded successfully!</div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Document Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="document">Select File:</label>
            <input type="file" id="document" name="document" required>
            <small>Maximum file size: 10MB</small>
        </div>
        
        <div class="form-group">
            <label for="department">Send to Department:</label>
            <select id="department" name="department">
                <option value="IT" selected>IT Department</option>
                <option value="COICT">COICT</option>
                <option value="TELECOMMUNICATION">Telecommunication</option>
                <option value="DATA_SCIENCE">Data Science</option>
                <option value="ICT">ICT</option>
            </select>
        </div>
        
        <button type="submit" name="upload" class="upload-btn">Upload Document</button>
    </form>
</section>

    <!-- 3. View Section -->
    <section>
        <h2>IT Department Documents</h2>
        <table class="document-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>File</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td>
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $doc['id']): ?>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($doc['title']) ?>" class="edit-input">
                        <?php else: ?>
                            <?= htmlspecialchars($doc['title']) ?>
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= $doc['filepath'] ?>" target="_blank"><?= $doc['filename'] ?></a></td>
                    <td><?= $doc['uploaded_at'] ?></td>
                    <td><?= $doc['is_read'] ? 'Read' : 'Unread' ?></td>
                    <td>
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $doc['id']): ?>
                            <button type="submit" name="edit">Save</button>
                            <a href="IT.php">Cancel</a>
                            </form>
                        <?php else: ?>
                            <a href="IT.php?edit=<?= $doc['id'] ?>">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <script>
        // Notification click handler
        document.querySelector('.notification-bell').addEventListener('click', function() {
            fetch('mark_read.php?department=IT')
                .then(() => {
                    document.getElementById('notificationCount').style.display = 'none';
                });
        });
    </script>
</body>
</html>