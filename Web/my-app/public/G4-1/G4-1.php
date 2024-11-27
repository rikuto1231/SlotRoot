<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ポイント交換画面</title>
    <link rel="stylesheet" href="./G4-1.css">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="points">
            <img src="./img/coin.png" alt="ポイントアイコン">
            <span id="points">0000</span>
        </div>
        <button class="back-button" onclick="goBack()">戻る</button>
    </div>
    <div id="trophies-container">
    </div>
</div>
<script>
    function goBack() {
        window.location.href = "../G1-1/G1-1.php";
    }

    async function fetchUserPoints() {
        const response = await fetch('./get_user_points.php');
        const data = await response.json();
        document.getElementById('points').textContent = data.points;
    }

    async function fetchTrophies() {
        const response = await fetch('./get_trophies.php');
        const trophies = await response.json();

        const container = document.getElementById('trophies-container');
        container.innerHTML = '';

        trophies.forEach(trophy => {
            const trophyDiv = document.createElement('div');
            trophyDiv.className = 'achievement';
            trophyDiv.innerHTML = `
                <img src="./img/${trophy.image}" alt="${trophy.name}">
                <div class="achievement-name">${trophy.name}</div>
                <div class="points-text">${trophy.point}pt</div>
                <button class="exchange-btn" onclick="exchangePoints(${trophy.id})">交換</button>
            `;
            container.appendChild(trophyDiv);
        });
    }

    async function exchangePoints(trophyId) {
        const response = await fetch('./exchange_trophy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ trophyId: trophyId })
        });
        const result = await response.json();
        if (result.success) {
            alert('交換が完了しました!');
            fetchUserPoints();
        } else {
            alert(result.message || '交換に失敗しました。');
        }
    }

    window.onload = function () {
        fetchUserPoints();
        fetchTrophies();
    };
</script>
</body>
</html>
