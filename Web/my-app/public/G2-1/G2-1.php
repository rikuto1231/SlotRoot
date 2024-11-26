<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="./G2-1.css">
</head>
<body>
    <a href="../G1-1/G1-1" class="back-button">戻る</a>
    <form id="registrationForm" action="register.php" method="POST">
        <div class="new_form_top">
            <h1>新規登録</h1>
            <p>名前、パスワードをご入力の上、「新規登録」ボタンをクリックしてください。</p>
            <div id="errorMessage" style="color: red;"></div>
        </div>
        <div class="new_form_btm">
            <input type="text" name="name" placeholder="名前" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <input type="submit" name="button" value="新規登録">
        </div>
    </form>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('./register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('登録が完了しました');
                    window.location.href = '../G1-1/G1-1.php';
                } else {
                    document.getElementById('errorMessage').textContent = data.message;
                }
            })
            .catch(error => {
                document.getElementById('errorMessage').textContent = 'エラーが発生しました';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>