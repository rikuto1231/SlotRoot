<?php
// DB接続
require '../../src/common/Db_connect.php';
$pdo = getDatabaseConnection();

// 全体のランキングを取得
$sql = "SELECT user_id, name, point FROM user ORDER BY point DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);


$userId = "1"; // セッションのuser_idに差し替え予定


$selfRankSql = "
    SELECT COUNT(*) + 1 AS rank
    FROM user
    WHERE point > (SELECT point FROM user WHERE user_id = :user_id)";
$selfRankStmt = $pdo->prepare($selfRankSql);
$selfRankStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$selfRankStmt->execute();

$selfRankData = $selfRankStmt->fetch(PDO::FETCH_ASSOC);
$selfRank = $selfRankData['rank'] ?? '未登録';

// 自分のポイントを取得
$selfPointSql = "SELECT point FROM user WHERE user_id = :user_id";
$selfPointStmt = $pdo->prepare($selfPointSql);
$selfPointStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$selfPointStmt->execute();

$selfPointData = $selfPointStmt->fetch(PDO::FETCH_ASSOC);
$selfPoints = $selfPointData['point'] ?? 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ランキング画面</title>
    <link rel="stylesheet" href="./G5-1.css">
</head>
<body>

<div class="container">
    <div class="header">
        <h1>ランキング</h1>
        <button class="back-btn" onclick="window.location.href='../G1-1/G1-1.html'">戻る</button>
    </div>

    <!-- ランキング出力 -->
    <div class="ranking-list">
        <?php foreach ($rankings as $index => $ranking): ?>
        <div class="achievement">
            <span class="rank"><?= ($index + 1) ?>位</span>
            <span class="name"><?= htmlspecialchars($ranking['name'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="points"><?= $ranking['point'] ?>pt</span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- 自分の順位 -->
    <div class="footer">
        <span class="rank">あなたの順位: <?= $selfRank ?>位</span>
        <span class="name">ユーザーID: <?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?></span>
        <span class="points"><?= $selfPoints ?>pt</span>
    </div>
</div>

</body>
</html>
