// 初期ポイント数
let currentPoints = 0;

// ポイントを交換する関数
function exchangePoints(cost) {
    if (currentPoints >= cost) {
        currentPoints -= cost;
        document.getElementById('points').innerText = currentPoints;
        alert('交換しました！');
    } else {
        alert('ポイントが足りません。');
    }
}

// 戻るボタンの処理（ブラウザの履歴に戻る）
function goBack() {
    window.history.back();
}
