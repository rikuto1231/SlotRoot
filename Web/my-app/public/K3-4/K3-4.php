<?php
ob_start();
session_start();

// GETでuser_idを受け取る
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// DB接続して現在のデータを取得
require '../../src/common/Db_connect.php';

try {
    $pdo = getDatabaseConnection();
    
    $sql = "SELECT name, point FROM user WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $name = $user['name'];
        $points = $user['point'];
    } else {
        die('ユーザーが見つかりません');
    }
} catch (PDOException $e) {
    die('データベースエラー');
}

// 不正アクセス対策: user_idが空の場合はエラーメッセージを表示して終了
if ($user_id === '') {
    die('不正なアクセスです');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>編集</title>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto mt-20 p-12 bg-white rounded-xl shadow-lg">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-12">情報を編集</h2>
        
        <!-- 編集フォーム -->
        <form action="save.php" method="POST" class="space-y-8">
            <!-- user_idをhiddenフィールドで送信 -->
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>">

            <!-- 名前入力 -->
            <div class="space-y-4">
                <label for="name" class="block text-2xl font-semibold text-gray-700">名前:</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" 
                    required
                    class="w-full px-6 py-4 text-2xl border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            
            <!-- ポイント入力 -->
            <div class="space-y-4">
                <label for="points" class="block text-2xl font-semibold text-gray-700">ポイント:</label>
                <input 
                    type="number" 
                    id="points" 
                    name="points" 
                    value="<?= htmlspecialchars($points, ENT_QUOTES, 'UTF-8') ?>" 
                    required
                    class="w-full px-6 py-4 text-2xl border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            
            <!-- ボタン群 -->
            <div class="flex justify-center space-x-8 mt-12">
                <button 
                    type="submit"
                    class="px-12 py-4 text-xl bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200"
                >
                    変更
                </button>
                <button 
                    type="button" 
                    onclick="location.href='../K3-3/K3-3.php?user_id=<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>'"
                    class="px-12 py-4 text-xl bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors duration-200"
                >
                    戻る
                </button>
            </div>
        </form>
    </div>

    <?php if (getenv('ENVIRONMENT') === 'development'): ?>
    <script>
        console.log('編集ページ - 開発モード');
        console.log('ユーザーID:', <?php echo json_encode($user_id); ?>);
        console.log('現在の名前:', <?php echo json_encode($name); ?>);
        console.log('現在のポイント:', <?php echo json_encode($points); ?>);
    </script>
    <?php endif; ?>
</body>
</html>