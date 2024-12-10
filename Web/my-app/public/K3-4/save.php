<?php
ob_start();
session_start();

// DB接続
require '../../src/common/Db_connect.php';

try {
    $pdo = getDatabaseConnection();
    
    // POSTデータの受け取り
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // データの取得と検証
        $user_id = $_POST['user_id'] ?? '';  // 変更
        $name = $_POST['name'] ?? '';        // 変更
        $points = $_POST['points'] ?? null;   // 変更

        // データ検証
        if (empty($user_id) || empty($name) || $points === null) {
            throw new Exception('必要なデータが不足しています');
        }

        // 数値の検証
        if (!is_numeric($user_id) || !is_numeric($points)) {
            throw new Exception('入力データが不正です');
        }

        // トランザクション開始
        $pdo->beginTransaction();

        try {
            // 更新日時を含めて更新
            $sql = "UPDATE user 
                   SET name = :name, 
                       point = :point,
                       update_at = CURRENT_TIMESTAMP 
                   WHERE user_id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':point', (int)$points, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);

            // 実行
            if (!$stmt->execute()) {
                throw new Exception('データの更新に失敗しました');
            }

            // トランザクションコミット
            $pdo->commit();

            // 成功時のリダイレクト - user_idを含めて元の詳細ページに戻る
            header("Location: ../K3-3/K3-3.php?user_id=" . urlencode($user_id));
            exit;

        } catch (Exception $e) {
            // トランザクションロールバック
            $pdo->rollBack();
            throw $e;
        }
    } else {
        throw new Exception('不正なアクセスです');
    }

} catch (Exception $e) {
    // エラーページ表示
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <?php require_once '../../src/common/common_head.php'; ?>
        <title>エラー</title>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto mt-20 p-12 bg-white rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="text-4xl font-bold text-red-600 mb-8">エラー</h2>
                <p class="text-2xl text-gray-700 mb-12"><?= htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') ?></p>
                <button 
                    onclick="window.history.back()"
                    class="px-12 py-4 text-xl bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors duration-200"
                >
                    戻る
                </button>
            </div>
        </div>

        <?php if (getenv('ENVIRONMENT') === 'development'): ?>
        <div class="max-w-4xl mx-auto mt-8 p-6 bg-red-50 rounded-xl">
            <p class="text-red-600">
                <?= htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}
?>