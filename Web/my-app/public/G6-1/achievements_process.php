<?php

error_reporting(0);

function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

require '../../src/common/Db_connect.php';

session_start();

class UserAchievements {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserTrophies($userId) {
        try {
            // 後々imageカラム取得してパスreturnに入れる
            $sql = "
                SELECT 
                    t.trophy_id,
                    t.name AS trophy_name,
                    t.point AS trophy_point,
                    t.image,
                    COUNT(ut.trophy_id) as quantity
                FROM 
                    trophy t
                INNER JOIN 
                    user_trophy ut ON t.trophy_id = ut.trophy_id
                WHERE 
                    ut.user_id = :user_id
                GROUP BY 
                    t.trophy_id, t.name, t.point,t.image
                ORDER BY 
                    t.trophy_id ASC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $trophies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($trophies)) {
                return [
                    'success' => true,
                    'message' => '実績がまだありません',
                    'trophies' => []
                ];
            }
            
            return [
                'success' => true,
                'trophies' => $trophies
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'データベースエラーが発生しました。',
                'error' => $e->getMessage()
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!isset($_SESSION['user_id'])) {
            sendJsonResponse([
                'success' => false,
                'message' => 'ログインが必要です。'
            ]);
            exit;
        }
        
        $pdo = getDatabaseConnection();
        $achievements = new UserAchievements($pdo);
        
        $result = $achievements->getUserTrophies($_SESSION['user_id']);
        sendJsonResponse($result);
        
    } catch (Exception $e) {
        sendJsonResponse([
            'success' => false,
            'message' => '処理中にエラーが発生しました。',
            'error' => $e->getMessage()
        ]);
    }
} else {
    sendJsonResponse([
        'success' => false,
        'message' => '不正なリクエストです。'
    ]);
}
?>