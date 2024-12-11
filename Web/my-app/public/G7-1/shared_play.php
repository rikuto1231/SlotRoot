<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../G3-1/G3-1.php');
    exit;
}

require_once '../Game/database.php';
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare("SELECT point FROM user WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userPoint = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>ノリ打ちマッチング - 麻生無双</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('loginback1.jpeg');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80% { content: '...'; }
            100% { content: ''; }
        }

        .gradient-border {
            position: relative;
            z-index: 1;
        }

        .gradient-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            padding: 2px;
            border-radius: 15px;
            background: linear-gradient(
                45deg,
                #ffd700,
                #4cd964,
                #5ac8fa,
                #ffd700
            );
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            background-size: 300% 300%;
            animation: gradient-animation 4s linear infinite;
            z-index: -1;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }

        input, button {
            position: relative;
            z-index: 2;
        }

        /* フォームの入力欄のスタイル改善 */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.5);
        }
    </style>
</head>
<body class="text-white w-full min-h-screen flex flex-col">
    <header class="relative z-10 flex justify-between items-start w-full px-20 py-10 box-border">
        <a href="G7-1.php" class="no-underline">
            <div class="inline-flex items-center justify-center border-2 border-[#ffd700] rounded-[15px] px-7 py-3.5 min-w-[200px] h-[60px] text-xl bg-opacity-80 bg-[#2a2a2a] text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90">
                ← モード選択に戻る
            </div>
        </a>
    </header>

    <main class="relative z-10 flex flex-col items-center justify-center flex-grow px-5">
        <div class="w-[800px] p-10 bg-black bg-opacity-70 backdrop-blur-sm rounded-[30px] gradient-border">
            <h1 class="font-shodou text-6xl mb-10 text-center text-[#ffd700] text-shadow-[0_0_20px_rgba(255,215,0,0.5)]">
                ノリ打ちマッチング
            </h1>

            <div class="mb-8 text-center">
                <p class="text-2xl">現在の所持ポイント</p>
                <p class="text-4xl text-[#ffd700] font-bold"><?php echo number_format($userPoint); ?> pt</p>
            </div>

            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="block text-2xl text-[#ffd700]">合言葉</label>
                    <input type="text" id="password" 
                           class="w-full p-4 rounded-lg bg-[#2a2a2a] border-2 border-[#ffd700] text-white text-xl focus:outline-none focus:ring-2 focus:ring-[#ffd700] transition-all"
                           placeholder="合言葉を入力してください">
                </div>

                <div class="space-y-3">
                    <label class="block text-2xl text-[#ffd700]">出資ポイント</label>
                    <input type="number" id="points" 
                           class="w-full p-4 rounded-lg bg-[#2a2a2a] border-2 border-[#ffd700] text-white text-xl focus:outline-none focus:ring-2 focus:ring-[#ffd700] transition-all"
                           min="100" max="<?php echo $userPoint; ?>" step="100" 
                           placeholder="出資ポイントを入力">
                    <p class="text-gray-400">※100pt単位で設定できます（最大<?php echo number_format($userPoint); ?>pt）</p>
                </div>

                <div class="flex gap-6 justify-center pt-4">
                    <button id="createRoom" 
                            class="px-10 py-4 rounded-lg text-2xl bg-[#4cd964] text-white font-bold cursor-pointer transition-all duration-300 hover:brightness-110 hover:scale-105">
                        部屋を作成
                    </button>
                    <button id="joinRoom" 
                            class="px-10 py-4 rounded-lg text-2xl bg-[#5ac8fa] text-white font-bold cursor-pointer transition-all duration-300 hover:brightness-110 hover:scale-105">
                        部屋に参加
                    </button>
                </div>
            </div>

            <div id="matchingStatus" class="hidden mt-8">
                <div class="flex items-center justify-center gap-4">
                    <div class="w-6 h-6 rounded-full bg-[#ffd700] animate-pulse"></div>
                    <p class="text-2xl text-[#ffd700] loading-dots">マッチング中</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('createRoom').addEventListener('click', () => handleRoomAction('create'));
        document.getElementById('joinRoom').addEventListener('click', () => handleRoomAction('join'));

        function handleRoomAction(action) {
            const password = document.getElementById('password').value;
            const points = parseInt(document.getElementById('points').value);
            
            if (!password) {
                alert('合言葉を入力してください');
                return;
            }
            
            if (!points || points < 100 || points > <?php echo $userPoint; ?>) {
                alert('有効なポイント範囲で指定してください（100pt ～ <?php echo number_format($userPoint); ?>pt）');
                return;
            }

            if (points % 100 !== 0) {
                alert('ポイントは100pt単位で指定してください');
                return;
            }
            

            document.getElementById('matchingStatus').classList.remove('hidden');
            document.getElementById('createRoom').disabled = true;
            document.getElementById('joinRoom').disabled = true;

            fetch(`../Game/shared_play_handler.php?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    password: password,
                    points: points,
                    userId: <?php echo $_SESSION['user_id']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'matched') {
                    window.location.href = `../Game/index.php?mode=shared&room=${password}&points=${data.total_points}`;
                } else if (data.status === 'waiting') {
                    startPolling(password);
                } else {
                    alert(data.message || 'エラーが発生しました');
                    resetUI();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました');
                resetUI();
            });
        }

        function startPolling(password) {
            const pollInterval = setInterval(() => {
                fetch(`../Game/shared_play_handler.php?action=check`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'matched') {
                        clearInterval(pollInterval);
                        window.location.href = `../Game/index.php?mode=shared&room=${password}&points=${data.total_points}`;
                    }
                })
                .catch(error => {
                    console.error('Polling error:', error);
                    clearInterval(pollInterval);
                    resetUI();
                });
            }, 1000);

            // 60秒後にタイムアウト
            setTimeout(() => {
                clearInterval(pollInterval);
                alert('マッチングがタイムアウトしました。再度お試しください。');
                resetUI();
            }, 60000);
        }

        function resetUI() {
            document.getElementById('matchingStatus').classList.add('hidden');
            document.getElementById('createRoom').disabled = false;
            document.getElementById('joinRoom').disabled = false;
        }

        // 入力値のバリデーション
        document.getElementById('points').addEventListener('input', function(e) {
            const value = parseInt(e.target.value);
            if (value > <?php echo $userPoint; ?>) {
                e.target.value = <?php echo $userPoint; ?>;
            }
            if (value < 0) {
                e.target.value = 0;
            }
        });

        // ボタンの有効/無効状態の管理
        function updateButtonStates() {
            const password = document.getElementById('password').value;
            const points = parseInt(document.getElementById('points').value);
            const isValid = password && points >= 100 && points <= <?php echo $userPoint; ?> && points % 100 === 0;
            
            document.getElementById('createRoom').className = isValid 
                ? "px-10 py-4 rounded-lg text-2xl bg-[#4cd964] text-white font-bold cursor-pointer transition-all duration-300 hover:brightness-110 hover:scale-105"
                : "px-10 py-4 rounded-lg text-2xl bg-gray-500 text-white font-bold cursor-not-allowed opacity-50";
            
            document.getElementById('joinRoom').className = isValid
                ? "px-10 py-4 rounded-lg text-2xl bg-[#5ac8fa] text-white font-bold cursor-pointer transition-all duration-300 hover:brightness-110 hover:scale-105"
                : "px-10 py-4 rounded-lg text-2xl bg-gray-500 text-white font-bold cursor-not-allowed opacity-50";
            
            document.getElementById('createRoom').disabled = !isValid;
            document.getElementById('joinRoom').disabled = !isValid;
        }

        document.getElementById('password').addEventListener('input', updateButtonStates);
        document.getElementById('points').addEventListener('input', updateButtonStates);
        
        // 初期状態でボタンを無効化
        updateButtonStates();
    </script>
</body>
</html>