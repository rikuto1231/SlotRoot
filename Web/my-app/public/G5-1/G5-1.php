<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>ランキング画面</title>
    <style>
        @font-face {
            font-family: "Shodou";
            src: url("../tmp_m.ttf") format('truetype');
        }

        .gradient-title {
            background: linear-gradient(45deg, 
                #FFD700,
                #FFA500,
                #FFD700,
                #FF8C00
            );
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shine 3s ease-in-out infinite;
        }

        @keyframes shine {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="font-sans m-0 p-0 bg-[url('./back_img.jpeg')] bg-cover bg-center bg-no-repeat bg-fixed text-white min-h-screen">
    <div class="h-screen w-full m-0 p-0 flex flex-col bg-black/40 backdrop-blur-sm">
        <div class="flex-shrink-0 relative flex justify-center items-center py-5 bg-black/30 border-b-2 border-[#ffd700]/50">
            <h1 class="font-['Shodou'] text-[80px] font-bold text-center my-2.5 gradient-title drop-shadow-[2px_2px_4px_rgba(0,0,0,0.5)] md:text-[60px]">ランキング</h1>
            <button 
                onclick="window.location.href='../G1-1/G1-1.php'" 
                class="absolute right-5 top-1/2 transform -translate-y-1/2 text-xl px-6 py-2.5 border-2 border-[#FFD700] rounded-[25px] bg-black/50 text-[#FFD700] cursor-pointer transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.3)] hover:scale-105 hover:shadow-[0_0_20px_rgba(255,215,0,0.5)] hover:bg-black/70"
            >戻る</button>
        </div>

        <div id="rankingList" class="flex-1 overflow-y-auto mx-auto w-[90%] py-5 px-5 scrollbar-thin scrollbar-thumb-[#FFD700] scrollbar-track-black/30 scrollbar-thumb-rounded scrollbar-track-rounded">
            <!-- js動的生成位置 -->
        </div>

        <div id="userRankInfo" class="flex-shrink-0 relative w-full grid grid-cols-3 gap-4 justify-items-center items-center p-5 bg-black/70 border-t-2 border-[#ffd700]/50 shadow-[0_-4px_20px_rgba(0,0,0,0.3)] backdrop-blur-sm box-border">
            <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">あなたの順位: ロード中</span>
            <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">ユーザー名: ロード中</span>
            <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">0pt</span>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const rankingList = document.getElementById('rankingList');
        const userRankInfo = document.getElementById('userRankInfo');

        fetch('ranking_process.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
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
                rankingList.innerHTML = data.rankings.map((ranking, index) => `
                    <div class="grid grid-cols-3 gap-4 items-center py-5 px-7 mb-4 bg-black/50 border-2 border-[#FFD700] rounded-[15px] shadow-[0_4px_15px_rgba(255,215,0,0.2)] transition-all duration-300 backdrop-blur-sm hover:translate-x-2.5 hover:shadow-[0_4px_20px_rgba(255,215,0,0.4)] hover:bg-black/60">
                        <div class="text-3xl font-bold text-[#FFD700] text-shadow-[0_0_10px_rgba(255,215,0,0.6)] text-center">${index + 1}位</div>
                        <div class="text-2xl text-center text-white text-shadow-[0_0_5px_rgba(255,255,255,0.7)]">${escapeHtml(ranking.name)}</div>
                        <div class="text-[28px] font-bold text-[#FFD700] text-shadow-[0_0_10px_rgba(255,215,0,0.6)] text-center">${ranking.total_point}pt</div>
                    </div>
                `).join('');

                userRankInfo.innerHTML = `
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">あなたの順位: ${data.user_rank.rank}位</span>
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">${data.user_rank.rank === 'ゲストモード' ? 'ゲスト' : data.user_rank.name}</span>
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">${data.user_rank.total_point}pt</span>
                `;
            } else {
                rankingList.innerHTML = `
                    <div class="grid grid-cols-3 gap-4 items-center py-5 px-7 mb-4 bg-black/50 border-2 border-[#FFD700] rounded-[15px]">
                        <div class="col-span-3 text-center">ランキング情報を取得できませんでした。</div>
                    </div>`;
                userRankInfo.innerHTML = `
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">順位: 取得エラー</span>
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">エラーが発生しました</span>
                    <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">0pt</span>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            rankingList.innerHTML = `
                <div class="grid grid-cols-3 gap-4 items-center py-5 px-7 mb-4 bg-black/50 border-2 border-[#FFD700] rounded-[15px]">
                    <div class="col-span-3 text-center">ランキング情報の読み込みに失敗しました。</div>
                </div>`;
            userRankInfo.innerHTML = `
                <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">順位: 読み込みエラー</span>
                <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">通信エラーが発生しました</span>
                <span class="text-[#FFD700] text-xl text-shadow-[0_0_8px_rgba(255,215,0,0.5)]">0pt</span>
            `;
        });

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
    </script>
</body>
</html>