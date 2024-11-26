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
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: transparent; /* 背景を完全透明に設定 */
        }
        .login-container {
            background: rgba(255, 255, 255, 0.8); /* 半透明の白背景 */
            padding: 60px; /* パディングをさらに大きくする */
            border-radius: 10px; /* 角を丸くする */
            width: 700px; /* コンテナの幅を拡大 */
        }
        .form-group {
            display: flex; /* 横並び配置 */
            align-items: center; /* ラベルとテキストボックスを中央揃え */
            margin-bottom: 25px; /* 各行の間隔を増やす */
        }
        .form-group label {
            width: 120px; /* ラベルの幅を大きく */
            font-size: 22px; /* ラベルのフォントサイズを拡大 */
            text-align: right; /* ラベルのテキストを右揃え */
            margin-right: 20px; /* ラベルと入力フィールドの間のスペースを増やす */
        }
        .form-group input {
            flex: 1; /* 入力フィールドが残りのスペースを占有 */
            padding: 15px; /* 入力フィールドの内側余白を増やす */
            border: 1px solid #ccc; /* 境界線の色 */
            border-radius: 5px; /* 入力フィールドの角を丸く */
            font-size: 18px; /* フォントサイズを大きく */
            background-color: transparent; /* 背景を完全透明に設定 */
        }
        .form-group input:focus {
            border-color: #007BFF; /* フォーカス時の境界線色を青に変更 */
            outline: none;
        }
        .login-container button {
            width: auto; /* ボタン幅を内容に合わせる */
            padding: 10px 30px; /* 縦横の余白をさらに大きく設定 */
            background-color: #007BFF; /* ボタンの背景を青に変更 */
            color: white; /* ボタンの文字色を白に設定 */
            border: 2px solid #007BFF; /* 青い境界線を追加 */
            border-radius: 50px; /* ボタンを丸みを持たせる */
            font-size: 20px; /* ボタンのフォントサイズを大きく調整 */
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3; /* ホバー時に背景色を少し暗くする */
        }
        .button-container {
            text-align: center; /* ボタンを中央揃え */
        }
    </style>
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
