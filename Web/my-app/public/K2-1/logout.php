<?php
// logout.php
session_start();
session_destroy(); // セッションを破棄

// JSON レスポンスを返す
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'ログアウトしました'
]);