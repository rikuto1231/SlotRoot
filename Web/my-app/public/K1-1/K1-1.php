<?php
session_start(); // セッション開始

// DB接続
require '../../src/common/Db_connect.php';

try {
    $pdo = getDatabaseConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $number = $_POST['id'] ?? ''; // ログイン番号を取得
        $password = $_POST['password'] ?? '';

        // ユーザーの存在を確認
        $stmt = $pdo->prepare("SELECT admin_id, number FROM admin WHERE number = :number AND pass = :password");
        $stmt->bindParam(':number', $number, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // セッションに管理者情報を保存
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_number'] = $user['number'];
            $_SESSION['is_admin'] = true;
            
            // ログイン成功時の処理
            echo "<script>
                console.log('ログイン成功！');
                window.location.href = '../K2-1/K2-1.php';  // リダイレクト先を指定
            </script>";
            exit;
        } else {
            $error_message = "IDまたはパスワードが間違っています。";
            echo "<script>console.log('ログイン失敗: {$error_message}');</script>";
        }
    }
} catch (PDOException $e) {
    echo "<script>console.error('エラー: " . addslashes($e->getMessage()) . "');</script>";
}

// セッションチェック（既にログインしている場合はリダイレクト）
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: ../K2-1/K2-1.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>ログインページ</title>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="max-w-4xl w-full mx-auto p-8">
        <div class="bg-white rounded-2xl shadow-2xl p-16">
            <!-- ヘッダー部分 -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">管理者ログイン</h1>
                <p class="text-gray-600">アカウント情報を入力してログインしてください</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-8">
                <!-- ID入力フィールド -->
                <div class="space-y-2">
                    <label for="id" class="block text-xl font-medium text-gray-700">ログインID</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="id" 
                            name="id" 
                            placeholder="IDを入力してください" 
                            required
                            class="w-full px-6 py-4 text-lg border border-gray-300 rounded-xl bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                        >
                    </div>
                </div>

                <!-- パスワード入力フィールド -->
                <div class="space-y-2">
                    <label for="password" class="block text-xl font-medium text-gray-700">パスワード</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="パスワードを入力してください" 
                            required
                            class="w-full px-6 py-4 text-lg border border-gray-300 rounded-xl bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                        >
                    </div>
                </div>

                <!-- ログインボタン -->
                <div class="pt-6">
                    <button 
                        type="submit" 
                        class="w-full px-8 py-4 text-xl text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all duration-200 font-medium"
                    >
                        ログイン
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (getenv('ENVIRONMENT') === 'development'): ?>
    <script>
        // 開発環境でのみ実行されるテストコード
        function testLoginSession() {
            console.log('=== セッション状態テスト ===');
            console.log('セッションチェック中...');
            
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    console.log('セッション情報:', data);
                });
        }

        document.addEventListener('DOMContentLoaded', testLoginSession);
    </script>
    <?php endif; ?>
</body>
</html>