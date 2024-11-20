<?php
// DB接続
require '../../src/common/Db_connect.php';


$pdo = getDatabaseConnection();


$sql = "SELECT user_id, name, pass, point, create_at FROM user ORDER BY point DESC";
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
    <link rel="stylesheet" href="K3-2.css">
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
        <th>パスワード</th>
        <th>所持PT</th>
        <th>作成日</th>
    </tr>
    <?php
    foreach ($rankings as $index => $ranking) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>';

        echo '<form method="POST" action="../K3-3/K3-3.php" style="display:inline;">';
        echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($ranking['user_id']) . '">';
        echo '<button type="submit" style="border:none; background:none; color:blue; text-decoration:underline; cursor:pointer;">';
        echo htmlspecialchars($ranking['name']);
        echo '</button>';
        echo '</form>';
        echo '</td>';
        echo '<td>' . htmlspecialchars($ranking['pass']) . '</td>';
        echo '<td>' . htmlspecialchars($ranking['point']) . 'P</td>';
        echo '<td>' . htmlspecialchars($ranking['create_at']) . '</td>';
        echo '</tr>';
    }
    ?>
</table>

</body>
</html>
