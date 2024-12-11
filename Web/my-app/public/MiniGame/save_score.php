<?php
session_start();
require_once '../../src/common/Db_connect.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('ログインが必要です');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $score = $data['score'] ?? 0;
    $points = floor($score);  

    $pdo = getDatabaseConnection();
    
    // トランザクション開始
    $pdo->beginTransaction();

    // ユーザーのポイントを更新
    $sql = "UPDATE user 
            SET point = point + :points,
                update_at = CURRENT_TIMESTAMP 
            WHERE user_id = :user_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':points', $points, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'points' => $points,
            'message' => "獲得ポイント: {$points}pt"
        ]);
    } else {
        throw new Exception('ポイントの更新に失敗しました');
    }

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}