<?php

header("Content-Type: application/json; charset=UTF-8"); // JSONレスポンス用ヘッダー
header("Access-Control-Allow-Methods: POST"); // POSTリクエストのみ許可

// データベース接続ファイルをインクルード
require_once '../common/DB_connect.php';

try {

    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['user_id']) && isset($input['password'])) {
        $user_id = htmlspecialchars($input['user_id'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($input['password'], ENT_QUOTES, 'UTF-8');

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $pdo = getDatabaseConnection();

        $defaultPoint = 500;

        $stmt = $pdo->prepare("INSERT INTO user (name, pass, point) VALUES (:name, :pass, :point)");
        $stmt->bindParam(':name', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':point', $defaultPoint, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['message' => '登録が完了しました！']);
    } else {
        echo json_encode(['error' => '名前とパスワードは必須です。']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'エラーが発生しました: ' . $e->getMessage()]);
}
?>
