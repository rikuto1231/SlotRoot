<?php
ob_start(); // 出力バッファリングを開始
session_start();

// セッションチェック（ログインしていない場合はリダイレクト）
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../K1-1/K1-1.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>K1-1 管理画面</title>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4 relative">
    <!-- ログアウトボタン -->
    <button onclick="handleLogout()" 
            class="absolute top-5 right-5 px-6 py-3 text-lg text-red-600 bg-white border-2 border-red-600 rounded-lg 
                transition-all duration-300 hover:bg-red-600 hover:text-white">
        ログアウト
    </button>

    <!-- メインコンテナ -->
    <div class="flex justify-center space-x-20">
        <!-- ユーザー情報ボタン -->
        <a href="../K3-2/K3-2.php" 
        class="w-[400px] h-[150px] flex items-center justify-center text-3xl font-bold text-white bg-blue-600 
                rounded-2xl transform transition-all duration-300 hover:bg-blue-700 hover:scale-110">
            ユーザー情報
        </a>

        <!-- ランキング情報ボタン -->
        <a href="../K3-1/K3-1.php" 
        class="w-[400px] h-[150px] flex items-center justify-center text-3xl font-bold text-white bg-blue-600 
                rounded-2xl transform transition-all duration-300 hover:bg-blue-700 hover:scale-110">
            ランキング情報
        </a>
    </div>

    <script>
        function handleLogout() {
            // ログアウト処理のAPIを呼び出し
            fetch('logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../K1-1/K1-1.php';
                    }
                })
                .catch(error => {
                    console.error('ログアウトエラー:', error);
                });
        }
    </script>
</body>
</html>
<?php ob_end_flush(); // 出力バッファリングを終了 ?>