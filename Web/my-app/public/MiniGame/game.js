const config = {
    type: Phaser.AUTO,
    parent: 'game-container',
    width: 1040,    // 800 * 1.3
    height: 520,    // 400 * 1.3
    physics: {
        default: 'arcade',
        arcade: {
            gravity: { y: 0 },
            debug: false
        }
    },
    scene: {
        preload: preload,
        create: create,
        update: update
    }
};

const game = new Phaser.Game(config);

let player;
let enemies;
let score = 0;
let scoreText;
let gameOver = false;
let gameSpeed = 1;
let background;
let cursors;
let enemySpawnRate = 1000; // 初期敵生成間隔（ms）
let isSpawnRateIncreased = false; // 敵生成間隔が変更されたかを監視
let giftSpawnRate = 2000; // gift生成間隔（ms）
let unGiftSpawnRate = 3000; // un_gift生成間隔（ms）
let gifts;
let unGifts;



function preload() {
        this.load.image('background', 'assets/images/background.png');
        this.load.image('player', 'assets/images/player.png');
        this.load.image('enemy', 'assets/images/obstacle.png');
        this.load.image('gift', 'assets/images/gift.png');
        this.load.image('un_gift', 'assets/images/un_gift.png');
}

function create() {
    background = this.add.tileSprite(520, 260, 1040, 520, 'background');

    player = this.physics.add.sprite(520, 260, 'player');
    player.setScale(0.8);
    player.setCollideWorldBounds(true);

    enemies = this.physics.add.group();
    gifts = this.physics.add.group();
    unGifts = this.physics.add.group();

    cursors = this.input.keyboard.addKeys({
        up: Phaser.Input.Keyboard.KeyCodes.W,
        down: Phaser.Input.Keyboard.KeyCodes.S,
        left: Phaser.Input.Keyboard.KeyCodes.A,
        right: Phaser.Input.Keyboard.KeyCodes.D
    });

    scoreText = this.add.text(16, 16, 'Score: 0', {
        fontSize: '32px',
        fill: '#fff',
        stroke: '#000',
        strokeThickness: 4
    });

    this.physics.add.collider(player, enemies, gameOverHandler, null, this);
    this.physics.add.overlap(player, gifts, collectGift, null, this);
    this.physics.add.overlap(player, unGifts, hitUnGift, null, this);

    this.enemySpawnTimer = this.time.addEvent({
        delay: enemySpawnRate,
        callback: spawnEnemy,
        callbackScope: this,
        loop: true
    });

    this.giftSpawnTimer = this.time.addEvent({
        delay: giftSpawnRate,
        callback: spawnGift,
        callbackScope: this,
        loop: true
    });

    this.unGiftSpawnTimer = this.time.addEvent({
        delay: unGiftSpawnRate,
        callback: spawnUnGift,
        callbackScope: this,
        loop: true
    });

    this.time.addEvent({
        delay: 100,
        callback: updateScore,
        callbackScope: this,
        loop: true
    });
}

function update() {
    if (!gameOver) {
        const moveSpeed = 200;

        if (cursors.left.isDown) {
            player.setVelocityX(-moveSpeed);
        } else if (cursors.right.isDown) {
            player.setVelocityX(moveSpeed);
        } else {
            player.setVelocityX(0);
        }

        if (cursors.up.isDown) {
            player.setVelocityY(-moveSpeed);
        } else if (cursors.down.isDown) {
            player.setVelocityY(moveSpeed);
        } else {
            player.setVelocityY(0);
        }

        background.tilePositionX += 1 * gameSpeed;

        enemies.children.iterate(function (enemy) {
            if (enemy && (enemy.x < -enemy.width || enemy.x > config.width + enemy.width ||
                enemy.y < -enemy.height || enemy.y > config.height + enemy.height)) {
                enemy.destroy();
            }
        });

        gifts.children.iterate(function (gift) {
            if (gift && gift.y > config.height) {
                gift.destroy();
            }
        });

        unGifts.children.iterate(function (unGift) {
            if (unGift && unGift.y > config.height) {
                unGift.destroy();
            }
        });

        gameSpeed += 0.0001;
    }
}

