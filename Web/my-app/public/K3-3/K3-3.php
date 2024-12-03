<?php
// DB接続
require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();

// POSTデータの取得
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // データベースから該当ユーザーを取得
    $sql = "SELECT name, point FROM user WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $name = $user['name'];
        $points = $user['point'];
    } else {
        // データが見つからない場合
        $name = "データが見つかりません";
        $points = 0;
    }
} else {
    // 不正なアクセスの場合
    $name = "不正なアクセスです";
    $points = 0;
    $user_id = null; // 不正アクセス時に送るデータがない場合
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K3-3 詳細表示</title>
    <link rel="stylesheet" href="K3-3.css">
</head>
<body>
    <div class="container">
        <h2>詳細情報</h2>
        <p><strong>名前:</strong> <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>ポイント:</strong> <?= htmlspecialchars($points, ENT_QUOTES, 'UTF-8') ?>pt</p>
        <div class="button">
            <!-- 編集画面にuser_idをGETパラメータで送信 -->
            <?php if ($user_id !== null): ?>
                <button onclick="location.href='../K3-4/K3-4.php?user_id=<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>'">編集</button>
            <?php endif; ?>
            <button onclick="location.href='../K3-2/K3-2.php'">戻る</button>
        </div>
    </div>
</body>
</html>
