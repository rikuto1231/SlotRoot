<!-- K3-4.php -->
<?php
// GETでuser_idを受け取る
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$points = isset($_GET['points']) ? $_GET['points'] : 0;

// 不正アクセス対策: user_idが空の場合はエラーメッセージを表示して終了
if ($user_id === '') {
    die('不正なアクセスです');
}
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
        <!-- userテーブルのデータを更新するためのフォーム -->
        <form action="save.php" method="POST">
            <!-- user_idをhiddenフィールドで送信 -->
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>">

            <!-- 名前入力 -->
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
            
            <!-- ポイント入力 -->
            <label for="points">ポイント:</label>
            <input type="number" id="points" name="points" value="<?= htmlspecialchars($points, ENT_QUOTES, 'UTF-8') ?>" required>
            
            <div class="button">
                <!-- 変更を保存するボタン -->
                <button type="submit">変更</button>
                <!-- 元の画面に戻るボタン -->
                <button type="button" onclick="location.href='../K3-3/K3-3.php?user_id=<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>'">戻る</button>
            </div>
        </form>
    </div>
</body>
</html>
