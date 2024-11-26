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

    <div class="achievement">
        <img src="./img/tuma.png" alt="金の延べ棒">
        <div class="achievement-name">啓祐の金の爪楊枝</div>
        <div class="points-text">100000pt</div>
        <button class="exchange-btn" onclick="exchangePoints(1000)">交換</button>
    </div>

    <div class="achievement">
        <img src="./img/tuda.png" alt="金の延べ棒">
        <div class="achievement-name">村上神拳の使い手</div>
        <div class="points-text">10000pt</div>
        <button class="exchange-btn" onclick="exchangePoints(1000)">交換</button>
    </div>

    <div class="achievement">
        <img src="./img/mae.png" alt="金の延べ棒">
        <div class="achievement-name">津田のペット前園</div>
        <div class="points-text">1000pt</div>
        <button class="exchange-btn" onclick="exchangePoints(1000)">交換</button>
    </div>
</div>

<script>
    function goBack() {
        window.location.href = "../G1-1/G1-1.html";
    }
</script>
</body>
</html>
