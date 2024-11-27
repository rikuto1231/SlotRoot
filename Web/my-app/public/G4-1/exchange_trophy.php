<?php
session_start();
require '../../src/common/Db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$trophyId = $data['trophyId'];
$user_id = $_SESSION['user_id'];

$pdo = getDatabaseConnection();

// トロフィー情報を取得
$stmt = $pdo->prepare("SELECT point FROM trophy WHERE trophy_id = :trophy_id");
$stmt->execute(['trophy_id' => $trophyId]);
$trophy = $stmt->fetch();

if (!$trophy) {
    echo json_encode(['success' => false, 'message' => '無効なトロフィーIDです。']);
    exit;
}

$trophyPoint = $trophy['point'];

// ユーザーの現在ポイントを取得
$stmt = $pdo->prepare("SELECT point FROM user WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if ($user['point'] < $trophyPoint) {
    echo json_encode(['success' => false, 'message' => 'ポイントが不足しています。']);
    exit;
}

// トランザクション処理
try {
    $pdo->beginTransaction();

    // ポイントを減算
    $stmt = $pdo->prepare("UPDATE user SET point = point - :trophy_point WHERE user_id = :user_id");
    $stmt->execute(['trophy_point' => $trophyPoint, 'user_id' => $user_id]);

    // 中間テーブルに挿入
    $stmt = $pdo->prepare("INSERT INTO user_trophy (user_id, trophy_id) VALUES (:user_id, :trophy_id)");
    $stmt->execute(['user_id' => $user_id, 'trophy_id' => $trophyId]);

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => '交換処理中にエラーが発生しました。']);
}
?>
