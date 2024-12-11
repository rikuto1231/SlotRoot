<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../G3-1/G3-1.php');
    exit;
}

// ノリ打ちモードのパラメータを取得
$isSharedMode = isset($_GET['mode']) && $_GET['mode'] === 'shared';
$_SESSION['is_shared_mode'] = $isSharedMode;  // セッションにフラグを保存
$roomPassword = $isSharedMode ? $_GET['room'] : '';
$totalPoints = $isSharedMode ? (int)$_GET['points'] : 0;

// ユーザー情報をJSON形式で準備
$userInfo = [
    'user_id' => $_SESSION['user_id'],
    'user_name' => $_SESSION['user_name'] ?? '',
    'mode' => $isSharedMode ? 'shared' : 'solo',
    'room_password' => $roomPassword,
    'total_points' => $totalPoints
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>スロット - 麻生無双</title>
    <link rel="shortcut icon" href="TemplateData/favicon.ico">
    <link rel="stylesheet" href="TemplateData/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            background-attachment: fixed;
        }
        
        #unity-container {
            background: rgba(0, 0, 0, 0.7);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
        }

        #unity-canvas {
            background: #000;
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.05);
        }

        #unity-loading-bar {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 5px;
            padding: 10px;
        }

        #unity-progress-bar-empty {
            background: rgba(255, 255, 255, 0.1);
        }

        #unity-progress-bar-full {
            background: linear-gradient(90deg, #ffd700, #ffb700);
        }

        #unity-footer {
            background: rgba(0, 0, 0, 0.6);
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        #unity-build-title {
            color: #ffd700;
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        #unity-fullscreen-button {
            background: linear-gradient(45deg, #1a1a1a, #2a2a2a);
            border: 1px solid #ffd700;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #unity-fullscreen-button:hover {
            background: linear-gradient(45deg, #2a2a2a, #3a3a3a);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        #unity-warning { 
            position: absolute;
            left: 50%;
            top: 5%;
            transform: translateX(-50%);
            background: rgba(200, 0, 0, 0.8);
            padding: 10px;
            border-radius: 5px;
            display: none;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid #ffd700;
            border-radius: 5px;
            color: #ffd700;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-weight: bold;
            transition: all 0.3s ease;
            z-index: 1000;
            cursor: pointer;
        }

        .back-button:hover {
            background: rgba(0, 0, 0, 0.9);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <button class="back-button" id="backButton">
        ← 戻る
    </button>

    <div id="unity-container" class="unity-desktop">
        <canvas id="unity-canvas" width=960 height=600 tabindex="-1"></canvas>
        <div id="unity-loading-bar">
            <div id="unity-logo"></div>
            <div id="unity-progress-bar-empty">
                <div id="unity-progress-bar-full"></div>
            </div>
        </div>
        <div id="unity-warning"></div>
        <div id="unity-footer">
            <div id="unity-webgl-logo"></div>
            <div id="unity-fullscreen-button"></div>
            <div id="unity-build-title">スロット</div>
        </div>
    </div>

    <script>
        var userInfo = <?php echo json_encode($userInfo); ?>;
        var unityInstance = null;
        
        var container = document.querySelector("#unity-container");
        var canvas = document.querySelector("#unity-canvas");
        var loadingBar = document.querySelector("#unity-loading-bar");
        var progressBarFull = document.querySelector("#unity-progress-bar-full");
        var fullscreenButton = document.querySelector("#unity-fullscreen-button");
        var warningBanner = document.querySelector("#unity-warning");
        var backButton = document.getElementById('backButton');

        // Warning機能
        function unityShowBanner(msg, type) {
            function updateBannerVisibility() {
                warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
            }
            var div = document.createElement('div');
            div.innerHTML = msg;
            warningBanner.appendChild(div);
            if (type == 'error') div.style = 'background: red; padding: 10px;';
            else {
                if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
                setTimeout(function() {
                    warningBanner.removeChild(div);
                    updateBannerVisibility();
                }, 5000);
            }
            updateBannerVisibility();
        }

        var buildUrl = "Build";
        var loaderUrl = buildUrl + "/Downloads.loader.js";
        var config = {
            dataUrl: buildUrl + "/Downloads.data",
            frameworkUrl: buildUrl + "/Downloads.framework.js",
            codeUrl: buildUrl + "/Downloads.wasm",
            streamingAssetsUrl: "StreamingAssets",
            companyName: "DefaultCompany",
            productName: "Sample_Slot",
            productVersion: "1.0",
            showBanner: unityShowBanner,
        };

        // レスポンシブ対応
        if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
            var meta = document.createElement('meta');
            meta.name = 'viewport';
            meta.content = 'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, shrink-to-fit=yes';
            document.getElementsByTagName('head')[0].appendChild(meta);
            container.className = "unity-mobile";
            canvas.className = "unity-mobile";
        } else {
            canvas.style.width = "960px";
            canvas.style.height = "600px";
        }

        loadingBar.style.display = "block";

        var script = document.createElement("script");
        script.src = loaderUrl;
        script.onload = () => {
            createUnityInstance(canvas, config, (progress) => {
                progressBarFull.style.width = 100 * progress + "%";
            }).then((instance) => {
                unityInstance = instance;
                loadingBar.style.display = "none";
                // フルスクリーンボタンの設定
                fullscreenButton.onclick = () => {
                    unityInstance.SetFullscreen(1);
                };
                
                // セッション情報をUnityに送信
                unityInstance.SendMessage("SessionManager", "SetUserInfo", JSON.stringify(userInfo));

                // ノリ打ちモードの場合の追加処理
                if (userInfo.mode === 'shared') {
                    unityInstance.SendMessage("SharedPlayManager", "InitializeSharedPlay", JSON.stringify({
                        roomPassword: userInfo.room_password,
                        totalPoints: userInfo.total_points
                    }));
                }
            }).catch((message) => {
                alert(message);
            });
        };

        document.body.appendChild(script);

        // 戻るボタンの処理
        backButton.addEventListener('click', function() {
            if (unityInstance) {
                if (userInfo.mode === 'shared') {
                    // ノリ打ちモード時は専用の終了処理を実行
                    unityInstance.SendMessage("SharedPlayManager", "EndSharedPlay");
                    // 少し待ってから遷移
                    setTimeout(() => {
                        window.location.href = '../G7-1/G7-1.php';
                    }, 500);
                } else {
                    // 通常モード時は現在のポイントを保存してから遷移
                    unityInstance.SendMessage("ReelManager", "SaveCurrentPoints");
                    setTimeout(() => {
                        window.location.href = '../G7-1/G7-1.php';
                    }, 500);
                }
            } else {
                window.location.href = '../G7-1/G7-1.php';
            }
        });

        // ブラウザバック時の確認
        window.onbeforeunload = function() {
            if (userInfo.mode === 'shared') {
                return "ノリ打ちを終了しますか？\n相手プレイヤーとの接続が切断されます。";
            }
        };
    </script>
</body>
</html>