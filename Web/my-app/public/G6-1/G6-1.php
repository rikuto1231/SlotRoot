<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>実績一覧</title>
    <link rel="stylesheet" href="G6-1.css">
</head>
<body>
    <a href="../G1-1/G1-1.php" class="back-button">戻る</a>
    
    <div class="container">
        <div class="items-wrapper" id="achievementsList">
            <!-- 実績リストがここに動的に生成されます -->
            <div class="loading">読み込み中...</div>
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
                            <div class="item">
                                <div class="item-name">
                                    <img src="../G4-1/img/${trophy.image}" alt="${trophy.name}">
                                    <span>${escapeHtml(trophy.trophy_name)}</span>
                                </div>
                                <div class="item-quantity">×${String(trophy.quantity).padStart(3, '0')}</div>
                            </div>
                        `).join('');
                    } else {
                        // 実績がない場合
                        achievementsList.innerHTML = `
                            <div class="item">
                                <div class="item-name">
                                    <span>実績がありません</span>
                                </div>
                                <div class="item-quantity">×000</div>
                            </div>
                        `;
                    }
                } else {
                    // エラーメッセージの表示
                    achievementsList.innerHTML = `
                        <div class="item">
                            <div class="item-name">
                                <span>${escapeHtml(data.message)}</span>
                            </div>
                            <div class="item-quantity">×000</div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                achievementsList.innerHTML = `
                    <div class="item">
                        <div class="item-name">
                            <span>データの読み込みに失敗しました</span>
                        </div>
                        <div class="item-quantity">×000</div>
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