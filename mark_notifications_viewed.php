<?php
header('Content-Type: application/json'); // Fix 4: Add proper headers

require 'config.php';

try {
    // Fix 5: Use prepared statements for security
    $stmt = $pdo->prepare("UPDATE documents SET is_viewed = 1 WHERE is_viewed = 0");
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Fix 6: Proper error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}