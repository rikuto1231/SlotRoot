<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="G3-1.css">
</head>
<body>
<a href="../G1-1/G1-1.php">
<button class="back-button">戻る</button>
</a>
<form id="loginForm">
    <div class="login_form_top">
    <h1>ログイン</h1>
    <p>ユーザ名、パスワードをご入力の上、「ログイン」ボタンをクリックしてください。</p>
    <div id="errorMessage" style="color:red;"></div>
    </div>
    <div class="login_form_btm">
    <input type="text" name="user_name" placeholder="ユーザ名" required>
    <input type="password" name="password" placeholder="パスワード" required>
    <input type="submit" value="ログイン">
    </div>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    document.getElementById('errorMessage').textContent = '';
    
    const formData = new FormData(this);
    
    fetch('./login_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new TypeError('JSONレスポンスではありません');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = '../G1-1/G1-1.php';
        } else {
            document.getElementById('errorMessage').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = 'ログイン処理中にエラーが発生しました。';
    });
});
</script>
</body>
</html>