<?php
require '../common/Db_connect.php';
$pdo = getDatabaseConnection();

$sql = "SELECT user_id, name, point FROM user ORDER BY point DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($rankings);
?>
