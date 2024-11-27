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
                <span id="pointsDisplay">0000pt</span>
            </div>
            <div class="trophy" onclick="handleTrophyClick()">
                <span>🏆</span>
                <span id="trophyDisplay" style="color: blue;">00</span>
            </div>
        </div>

        <h1>麻生無双</h1>

        <div class="login-buttons" id="loginButtons">
            <a href="../G2-1/G2-1.php">
                <button>新規登録</button>   
            </a>
            <a href="../G3-1/G3-1.php">
                <button id="loginButton">ログイン/ログアウト</button>
            </a>
        </div>
    </header>

    <section class="main-buttons">
        <a href="../Game/index.html">
            <button class="play-button">プレイ</button>
        </a>
        <a href="../G4-1/G4-1.php">
            <button class="exchange-button">交換</button>
        </a>
        <a href="../G5-1/G5-1.php">
            <button class="ranking-button">ランキング</button>
        </a>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ユーザーステータスの取得と表示を更新
        function updateUserStatus() {
            fetch('menu_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ポイント表示（4桁でゼロ埋め）
                        const pointsStr = String(data.data.points).padStart(4, '0');
                        document.getElementById('pointsDisplay').textContent = `${pointsStr}pt`;
                        
                        // トロフィー数表示（2桁でゼロ埋め）
                        const trophyStr = String(data.data.trophy_count).padStart(2, '0');
                        document.getElementById('trophyDisplay').textContent = trophyStr;

                        // ログインボタンのテキストを更新
                        const loginButton = document.getElementById('loginButton');
                        loginButton.textContent = data.data.is_guest ? 'ログイン' : 'ログアウト';
                    } else {
                        console.error('ステータス更新エラー:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // 初回表示時にステータスを更新
        updateUserStatus();
    });

    function handleTrophyClick() {
        window.location.href = "../G6-1/G6-1.php";
    }
    </script>
</body>
</html>