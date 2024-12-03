<?php
require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();

// POSTデータの受け取り
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $points = isset($_POST['points']) ? $_POST['points'] : '';

    // データが不足している場合はエラー
    if ($user_id === '' || $name === '' || $points === '') {
        die('必要なデータが不足しています');
    }

    // userテーブルを更新するSQL
    $sql = "UPDATE user SET name = :name, point = :points WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':points', $points, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    // 実行
    if ($stmt->execute()) {
        // 成功時に戻る画面へリダイレクト
        header("Location: ../K3-3/K3-3.php?user_id=$user_id");
        exit;
    } else {
        die('更新に失敗しました');
    }
} else {
    die('不正なアクセスです');
}
