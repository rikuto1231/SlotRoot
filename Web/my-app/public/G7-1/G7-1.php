<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../G3-1/G3-1.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <?php require_once '../../src/common/common_head.php'; ?>

    <title>プレイモード選択 - 麻生無双</title>
    <style>
        .gradient-border {
            position: relative;
        }

        .gradient-border::before {
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
            animation: gradient-animation 4s linear infinite;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }

        .hover-scale {
            transition: all 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        }
    </style>
</head>
<body class="bg-[#1a1a1a] text-white min-h-screen w-auto flex flex-col justify-start bg-[url('back_img.jpeg')] bg-center bg-cover bg-no-repeat bg-fixed">
    <header class="flex justify-between items-start w-full px-20 py-10 box-border bg-transparent">
        <a href="../G1-1/G1-1.php" class="no-underline">
            <div class="inline-flex items-center justify-center border-2 border-[#ffd700] rounded-[15px] px-7 py-3.5 min-w-[200px] h-[60px] text-xl bg-opacity-80 bg-[#2a2a2a] text-[#ffd700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] backdrop-blur-sm hover:translate-y-[-5px] hover:shadow-[0_0_25px_rgba(255,215,0,0.4)] hover:bg-opacity-90">
                ← メニューに戻る
            </div>
        </a>
    </header>

    <main class="flex flex-col items-center justify-center flex-grow px-5">
        <h1 class="font-shodou text-[100px] mb-20 text-[#ffd700] text-shadow-[0_0_20px_rgba(255,215,0,0.5)]">
            プレイモード選択
        </h1>

        <div class="flex flex-col gap-10 items-center">
            <a href="../Game/index.php" class="w-[800px] group">
                <div class="gradient-border hover-scale">
                    <button class="w-full p-8 rounded-[20px] text-5xl bg-black bg-opacity-70 backdrop-blur-sm text-[#ffd700] group-hover:bg-opacity-80 font-shodou relative overflow-hidden">
                        ソロプレイ
                        <span class="block text-2xl mt-2 text-gray-400">1人でプレイ</span>
                    </button>
                </div>
            </a>

            <a href="shared_play.php" class="w-[800px] group">
                <div class="gradient-border hover-scale">
                    <button class="w-full p-8 rounded-[20px] text-5xl bg-black bg-opacity-70 backdrop-blur-sm text-[#4cd964] group-hover:bg-opacity-80 font-shodou relative overflow-hidden">
                        ノリ打ち
                        <span class="block text-2xl mt-2 text-gray-400">2人でポイントを共有してプレイ</span>
                    </button>
                </div>
            </a>
        </div>
    </main>
</body>
</html>