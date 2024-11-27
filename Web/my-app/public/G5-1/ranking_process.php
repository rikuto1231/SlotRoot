<?php

// header情報の先送信を防ぐため
error_reporting(0);

// JSON形式のレスポンス関数
function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

require '../../src/common/Db_connect.php';

session_start();

class RankingSystem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    //全体ランキング取得
    public function getRankings() {
        try {
            $sql = "
                SELECT 
                    u.user_id, 
                    u.name, 
                    u.point AS user_point, 
                    COALESCE(SUM(t.point), 0) AS trophy_point,
                    (u.point + COALESCE(SUM(t.point), 0)) AS total_point
                FROM 
                    user u
                LEFT JOIN 
                    user_trophy ut ON u.user_id = ut.user_id
                LEFT JOIN 
                    trophy t ON ut.trophy_id = t.trophy_id
                GROUP BY 
                    u.user_id, u.name, u.point
                ORDER BY 
                    total_point DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'rankings' => $rankings
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'データベースエラーが発生しました。' . $e->getMessage()
            ];
        }
    }
    
    //ユーザランキング情報の取得
    public function getUserRank($userId) {
        try {
            $totalPointSql = "
                SELECT 
                    u.name,
                    u.point AS user_point, 
                    COALESCE(SUM(t.point), 0) AS trophy_point,
                    (u.point + COALESCE(SUM(t.point), 0)) AS total_point
                FROM 
                    user u
                LEFT JOIN 
                    user_trophy ut ON u.user_id = ut.user_id
                LEFT JOIN 
                    trophy t ON ut.trophy_id = t.trophy_id
                WHERE 
                    u.user_id = :user_id
                GROUP BY 
                    u.user_id, u.point, u.name
            ";
            $totalPointStmt = $this->pdo->prepare($totalPointSql);
            $totalPointStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $totalPointStmt->execute();
            $userPointData = $totalPointStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userPointData) {
                return [
                    'success' => false,
                    'message' => 'ユーザーが見つかりません。'
                ];
            }
            
            $rankSql = "
                SELECT COUNT(*) + 1 AS rank
                FROM (
                    SELECT 
                        u.user_id, 
                        (u.point + COALESCE(SUM(t.point), 0)) AS total_point
                    FROM 
                        user u
                    LEFT JOIN 
                        user_trophy ut ON u.user_id = ut.user_id
                    LEFT JOIN 
                        trophy t ON ut.trophy_id = t.trophy_id
                    GROUP BY 
                        u.user_id, u.point
                    HAVING 
                        total_point > :total_point
                ) AS higher_ranks
            ";
            $rankStmt = $this->pdo->prepare($rankSql);
            $rankStmt->bindParam(':total_point', $userPointData['total_point'], PDO::PARAM_INT);
            $rankStmt->execute();
            $rankData = $rankStmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'user_point' => $userPointData['user_point'],
                'trophy_point' => $userPointData['trophy_point'],
                'total_point' => $userPointData['total_point'],
                'rank' => $rankData['rank'] ?? '圏外'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'データベースエラーが発生しました。' . $e->getMessage()
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = getDatabaseConnection();
        
        $rankingSystem = new RankingSystem($pdo);
        
        $rankingsResult = $rankingSystem->getRankings();
        
        if (isset($_SESSION['user_id'])) {
            $userRankResult = $rankingSystem->getUserRank($_SESSION['user_id']);
            $userRankResult['name'] = $_SESSION['name'] ?? '不明なユーザー'; 
        } else {
            $userRankResult = [
                'success' => true,
                'user_point' => 0,
                'trophy_point' => 0,
                'total_point' => 0,
                'rank' => 'ゲストモード',
                'name' => 'ゲスト',
            ];
        }

        
        $response = [
            'success' => $rankingsResult['success'] && $userRankResult['success'],
            'rankings' => $rankingsResult['rankings'] ?? [],
            'user_rank' => $userRankResult,
            'name' => $userRankResult['name']
        ];
        
        sendJsonResponse($response);
        
    } catch (Exception $e) {
        sendJsonResponse([
            'success' => false,
            'message' => '処理中にエラーが発生しました。' . $e->getMessage()
        ]);
    }
} else {
    sendJsonResponse([
        'success' => false,
        'message' => '不正なリクエストです。'
    ]);
}
?>