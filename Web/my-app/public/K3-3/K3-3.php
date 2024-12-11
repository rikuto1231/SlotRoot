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
$trophy_types = 0;
$trophy_count = 0;
$trophies = [];
}

if ($user_id) {
// 基本情報と実績集計の取得
$sql = "SELECT 
            u.name, 
            u.point, 
            u.create_at, 
            u.update_at,
            COUNT(DISTINCT ut.trophy_id) as trophy_types,
            COUNT(ut.trophy_id) as trophy_count
        FROM user u
        LEFT JOIN user_trophy ut ON u.user_id = ut.user_id
        WHERE u.user_id = :user_id
        GROUP BY u.name, u.point, u.create_at, u.update_at";
        
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 実績の詳細情報を取得
$trophySql = "SELECT 
                t.name as trophy_name,
                t.point as trophy_point,
                COUNT(ut.trophy_id) as achievement_count
            FROM trophy t
            INNER JOIN user_trophy ut ON t.trophy_id = ut.trophy_id
            WHERE ut.user_id = :user_id
            GROUP BY t.trophy_id, t.name, t.point
            ORDER BY t.trophy_id";
            
$trophyStmt = $pdo->prepare($trophySql);
$trophyStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$trophyStmt->execute();
$trophies = $trophyStmt->fetchAll(PDO::FETCH_ASSOC);

if ($user) {
    $name = $user['name'];
    $points = $user['point'];
    $create_at = $user['create_at'];
    $update_at = $user['update_at'];
    $trophy_types = $user['trophy_types'];
    $trophy_count = $user['trophy_count'];
} else {
    $name = "データが見つかりません";
    $points = 0;
    $create_at = null;
    $update_at = null;
    $trophy_types = 0;
    $trophy_count = 0;
    $trophies = [];
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

        <!-- 実績情報を追加 -->
        <div class="space-y-8">
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">実績種類:</span>
                <span class="text-2xl text-gray-800"><?= number_format($trophy_types) ?>種類</span>
            </div>

            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl">
                <span class="text-2xl font-semibold text-gray-700">実績数:</span>
                <span class="text-2xl text-gray-800"><?= number_format($trophy_count) ?>個</span>
            </div>

            <!-- 実績詳細の折りたたみセクション -->
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <button 
                    onclick="toggleTrophyDetails()"
                    class="w-full flex items-center justify-between p-6 focus:outline-none"
                >
                    <span class="text-2xl font-semibold text-gray-700">実績詳細</span>
                    <span id="toggleIcon" class="text-2xl">▼</span>
                </button>
                
                <div id="trophyDetails" class="hidden">
                    <div class="max-h-96 overflow-y-auto p-6 border-t border-gray-200">
                        <?php if (!empty($trophies)): ?>
                            <?php foreach ($trophies as $trophy): ?>
                                <div class="flex items-center justify-between py-4 border-b border-gray-200 last:border-0">
                                    <span class="text-xl text-gray-800"><?= htmlspecialchars($trophy['trophy_name']) ?></span>
                                    <div class="flex items-center gap-8">
                                        <span class="text-xl text-blue-600"><?= number_format($trophy['trophy_point']) ?>pt</span>
                                        <span class="text-xl text-gray-600">×<?= number_format($trophy['achievement_count']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-xl text-gray-600 text-center py-4">実績がありません</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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
    console.log('実績種類:', <?php echo json_encode($trophy_types); ?>);
    console.log('実績数:', <?php echo json_encode($trophy_count); ?>);
    console.log('実績詳細:', <?php echo json_encode($trophies); ?>);
    console.log('作成日:', <?php echo json_encode($create_at); ?>);
    console.log('更新日:', <?php echo json_encode($update_at); ?>);
</script>
<?php endif; ?>

<script>
    function toggleTrophyDetails() {
        const details = document.getElementById('trophyDetails');
        const icon = document.getElementById('toggleIcon');
        
        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
            icon.textContent = '▲';
        } else {
            details.classList.add('hidden');
            icon.textContent = '▼';
        }
    }
</script>
</body>
</html>