<?php
require '../../src/common/Db_connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($user_id) && !empty($password)) {
        $pdo = getDatabaseConnection();
        $sql = "SELECT user_id, password FROM user WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['user_id'];
            header('Location: dashboard.php'); // ダッシュボードへリダイレクト
            exit;
        } else {
            $error = 'ユーザ名またはパスワードが正しくありません。';
        }
    } else {
        $error = 'ユーザ名とパスワードを入力してください。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="G3-1.css">
</head>
<body>
<a href="../G1-1/Top.html">
  <button class="back-button">戻る</button>
</a>
<form method="POST" action="login.php">
    <div class="login_form_top">
      <h1>ログイン</h1>
      <p>ユーザ名、パスワードをご入力の上、「ログイン」ボタンをクリックしてください。</p>
    </div>
    <div class="login_form_btm">
      <input type="text" name="user_id" placeholder="ユーザ名" required>
      <input type="password" name="password" placeholder="パスワード" required>
      <input type="submit" value="ログイン">
    </div>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</form>
</body>
</html>
