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

function preload() {
    // 画像のロード
    this.load.image('background', 'assets/images/background.png');
    this.load.image('player', 'assets/images/player.png');
    this.load.image('enemy', 'assets/images/obstacle.png');
}

function create() {
    // 背景の設定
    background = this.add.tileSprite(520, 260, 1040, 520, 'background');

    // プレイヤーの設定
    player = this.physics.add.sprite(520, 260, 'player');
    player.setScale(0.8);
    player.setCollideWorldBounds(true);

    // 敵グループの設定
    enemies = this.physics.add.group();

    // WASDキーの設定
    cursors = this.input.keyboard.addKeys({
        up: Phaser.Input.Keyboard.KeyCodes.W,
        down: Phaser.Input.Keyboard.KeyCodes.S,
        left: Phaser.Input.Keyboard.KeyCodes.A,
        right: Phaser.Input.Keyboard.KeyCodes.D
    });

    // スコアテキストの設定
    scoreText = this.add.text(16, 16, 'Score: 0', { 
        fontSize: '32px', 
        fill: '#fff',
        stroke: '#000',
        strokeThickness: 4
    });

    // 敵との衝突判定
    this.physics.add.collider(player, enemies, gameOverHandler, null, this);

    // 定期的に敵を生成
    this.time.addEvent({
        delay: 1000,
        callback: spawnEnemy,
        callbackScope: this,
        loop: true
    });

    // スコア更新
    this.time.addEvent({
        delay: 100,
        callback: updateScore,
        callbackScope: this,
        loop: true
    });
}

function update() {
    if (!gameOver) {
        // プレイヤーの移動
        const moveSpeed = 200;
        
        // 水平方向の移動
        if (cursors.left.isDown) {
            player.setVelocityX(-moveSpeed);
        } else if (cursors.right.isDown) {
            player.setVelocityX(moveSpeed);
        } else {
            player.setVelocityX(0);
        }

        // 垂直方向の移動
        if (cursors.up.isDown) {
            player.setVelocityY(-moveSpeed);
        } else if (cursors.down.isDown) {
            player.setVelocityY(moveSpeed);
        } else {
            player.setVelocityY(0);
        }

        // 背景のスクロール
        background.tilePositionX += 1 * gameSpeed;

        // 敵の移動と削除
        enemies.children.iterate(function(enemy) {
            if (enemy) {
                if (enemy.x < -enemy.width || enemy.x > config.width + enemy.width ||
                    enemy.y < -enemy.height || enemy.y > config.height + enemy.height) {
                    enemy.destroy();
                }
            }
        });

        // ゲームスピードの増加
        gameSpeed += 0.0001;
    }
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
    }
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