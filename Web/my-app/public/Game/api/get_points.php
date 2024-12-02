<?php
require '../../../src/common/Db_connect.php';
header('Content-Type: application/json');

$pdo = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT point FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'points' => $stmt->fetchColumn() ?: 500]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $points = $_POST['points'] ?? null;
    
    if (!$userId || !$points) {
        echo json_encode(['success' => false]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE user SET point = ? WHERE user_id = ?");
    $success = $stmt->execute([$points, $userId]);
    echo json_encode(['success' => $success]);
}
?>