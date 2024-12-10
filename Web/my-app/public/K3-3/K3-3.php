<?php
ob_start();
session_start();

// DB接続
require '../../src/common/Db_connect.php';

$pdo = getDatabaseConnection();

// POSTデータの取得
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
} elseif (isset($_GET['user_id'])) {  // GET パラメータのチェックを追加
    $user_id = $_GET['user_id'];
} else {
    // どちらの方法でもuser_idが取得できない場合
    $name = "不正なアクセスです";
    $points = 0;
    $user_id = null;
    $create_at = null;
    $update_at = null;
}

if ($user_id) {  // user_idが存在する場合のみDBアクセス
    // データベースから該当ユーザーを取得
    $sql = "SELECT name, point, create_at, update_at FROM user WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $name = $user['name'];
        $points = $user['point'];
        $create_at = $user['create_at'];
        $update_at = $user['update_at'];
    } else {
        $name = "データが見つかりません";
        $points = 0;
        $create_at = null;
        $update_at = null;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>詳細表示</title>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto mt-20 p-12 bg-white rounded-xl shadow-lg">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-12">詳細情報</h2>
        
        <!-- ユーザー情報表示 -->
        <div class="space-y-8 mb-12">
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">名前:</span>
                <span class="text-2xl text-gray-800"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">ポイント:</span>
                <span class="text-2xl text-gray-800"><?= number_format($points) ?>pt</span>
            </div>

            <?php if ($create_at): ?>
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">作成日:</span>
                <span class="text-2xl text-gray-800"><?= date('Y/m/d H:i', strtotime($create_at)) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($update_at): ?>
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">更新日:</span>
                <span class="text-2xl text-gray-800"><?= date('Y/m/d H:i', strtotime($update_at)) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- ボタン群 -->
        <div class="flex justify-center space-x-8 mt-12">
            <?php if ($user_id !== null): ?>
                <button 
                    onclick="location.href='../K3-4/K3-4.php?user_id=<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>'"
                    class="px-12 py-4 text-xl bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200"
                >
                    編集
                </button>
            <?php endif; ?>
            <button 
                onclick="location.href='../K3-2/K3-2.php'"
                class="px-12 py-4 text-xl bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors duration-200"
            >
                戻る
            </button>
        </div>
    </div>

    <?php if (getenv('ENVIRONMENT') === 'development'): ?>
    <script>
        console.log('詳細表示ページ - 開発モード');
        console.log('ユーザーID:', <?php echo json_encode($user_id); ?>);
        console.log('名前:', <?php echo json_encode($name); ?>);
        console.log('ポイント:', <?php echo json_encode($points); ?>);
        console.log('作成日:', <?php echo json_encode($create_at); ?>);
        console.log('更新日:', <?php echo json_encode($update_at); ?>);
    </script>
    <?php endif; ?>
</body>
</html>