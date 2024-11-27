<?php
require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();

$stmt = $pdo->prepare("SELECT trophy_id AS id, name, point, image FROM trophy");
$stmt->execute();
$trophies = $stmt->fetchAll();

echo json_encode($trophies);
?>
