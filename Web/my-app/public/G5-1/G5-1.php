<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ランキング画面</title>
    <link rel="stylesheet" href="./G5-1.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>ランキング</h1>
        <button class="back-btn" onclick="window.location.href='../G1-1/G1-1.php'">戻る</button>
    </div>

    <div class="ranking-list" id="rankingList">
        <!-- js動的生成位置 -->
    </div>
    
    <div class="footer" id="userRankInfo">
        <span class="rank">あなたの順位: ロード中</span>
        <span class="name">ユーザー名: ロード中</span>
        <span class="points">0pt</span>
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
                <div class="achievement">
                    <span class="rank">${index + 1}位</span>
                    <span class="name">${escapeHtml(ranking.name)}</span>
                    <span class="points">${ranking.total_point}pt</span>
                </div>
            `).join('');

            // name取得用に取得元jsonプロパティ追加予定
            console.log(data);
            userRankInfo.innerHTML = `
                <span class="rank">あなたの順位: ${data.user_rank.rank}位</span>
                <span class="name">${data.user_rank.rank === 'ゲストモード' ? 'ゲスト' : data.user_rank.name}</span>
                <span class="points">${data.user_rank.total_point}pt（ユーザーポイント: ${data.user_rank.user_point}pt / トロフィーポイント: ${data.user_rank.trophy_point}pt）</span>
            `;
        } else {
            rankingList.innerHTML = `<div class="achievement">ランキング情報を取得できませんでした。</div>`;
            userRankInfo.innerHTML = `
                <span class="rank">順位: 取得エラー</span>
                <span class="name">エラーが発生しました</span>
                <span class="points">0pt</span>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        rankingList.innerHTML = `<div class="achievement">ランキング情報の読み込みに失敗しました。</div>`;
        userRankInfo.innerHTML = `
            <span class="rank">順位: 読み込みエラー</span>
            <span class="name">通信エラーが発生しました</span>
            <span class="points">0pt</span>
        `;
    });

    // XSS対策
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