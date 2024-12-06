<?php 
// エラーログを詳細に表示
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
session_start();
header('Content-Type: application/json');

require_once 'database.php';

try {
    $pdo = getDatabaseConnection();
    error_log("Database connection attempt completed");

    // GETリクエスト（ポイント取得）
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
        error_log("GET request received for user_id: " . $_GET['user_id']);
        
        $stmt = $pdo->prepare("SELECT point FROM user WHERE user_id = ?");
        $stmt->execute([$_GET['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("DB Query result: " . json_encode($result));
        
        if ($result) {
            echo $result['point'];
        } else {
            error_log("No points found for user, returning default 500");
            echo "500";
        }
    }
    // POSTリクエスト（ポイント更新）を追加
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['point'])) {
        error_log("POST request received - user_id: " . $_POST['user_id'] . ", point: " . $_POST['point']);
        
        $stmt = $pdo->prepare("UPDATE user SET point = ? WHERE user_id = ?");
        $stmt->execute([$_POST['point'], $_POST['user_id']]);
        
        error_log("Points updated successfully");
        echo json_encode(['success' => true]);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
}