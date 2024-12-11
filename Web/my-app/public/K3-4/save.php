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
        $user_id = $_POST['user_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $points = $_POST['points'] ?? null;

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
            // ユーザー情報の更新
            $sql = "UPDATE user 
                   SET name = :name, 
                       point = :point,
                       update_at = CURRENT_TIMESTAMP 
                   WHERE user_id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':point', (int)$points, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception('データの更新に失敗しました');
            }

            // 実績数の更新
            if (isset($_POST['trophy_count'])) {
                foreach ($_POST['trophy_count'] as $trophy_id => $count) {
                    // 現在の実績数を取得
                    $currentCountSql = "SELECT COUNT(*) as current_count 
                                      FROM user_trophy 
                                      WHERE user_id = :user_id AND trophy_id = :trophy_id";
                    $countStmt = $pdo->prepare($currentCountSql);
                    $countStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $countStmt->bindValue(':trophy_id', $trophy_id, PDO::PARAM_INT);
                    $countStmt->execute();
                    $currentCount = $countStmt->fetch(PDO::FETCH_ASSOC)['current_count'];

                    if ($count > $currentCount) {
                        // 実績を追加
                        $diff = $count - $currentCount;
                        for ($i = 0; $i < $diff; $i++) {
                            $insertSql = "INSERT INTO user_trophy (user_id, trophy_id) 
                                        VALUES (:user_id, :trophy_id)";
                            $insertStmt = $pdo->prepare($insertSql);
                            $insertStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                            $insertStmt->bindValue(':trophy_id', $trophy_id, PDO::PARAM_INT);
                            $insertStmt->execute();
                        }
                    } elseif ($count < $currentCount) {
                        // 実績を削除
                        $diff = $currentCount - $count;
                        $deleteSql = "DELETE FROM user_trophy 
                                     WHERE user_id = :user_id AND trophy_id = :trophy_id 
                                     LIMIT :limit";
                        $deleteStmt = $pdo->prepare($deleteSql);
                        $deleteStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                        $deleteStmt->bindValue(':trophy_id', $trophy_id, PDO::PARAM_INT);
                        $deleteStmt->bindValue(':limit', $diff, PDO::PARAM_INT);
                        $deleteStmt->execute();
                    }
                }
            }

            // トランザクションコミット
            $pdo->commit();

            // 成功時のリダイレクト
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