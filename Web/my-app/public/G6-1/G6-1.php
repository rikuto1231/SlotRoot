<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>実績一覧</title>
    <?php require_once '../../src/common/common_head.php'; ?>
    <style>
        /* Tailwindでは実現できない特殊なスタイルのみCSSで定義 */
        body {
            background-image: url('./loginback1.jpeg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="m-0 p-0 font-sans">
    <a href="../G1-1/G1-1.php" class="fixed top-[2%] right-[2%] z-10 bg-black border-2 border-[#ffd700] text-[#ffd700] px-5 py-2.5 rounded-full font-bold cursor-pointer transition duration-300 hover:bg-[#ffd700] hover:text-black">戻る</a>
    
    <div class="max-w-[90%] mx-auto mt-[10%] p-4 bg-black/85 rounded-xl shadow-[0_0_10px_rgba(255,223,0,0.8)]">
        <div class="h-[60vh] overflow-y-auto" id="achievementsList">
            <!-- 実績リストがここに動的に生成されます -->
            <div class="loading text-center text-[#ffd700]">読み込み中...</div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const achievementsList = document.getElementById('achievementsList');

        // 実績データの取得
        fetch('./achievements_process.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.trophies && data.trophies.length > 0) {
                        // 実績リストの生成
                        achievementsList.innerHTML = data.trophies.map(trophy => `
                            <div class="flex justify-between items-center p-[1.2%] mb-4 bg-black/90 rounded-lg border-2 border-[#ffd700] shadow-[0_0_5px_#ffaa00,0_0_10px_#ff8800]">
                                <div class="flex items-center gap-2.5">
                                    <img src="../G4-1/img/${trophy.image}" alt="${trophy.name}" class="w-20 h-20 object-contain">
                                    <span class="text-2xl text-[#ffd700] flex-grow">${escapeHtml(trophy.trophy_name)}</span>
                                </div>
                                <div class="text-3xl font-bold text-[#ffd700] text-center">×${String(trophy.quantity).padStart(3, '0')}</div>
                            </div>
                        `).join('');
                    } else {
                        // 実績がない場合
                        achievementsList.innerHTML = `
                            <div class="flex justify-between items-center p-[1.2%] mb-4 bg-black/90 rounded-lg border-2 border-[#ffd700] shadow-[0_0_5px_#ffaa00,0_0_10px_#ff8800]">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl text-[#ffd700]">実績がありません</span>
                                </div>
                                <div class="text-3xl font-bold text-[#ffd700] text-center">×000</div>
                            </div>
                        `;
                    }
                } else {
                    // エラーメッセージの表示
                    achievementsList.innerHTML = `
                        <div class="flex justify-between items-center p-[1.2%] mb-4 bg-black/90 rounded-lg border-2 border-[#ffd700] shadow-[0_0_5px_#ffaa00,0_0_10px_#ff8800]">
                            <div class="flex items-center gap-2.5">
                                <span class="text-2xl text-[#ffd700]">${escapeHtml(data.message)}</span>
                            </div>
                            <div class="text-3xl font-bold text-[#ffd700] text-center">×000</div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                achievementsList.innerHTML = `
                    <div class="flex justify-between items-center p-[1.2%] mb-4 bg-black/90 rounded-lg border-2 border-[#ffd700] shadow-[0_0_5px_#ffaa00,0_0_10px_#ff8800]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl text-[#ffd700]">データの読み込みに失敗しました</span>
                        </div>
                        <div class="text-3xl font-bold text-[#ffd700] text-center">×000</div>
                    </div>
                `;
            });

        // XSS対策のためのエスケープ関数
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