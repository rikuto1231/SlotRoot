<?php
session_start();
require '../common/Db_connect.php';
$pdo = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $_SESSION['user_id'] = $userId;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'user_idが指定されていません']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '無効なリクエスト']);
}
?>
