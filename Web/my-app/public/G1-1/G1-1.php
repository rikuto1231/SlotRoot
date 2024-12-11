<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>麻生無双</title>
    <style>
        .gradient-text {
            background: linear-gradient(to right, 
                #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8f00ff, #ff0000
            );
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            background-size: 400% 100%;
            animation: rainbow-animation 8s linear infinite;
        }

        @keyframes rainbow-animation {
            0% { background-position: 0% center }
            100% { background-position: -400% center }
        }

        .slot-machine-border {
            position: relative;
        }

        .slot-machine-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            padding: 2px;
            border-radius: 22px;
            background: linear-gradient(
                45deg,
                #ffd700,
                #ff6b6b,
                #4cd964,
                #5ac8fa,
                #ffd700
            );
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            background-size: 400% 400%;
            animation: gradient-border 4s linear infinite;
        }

        @keyframes gradient-border {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }
    </style>
</head>
<body class="bg-[#1a1a1a] text-white min-h-screen w-auto flex flex-col justify-start bg-[url('loginback1.jpeg')] bg-center bg-cover bg-no-repeat bg-fixed">
    <header class="flex justify-between items-start w-full px-20 py-10 box-border bg-transparent relative h-[300px]">
        <div class="flex flex-col gap-4 z-10">
            <a href="../MiniGame/index.php" class="no-underline">
                <div class="inline-flex items-center justify-center border-2 border-[#ffd700] rounded-[15px] px-7 py-3.5 min-w-[200px] h-[80px] text-2xl bg-opacity-80 bg-[#2a2a2a] text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90">
                    <span class="flex-shrink-0">🅿</span>
                    <span id="pointsDisplay" class="ml-2.5 text-shadow-[0_0_10px_rgba(255,215,0,0.5)] whitespace-nowrap">0000pt</span>
                </div>
            </a>
            <div class="flex items-center justify-center border-2 border-[#ffd700] rounded-[15px] px-7 py-3.5 min-w-[200px] h-[80px] text-2xl bg-opacity-80 bg-[#2a2a2a] text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90" onclick="handleTrophyClick()">
                <span>🏆</span>
                <span id="trophyDisplay" class="ml-2.5 text-blue-500">00</span>
            </div>
        </div>

        <h1 class="font-shodou text-[250px] whitespace-nowrap tracking-[15px] gradient-text text-shadow-[0_0_20px_rgba(0,0,0,0.5)] absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">麻生無双</h1>

        <div class="flex flex-col gap-4 z-10">
            <a href="../G2-1/G2-1.php" class="no-underline" id="registerButton">
                <button class="bg-opacity-80 bg-[#2a2a2a] border-2 border-[#ffd700] rounded-[15px] px-7 py-4 text-lg text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90 w-full min-w-[200px]">新規登録</button>
            </a>
            <button id="loginButton" onclick="handleLoginLogout()" class="bg-opacity-80 bg-[#2a2a2a] border-2 border-[#ffd700] rounded-[15px] px-7 py-4 text-lg text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90 w-full min-w-[200px]">ログイン/ログアウト</button>
        </div>
    </header>

    <section class="flex flex-col items-center gap-10 mt-40 mb-20 px-5 max-w-[1200px] mx-auto">
        <a href="../G7-1/G7-1.php">
            <button class="font-shodou w-[600px] p-8 rounded-[20px] text-5xl bg-black bg-opacity-70 cursor-pointer transition-all duration-300 relative overflow-hidden backdrop-blur-sm text-red-500 border-3 border-red-500 shadow-[0_0_20px_rgba(255,0,0,0.3)] hover:scale-105 hover:bg-opacity-80 hover:shadow-[0_0_30px_rgba(255,0,0,0.5)] slot-machine-border">プレイ</button>
        </a>
        <a href="../G4-1/G4-1.php">
            <button class="font-shodou w-[600px] p-8 rounded-[20px] text-5xl bg-black bg-opacity-70 cursor-pointer transition-all duration-300 relative overflow-hidden backdrop-blur-sm text-[#32CD32] border-3 border-[#32CD32] shadow-[0_0_20px_rgba(50,205,50,0.3)] hover:scale-105 hover:bg-opacity-80 hover:shadow-[0_0_30px_rgba(50,205,50,0.5)] slot-machine-border">交換</button>
        </a>
        <a href="../G5-1/G5-1.php">
            <button class="font-shodou w-[600px] p-8 rounded-[20px] text-5xl bg-black bg-opacity-70 cursor-pointer transition-all duration-300 relative overflow-hidden backdrop-blur-sm text-[#ffd700] border-3 border-[#ffd700] shadow-[0_0_20px_rgba(255,215,0,0.3)] hover:scale-105 hover:bg-opacity-80 hover:shadow-[0_0_30px_rgba(255,215,0,0.5)] slot-machine-border">ランキング</button>
        </a>
    </section>

    <script>
        function handleTrophyClick() {
            window.location.href = "../G6-1/G6-1.php";
        }

        function handleLoginLogout() {
            const loginButton = document.getElementById('loginButton');
            if (loginButton.textContent === 'ログアウト') {
                fetch('logout.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'G1-1.php';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
                window.location.href = '../G3-1/G3-1.php';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            function updateUserStatus() {
                fetch('menu_data.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const pointsStr = String(data.data.points).padStart(4, '0');
                            document.getElementById('pointsDisplay').textContent = `${pointsStr}pt`;
                            const trophyStr = String(data.data.trophy_count).padStart(2, '0');
                            document.getElementById('trophyDisplay').textContent = trophyStr;
                            const loginButton = document.getElementById('loginButton');
                            const registerButton = document.getElementById('registerButton');
                            
                            if (data.data.is_guest) {
                                loginButton.textContent = 'ログイン';
                                registerButton.style.display = 'block';
                            } else {
                                loginButton.textContent = 'ログアウト';
                                registerButton.style.display = 'none';
                            }
                        } else {
                            console.error('ステータス更新エラー:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
            
            updateUserStatus();
        });

        // 画面サイズに応じた要素の配置調整
        function adjustLayout() {
            const header = document.querySelector('header');
            const title = document.querySelector('h1');
            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;

            // タイトルのサイズ調整
            if (viewportWidth >= 1920) {
                title.style.fontSize = '250px';
            } else if (viewportWidth >= 1440) {
                title.style.fontSize = '200px';
            } else if (viewportWidth >= 1280) {
                title.style.fontSize = '180px';
            } else {
                title.style.fontSize = '150px';
            }

            // ヘッダーの高さ調整
            const headerHeight = Math.max(300, viewportHeight * 0.3);
            header.style.height = `${headerHeight}px`;

            // メインコンテンツの位置調整
            const section = document.querySelector('section');
            const optimalMarginTop = viewportHeight * 0.2;
            section.style.marginTop = `${optimalMarginTop}px`;
        }

        // 画面サイズ変更時にレイアウトを調整
        window.addEventListener('load', adjustLayout);
        window.addEventListener('resize', adjustLayout);
    </script>
</body>
</html>