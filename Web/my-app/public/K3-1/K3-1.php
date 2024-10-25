<?php

require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();

// 現状：ユーザポイント  今後：実績ポイント検索部分も実装
$sql = "SELECT name, point FROM User ORDER BY point DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ランキングテーブル</title>
    <link rel="stylesheet" href="K3-1.css">
</head>
<body>

<header>
    <div class="header-title">
        <h1>ランキング</h1>
    </div>
    <div class="search-input">
        <input type="text" placeholder="名前を検索...">
    </div>
    <div class="back-button">
        <button>戻る</button>
    </div>
</header>

<table>
    <tr>
        <th>順位</th>
        <th>名前</th>
        <th>ポイント</th>
    </tr>
    <?php
    foreach ($rankings as $index => $ranking) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td><a href="#">' . htmlspecialchars($ranking['name']) . '</a></td>';
        echo '<td>' . htmlspecialchars($ranking['points']) . 'P</td>';
        echo '</tr>';
    }
    ?>
</table>

</body>
</html>
