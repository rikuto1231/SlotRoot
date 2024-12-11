<?php
session_start();
require_once '../../src/common/common_head.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: ../G3-1/G3-1.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>麻生無双 - ミニゲーム</title>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.60.0/dist/phaser.min.js"></script>
    <script src="game.js" defer></script>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #1a1a1a;
            background-image: url('../G1-1/loginback1.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        #game-container {
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        .button {
            margin-top: 20px;
            padding: 15px 30px;
            background: transparent;
            border: 2px solid #ffd700;
            color: #ffd700;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            min-width: 200px;
            backdrop-filter: blur(5px);
            background-color: rgba(42, 42, 42, 0.8);
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.4);
            background-color: rgba(42, 42, 42, 0.9);
        }

        .title {
        color: #ffd700;
        font-size: 48px;  /* 36pxから48pxに拡大 */
        font-weight: 900; /* より太く */
        margin-bottom: 30px;
        text-align: center;
        text-shadow: 
            0 0 10px rgba(255, 215, 0, 0.5),
            0 0 20px rgba(255, 0, 0, 0.3);  /* 赤色のグロー効果を追加 */
        letter-spacing: 2px;  /* 文字間隔を少し広げる */
        font-family: 'Arial Black', 'Arial Bold', Arial, sans-serif; /* よりインパクトのあるフォント */
        background: linear-gradient(
            to bottom,
            #ffd700 0%,
            #ffed4a 50%,
            #ffd700 100%
        );
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: titleGlow 2s ease-in-out infinite alternate;
    }

    @keyframes titleGlow {
        from {
            filter: drop-shadow(0 0 2px rgba(255, 215, 0, 0.5))
                   drop-shadow(0 0 5px rgba(255, 0, 0, 0.3));
        }
        to {
            filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.8))
                   drop-shadow(0 0 10px rgba(255, 0, 0, 0.5));
        }
    }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            box-sizing: border-box;
        }

        .instructions {
            color: #fff;
            margin: 20px 0;
            text-align: center;
            font-size: 18px;
            line-height: 1.6;
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">前園を避けろ！！！
        </h1>
        
        <div id="game-container"></div>

        <div class="instructions">
            <p>WASDで移動！障害物を避けてポイントを稼ごう！</p>
            <p>獲得したスコア分がポイントに変換されます</p>
        </div>

        <a href="../G1-1/G1-1.php" class="button">メニューに戻る</a>
    </div>
</body>
</html>