<?php
// DB接続
require '../../src/common/Db_connect.php';

try {
    $pdo = getDatabaseConnection();

    $sql = "SELECT user_id, name, point FROM user ORDER BY point DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $id = $_POST['id'] ?? ''; // POSTデータからIDを取得（存在しない場合は空文字）
    $password = $_POST['password'] ?? ''; // POSTデータからパスワードを取得（存在しない場合は空文字）

    // ユーザーの存在を確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND password = :password");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    // 結果を取得
    if ($stmt->rowCount() > 0) {
        echo "ログイン成功！";
        echo "<script>console.log('ログイン成功！');</script>";
        // 必要に応じてリダイレクトやセッション設定を行う
    } else {
        echo "IDまたはパスワードが間違っています。";
        echo "<script>console.log('ログイン失敗: IDまたはパスワードが間違っています。');</script>";
    }
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    echo "<script>console.error('エラー: " . $e->getMessage() . "');</script>";
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインページ</title>
    <link rel="stylesheet" href="./K3-1.css">
</head>
<body>
    <div class="login-container">
        <form>
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" id="id" name="id" placeholder="IDを入力してください">
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力してください">
            </div>
            <div class="button-container">
                <button type="submit">ログイン</button>
            </div>
        </form>
    </div>
</body>
</html>
