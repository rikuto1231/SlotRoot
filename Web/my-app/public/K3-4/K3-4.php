<!-- K3-4.php -->
<?php
// クエリパラメータからデータを受け取る
$name = isset($_GET['name']) ? $_GET['name'] : '';
$points = isset($_GET['points']) ? $_GET['points'] : 0;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K3-4 編集</title>
    <link rel="stylesheet" href="K3-4.css">
</head>
<body>
    <div class="container">
        <h2>情報を編集</h2>
        <form action="save.php" method="POST">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
            
            <label for="points">ポイント:</label>
            <input type="number" id="points" name="points" value="<?= htmlspecialchars($points, ENT_QUOTES, 'UTF-8') ?>" required>
            
            <div class="button">
                <button type="submit">変更</button>
                <button type="button" onclick="location.href='../K3-3/K3-3.php'">戻る</button>
            </div>
        </form>
    </div>
</body>
</html>
