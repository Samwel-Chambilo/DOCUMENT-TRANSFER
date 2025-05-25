<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM documents WHERE is_viewed = 0");
    $count = $stmt->fetch()['cnt'];
    echo json_encode(['count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
}