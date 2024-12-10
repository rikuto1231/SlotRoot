<!DOCTYPE html>
<html lang="ja">
<head>
<?php require_once '../../src/common/common_head.php'; ?>
<title>ポイント交換画面</title>
</head>
<body class="bg-[#1a1a1a] text-white min-h-screen w-auto flex flex-col justify-start bg-[url('./img/loginback1.jpeg')] bg-center bg-cover bg-no-repeat bg-fixed">

<div class="w-[95%] max-w-[1400px] mx-auto my-5 p-5 bg-black/85 rounded-[20px] shadow-[0_0_30px_rgba(255,215,0,0.3)] border border-[#ffd700]/30 backdrop-blur-[10px] h-[90vh] flex flex-col">
    
    <div class="sticky top-0 flex justify-between items-center p-5 bg-black/95 border-b-2 border-[#ffd700]/30 rounded-[15px] mb-5 z-50">
        <div class="flex items-center px-5 py-2.5 bg-gradient-to-br from-black/70 to-[#2a2a2a]/70 border-2 border-[#ffd700] rounded-[15px] shadow-[0_0_20px_rgba(255,215,0,0.2)]">
            <img src="./img/coin.png" alt="ポイントアイコン" class="w-10 h-10 mr-4 filter drop-shadow-[0_0_5px_rgba(255,215,0,0.5)]">
            <span id="points" class="text-[#ffd700] text-4xl font-bold text-shadow-[0_0_10px_rgba(255,215,0,0.5)] tracking-[2px]">0000</span>
        </div>

        <button onclick="goBack()" class="bg-gradient-to-br from-[#2a2a2a]/80 to-black/80 border-2 border-[#ffd700] text-[#ffd700] px-7 py-3 rounded-[25px] text-lg cursor-pointer transition-all duration-300 text-shadow-[0_0_5px_rgba(255,215,0,0.5)] shadow-[0_0_15px_rgba(255,215,0,0.2)] hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(255,215,0,0.4)] hover:bg-gradient-to-br hover:from-black/80 hover:to-[#2a2a2a]/80">
            戻る
        </button>
    </div>

    <div id="trophies-container" class="flex-grow overflow-y-auto p-2.5 scrollbar-thin scrollbar-thumb-[#ffd700] scrollbar-track-black/30 scrollbar-thumb-rounded scrollbar-track-rounded">
        <!-- トロフィーのコンテンツはJavaScriptで動的に追加 -->
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
        trophyDiv.className = 'grid grid-cols-[auto_1fr_auto_auto] items-center gap-5 p-6 bg-gradient-to-br from-black/90 to-[#2a2a2a]/90 border-2 border-[#ffd700] rounded-[15px] mb-5 transition-all duration-300 shadow-[0_0_15px_rgba(255,215,0,0.2)] w-[95%] mx-auto hover:-translate-y-1 hover:shadow-[0_0_25px_rgba(255,215,0,0.3)]';
        trophyDiv.innerHTML = `
            <img src="./img/${trophy.image}" alt="${trophy.name}" class="w-20 h-20 object-contain filter drop-shadow-[0_0_5px_rgba(255,215,0,0.3)]">
            <div class="text-[#ffd700] text-2xl text-shadow-[0_0_5px_rgba(255,215,0,0.5)] pr-4">${trophy.name}</div>
            <div class="text-[#ffd700] text-3xl font-bold text-shadow-[0_0_5px_rgba(255,215,0,0.5)] text-right min-w-[120px]">${trophy.point}pt</div>
            <button onclick="exchangePoints(${trophy.id})" class="px-7 py-3 bg-gradient-to-br from-[#2a2a2a]/80 to-black/80 border-2 border-[#ffd700] text-[#ffd700] rounded-xl text-xl cursor-pointer transition-all duration-300 min-w-[100px] text-shadow-[0_0_5px_rgba(255,215,0,0.5)] hover:bg-gradient-to-br hover:from-black/80 hover:to-[#2a2a2a]/80 hover:shadow-[0_0_15px_rgba(255,215,0,0.4)] hover:-translate-y-0.5">
                交換
            </button>
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

window.onload = function() {
    fetchUserPoints();
    fetchTrophies();
};
</script>
</body>
</html>