function spawnGift() {
    const gift = gifts.create(Phaser.Math.Between(50, 990), 0, 'gift');
    gift.setVelocity(0, 200 * gameSpeed);
    gift.setCollideWorldBounds(true);
    gift.setBounce(1);
}

function spawnUnGift() {
    const unGift = unGifts.create(Phaser.Math.Between(50, 990), 0, 'un_gift');
    unGift.setVelocity(0, 200 * gameSpeed);
    unGift.setCollideWorldBounds(true);
    unGift.setBounce(1);
}

function collectGift(player, gift) {
    score += 20;
    scoreText.setText('Score: ' + score);
    gift.destroy();
}

function hitUnGift(player, unGift) {
    score -= 50;
    scoreText.setText('Score: ' + score);
    unGift.destroy();
}

function spawnEnemy() {
    if (!gameOver) {
        // ランダムな出現位置と移動パターンを設定
        const patterns = [
            // 右から左
            () => {
                const enemy = enemies.create(1040, Phaser.Math.Between(50, 470), 'enemy');
                enemy.setVelocity(-200 * gameSpeed, 0);
            },
            // 上から下
            () => {
                const enemy = enemies.create(Phaser.Math.Between(50, 990), 0, 'enemy');
                enemy.setVelocity(0, 200 * gameSpeed);
            },
            // 斜めの動き
            () => {
                const enemy = enemies.create(1040, Phaser.Math.Between(50, 470), 'enemy');
                enemy.setVelocity(-200 * gameSpeed, Phaser.Math.Between(-100, 100) * gameSpeed);
            }
        ];

        // ランダムなパターンを選択
        const pattern = Phaser.Math.RND.pick(patterns);
        pattern();
    }
}

function updateScore() {
    if (!gameOver) {
        score += 2;
        scoreText.setText('Score: ' + score);

        // スコアが500を超えたら敵生成間隔を調整
        if (score >= 500 && !isSpawnRateIncreased) {
            isSpawnRateIncreased = true;
            increaseEnemySpawnRate(this);
        }
    }
}

function increaseEnemySpawnRate(scene) {
    // 生成間隔を1.6倍短くする
    enemySpawnRate /= 1.6;

    // タイマーを再作成
    scene.enemySpawnTimer.remove();
    scene.enemySpawnTimer = scene.time.addEvent({
        delay: enemySpawnRate,
        callback: spawnEnemy,
        callbackScope: scene,
        loop: true
    });
}

function gameOverHandler() {
    gameOver = true;
    this.physics.pause();

    // ゲームオーバー表示
    const gameOverText = this.add.text(520, 220, 'Game Over', {
        fontSize: '64px',
        fill: '#fff',
        stroke: '#000',
        strokeThickness: 6
    });
    gameOverText.setOrigin(0.5);

    const finalScoreText = this.add.text(520, 300, 'Score: ' + score, {
        fontSize: '32px',
        fill: '#fff',
        stroke: '#000',
        strokeThickness: 4
    });
    finalScoreText.setOrigin(0.5);

    // リトライボタン
    const retryButton = this.add.text(520, 380, 'もう一度プレイ', {
        fontSize: '24px',
        fill: '#fff',
        backgroundColor: '#000',
        padding: { x: 20, y: 10 },
        stroke: '#fff',
        strokeThickness: 2
    });
    retryButton.setOrigin(0.5);
    retryButton.setInteractive();
    retryButton.on('pointerdown', () => {
        location.reload();
    });

    // スコアを保存
    sendScore(score);
}

function sendScore(score) {
    fetch('save_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            score: score
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pointsText = this.add.text(520, 340, data.message, {
                fontSize: '24px',
                fill: '#ffd700',
                stroke: '#000',
                strokeThickness: 4
            });
            pointsText.setOrigin(0.5);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}