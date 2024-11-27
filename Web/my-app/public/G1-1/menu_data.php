<?php

error_reporting(0);

function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

require '../../src/common/Db_connect.php';

session_start();

class UserStatusManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getUserStatus() {
        try {
            // ゲストユーザーの場合のデフォルト値
            if (!isset($_SESSION['user_id'])) {
                return [
                    'success' => true,
                    'data' => [
                        'user' => 'ゲスト',
                        'points' => 0,
                        'trophy_count' => 0,
                        'is_guest' => true
                    ]
                ];
            }

            // ユーザーのポイントと実績数を取得
            $sql = "
                SELECT 
                    u.name,
                    u.point,
                    COUNT(DISTINCT ut.trophy_id) as trophy_count
                FROM 
                    user u
                LEFT JOIN 
                    user_trophy ut ON u.user_id = ut.user_id
                WHERE 
                    u.user_id = :user_id
                GROUP BY 
                    u.user_id, u.name, u.point
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                return [
                    'success' => true,
                    'data' => [
                        'user' => $userData['name'],
                        'points' => $userData['point'],
                        'trophy_count' => $userData['trophy_count'],
                        'is_guest' => false
                    ]
                ];
            } else {
                throw new Exception('ユーザーデータが見つかりません。');
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'データベースエラーが発生しました。'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = getDatabaseConnection();
        $statusManager = new UserStatusManager($pdo);
        $result = $statusManager->getUserStatus();
        sendJsonResponse($result);
    } catch (Exception $e) {
        sendJsonResponse([
            'success' => false,
            'message' => '処理中にエラーが発生しました。'
        ]);
    }
}
?>