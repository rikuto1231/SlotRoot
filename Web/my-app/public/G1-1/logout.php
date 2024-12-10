<?php
session_start();

// ログアウト処理
session_destroy();

// JSON レスポンスを返す
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'ログアウトしました'
]);