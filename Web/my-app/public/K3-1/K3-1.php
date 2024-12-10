<?php
ob_start();
session_start();

// DB接続
require '../../src/common/Db_connect.php';

try {
    $pdo = getDatabaseConnection();
    
    // デフォルトのソート設定
    $allowedColumns = ['point', 'name', 'create_at', 'update_at']; // 許可されたカラム
    $sortColumn = in_array($_GET['sort'] ?? '', $allowedColumns) ? $_GET['sort'] : 'point';
    $sortOrder = (($_GET['order'] ?? '') === 'ASC') ? 'ASC' : 'DESC'; // デフォルトはDESC
    
    // SQLインジェクション対策としてホワイトリストで検証
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'point';
    }
    
    // SQLクエリ
    $sql = "SELECT user_id, name, point, create_at, update_at 
            FROM user 
            ORDER BY {$sortColumn} {$sortOrder}";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
}

// 現在のソート状態を取得（セレクトボックスの選択状態用）
$currentSort = $sortColumn . '_' . strtolower($sortOrder);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>ランキングテーブル</title>
</head>
<body class="bg-gray-50">
    <!-- ヘッダー -->
    <header class="fixed top-0 left-0 w-full bg-blue-800 text-white p-4 shadow-lg z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex-none">
                <h1 class="text-2xl font-bold">ランキング</h1>
            </div>

            <!-- 検索とソート -->
            <div class="flex-grow flex items-center gap-4">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="名前を検索..." 
                    class="w-64 px-4 py-2 rounded-lg text-gray-900 placeholder-gray-500 bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onkeyup="handleSearch()"
                >
                
                <!-- ソート選択 -->
                <select 
                    id="sortSelect"
                    onchange="sortTable(this.value)"
                    class="px-4 py-2 rounded-lg text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="point_desc" <?php echo $currentSort === 'point_desc' ? 'selected' : ''; ?>>ポイント (高い順)</option>
                    <option value="point_asc" <?php echo $currentSort === 'point_asc' ? 'selected' : ''; ?>>ポイント (低い順)</option>
                    <option value="name_asc" <?php echo $currentSort === 'name_asc' ? 'selected' : ''; ?>>名前 (昇順)</option>
                    <option value="name_desc" <?php echo $currentSort === 'name_desc' ? 'selected' : ''; ?>>名前 (降順)</option>
                    <option value="create_at_desc" <?php echo $currentSort === 'create_at_desc' ? 'selected' : ''; ?>>作成日 (新しい順)</option>
                    <option value="create_at_asc" <?php echo $currentSort === 'create_at_asc' ? 'selected' : ''; ?>>作成日 (古い順)</option>
                    <option value="update_at_desc" <?php echo $currentSort === 'update_at_desc' ? 'selected' : ''; ?>>更新日 (新しい順)</option>
                    <option value="update_at_asc" <?php echo $currentSort === 'update_at_asc' ? 'selected' : ''; ?>>更新日 (古い順)</option>
                </select>
            </div>

            <!-- 戻るボタン -->
            <div class="flex-none">
                <button 
                    onclick="window.location.href='../K2-1/K2-1.php'"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                >
                    戻る
                </button>
            </div>
        </div>
    </header>

    <!-- テーブル -->
    <div class="mt-24 max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full" id="rankingTable">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-700 to-blue-800 text-white">
                        <th class="px-6 py-4 text-lg font-semibold">順位</th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer hover:bg-blue-600" onclick="sortTableByColumn('name')">
                            名前 <?php echo $sortColumn === 'name' ? ($sortOrder === 'ASC' ? '▲' : '▼') : ''; ?>
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer hover:bg-blue-600" onclick="sortTableByColumn('point')">
                            ポイント <?php echo $sortColumn === 'point' ? ($sortOrder === 'ASC' ? '▲' : '▼') : ''; ?>
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer hover:bg-blue-600" onclick="sortTableByColumn('create_at')">
                            作成日 <?php echo $sortColumn === 'create_at' ? ($sortOrder === 'ASC' ? '▲' : '▼') : ''; ?>
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer hover:bg-blue-600" onclick="sortTableByColumn('update_at')">
                            更新日 <?php echo $sortColumn === 'update_at' ? ($sortOrder === 'ASC' ? '▲' : '▼') : ''; ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($rankings as $index => $ranking): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center"><?php echo ($index + 1); ?></td>
                            <td class="px-6 py-4">
                                <form method="POST" action="../K3-3/K3-3.php" class="inline">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($ranking['user_id']); ?>">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        <?php echo htmlspecialchars($ranking['name']); ?>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center"><?php echo number_format($ranking['point']); ?>P</td>
                            <td class="px-6 py-4 text-center"><?php echo date('Y/m/d H:i', strtotime($ranking['create_at'])); ?></td>
                            <td class="px-6 py-4 text-center"><?php echo date('Y/m/d H:i', strtotime($ranking['update_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // 検索とソートの状態を保持する変数
        let currentSearchQuery = '';

        // 検索処理
        function handleSearch() {
            const searchInput = document.getElementById('searchInput');
            currentSearchQuery = searchInput.value;
            filterTable();
        }

        // テーブルのフィルタリング
        function filterTable() {
            const query = currentSearchQuery.toLowerCase();
            const table = document.getElementById('rankingTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[1];
                if (nameCell) {
                    const name = nameCell.textContent || nameCell.innerText;
                    if (name.toLowerCase().indexOf(query) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        // ソート処理
        function sortTable(value) {
            // 現在の検索クエリをURLパラメータとして保持
            const searchInput = document.getElementById('searchInput');
            const params = new URLSearchParams(window.location.search);
            
            // ソートパラメータを設定
            const [column, order] = value.split('_');
            params.set('sort', column);
            params.set('order', order.toUpperCase());
            
            // 検索クエリをローカルストレージに保存
            localStorage.setItem('searchQuery', searchInput.value);
            
            // ページを更新
            window.location.href = `?${params.toString()}`;
        }

        function sortTableByColumn(column) {
            const params = new URLSearchParams(window.location.search);
            const currentSort = params.get('sort');
            const currentOrder = params.get('order');
            
            let newOrder = 'DESC';
            if (currentSort === column && currentOrder === 'DESC') {
                newOrder = 'ASC';
            }
            
            // 検索クエリをローカルストレージに保存
            const searchInput = document.getElementById('searchInput');
            localStorage.setItem('searchQuery', searchInput.value);

            // ソートパラメータを設定して更新
            params.set('sort', column);
            params.set('order', newOrder);
            window.location.href = `?${params.toString()}`;
        }

        // ページ読み込み時に検索状態を復元
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const savedQuery = localStorage.getItem('searchQuery');
            
            if (savedQuery) {
                searchInput.value = savedQuery;
                currentSearchQuery = savedQuery;
                filterTable();
            }
        });
    </script>
</body>
</html>