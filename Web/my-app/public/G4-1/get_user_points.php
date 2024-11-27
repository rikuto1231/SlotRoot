<?php
session_start();
require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT point FROM user WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(['points' => $user['point']]);
} else {
    echo json_encode(['points' => 0]);
}
?>
