<?php
// 必要に応じてセッションや認証チェックをここに追加できます。
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>K1-1 管理画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            justify-content: center;
            gap: 70px; /* ボタン間隔をさらに広げる */
        }
        .button {
            width: 400px; /* ボタンの幅をさらに拡大 */
            height: 150px; /* ボタンの高さをさらに拡大 */
            font-size: 32px; /* フォントサイズをさらに大きく */
            font-weight: bold; /* 太字で強調 */
            text-align: center;
            line-height: 150px; /* ボタン内テキストを中央揃え */
            text-decoration: none;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 20px; /* ボタンの丸みを強調 */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .button:hover {
            background-color: #0056b3;
            transform: scale(1.15); /* ホバー時の拡大率をさらにアップ */
        }
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            font-size: 18px;
            color: #007BFF;
            background-color: #fff;
            border: 1px solid #007BFF;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .back-button:hover {
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</head>
<body>
<a href="../K1-1/K1-1.html">
  <button class="back-button">戻る</button>
</a>
    <div class="container">
        <!-- 修正: K3-2/K3-2.phpへのリンク -->
        <a href="../K3-2/K3-2.php" class="button">ユーザー情報</a>
        <a href="../K3-1/K3-1.php" class="button">ランキング情報</a>
    </div>
</body>
</html>
