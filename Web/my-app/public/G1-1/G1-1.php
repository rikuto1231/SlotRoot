<?php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : 'ゲスト';
$points = isset($_SESSION['points']) ? $_SESSION['points'] : 0;
$trophy = isset($_SESSION['trophy']) ? $_SESSION['trophy'] : 0;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>麻生無双</title>
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./G1-1.css">

</head>
<body>

    <header>
        <div class="points-trophy">
            <div class="points">
                <span>🅿</span>
                <span>0000pt</span>
            </div>
            <div class="trophy" onclick="handleTrophyClick()">
                <span>🏆</span>
                <span style="color: blue;">00</span>
            </div>
            
            <script>
                function handleTrophyClick() {
                    window.location.href = "../G6-1/G6-1.php";
                }
            </script>
        </div>

        <h1>麻生無双</h1>

        <div class="login-buttons">
            <a href="../G2-1/G2-1.php">
                <button>新規登録</button>   
            </a>
            <a href="../G3-1/G3-1.php">
                <button>ログイン/ログアウト</button>
            </a>

        </div>
    </header>

    <section class="main-buttons">
        <button class="play-button">プレイ</button>
            <a href="../G4-1/G4-1.php">
                <button class="exchange-button">交換</button>
            <a href="../G5-1/G5-1.php">
                <button class="ranking-button">ランキング</button>
            </a>
    </section>

</body>
</html>
