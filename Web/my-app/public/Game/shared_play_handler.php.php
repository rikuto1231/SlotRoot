<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'database.php';

class RoomManager {
    private $roomsFile = 'shared_rooms.json';
    private $lockFile = 'shared_rooms.lock';
    private $pdo;

    public function __construct() {
        $this->pdo = getDatabaseConnection();
        if (!file_exists($this->roomsFile)) {
            file_put_contents($this->roomsFile, json_encode([]));
        }
    }

    private function acquireLock() {
        $lockFp = fopen($this->lockFile, 'w+');
        $timeout = 10; // 10秒でタイムアウト
        $start = time();
        
        while (!flock($lockFp, LOCK_EX | LOCK_NB)) {
            if (time() - $start > $timeout) {
                fclose($lockFp);
                throw new Exception('Lock timeout');
            }
            usleep(100000); // 0.1秒待機
        }
        
        return $lockFp;
    }

    private function releaseLock($lockFp) {
        flock($lockFp, LOCK_UN);
        fclose($lockFp);
    }

    public function createRoom($password, $userId, $points) {
        // ポイントの検証
        if (!$this->validatePoints($userId, $points)) {
            return ['status' => 'error', 'message' => 'Insufficient points'];
        }
    
        $lockFp = $this->acquireLock();
        
        try {
            $rooms = $this->getRooms();
            
            // 既存のルームをクリーンアップ
            $this->cleanupOldRooms($rooms);
            
            if (isset($rooms[$password])) {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'Room already exists'];
            }
    
            // ユーザー名とポイントを取得
            $stmt = $this->pdo->prepare("SELECT name, point FROM user WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'User not found'];
            }
    
            // ポイントを消費
            $updatedPoints = $user['point'] - $points;
            $updateStmt = $this->pdo->prepare("UPDATE user SET point = ? WHERE user_id = ?");
            $updateStmt->execute([$updatedPoints, $userId]);
    
            // セッションを更新
            $_SESSION['user_point'] = $updatedPoints;
    
            $rooms[$password] = [
                'host' => [
                    'userId' => $userId,
                    'userName' => $user['name'],
                    'points' => $points
                ],
                'created_at' => time(),
                'status' => 'waiting'
            ];
    
            $this->saveRooms($rooms);
            $this->releaseLock($lockFp);
            
            return [
                'status' => 'waiting',
                'message' => 'Room created',
                'remaining_points' => $updatedPoints
            ];
        } catch (Exception $e) {
            $this->releaseLock($lockFp);
            throw $e;
        }
    }

    public function joinRoom($password, $userId, $points) {
        // ポイントの検証
        if (!$this->validatePoints($userId, $points)) {
            return ['status' => 'error', 'message' => 'Insufficient points'];
        }
    
        $lockFp = $this->acquireLock();
        
        try {
            $rooms = $this->getRooms();
            
            if (!isset($rooms[$password])) {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'Room not found'];
            }
    
            if ($rooms[$password]['status'] !== 'waiting') {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'Room is not available'];
            }
    
            // ユーザー名とポイントを取得
            $stmt = $this->pdo->prepare("SELECT name, point FROM user WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'User not found'];
            }
    
            // ポイントを消費
            $updatedPoints = $user['point'] - $points;
            $updateStmt = $this->pdo->prepare("UPDATE user SET point = ? WHERE user_id = ?");
            $updateStmt->execute([$updatedPoints, $userId]);
    
            // セッションを更新
            $_SESSION['user_point'] = $updatedPoints;
    
            $rooms[$password]['guest'] = [
                'userId' => $userId,
                'userName' => $user['name'],
                'points' => $points
            ];
            $rooms[$password]['status'] = 'matched';
            $rooms[$password]['total_points'] = $points + $rooms[$password]['host']['points'];
            $rooms[$password]['matched_at'] = time();
    
            $this->saveRooms($rooms);
            $this->releaseLock($lockFp);
            
            return [
                'status' => 'matched',
                'total_points' => $rooms[$password]['total_points'],
                'remaining_points' => $updatedPoints
            ];
        } catch (Exception $e) {
            $this->releaseLock($lockFp);
            throw $e;
        }
    }

    public function checkRoom($password) {
        $rooms = $this->getRooms();
        
        if (!isset($rooms[$password])) {
            return ['status' => 'error', 'message' => 'Room not found'];
        }

        if ($rooms[$password]['status'] === 'matched') {
            return [
                'status' => 'matched',
                'total_points' => $rooms[$password]['total_points']
            ];
        }

        return ['status' => 'waiting'];
    }

    public function updatePoints($password, $points) {
        $lockFp = $this->acquireLock();
        
        try {
            $rooms = $this->getRooms();
            
            if (!isset($rooms[$password])) {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'Room not found'];
            }

            if ($rooms[$password]['status'] !== 'matched') {
                $this->releaseLock($lockFp);
                return ['status' => 'error', 'message' => 'Room is not in matched state'];
            }

            // 合計ポイントを更新
            $rooms[$password]['total_points'] = $points;
            $this->saveRooms($rooms);
            $this->releaseLock($lockFp);
            
            return [
                'status' => 'success',
                'total_points' => $points
            ];
        } catch (Exception $e) {
            $this->releaseLock($lockFp);
            throw $e;
        }
    }

    private function validatePoints($userId, $points) {
        $stmt = $this->pdo->prepare("SELECT point FROM user WHERE user_id = ?");
        $stmt->execute([$userId]);
        $currentPoints = $stmt->fetchColumn();
        
        return $points <= $currentPoints && $points >= 100 && $points % 100 === 0;
    }

    private function cleanupOldRooms(&$rooms) {
        $currentTime = time();
        foreach ($rooms as $password => $room) {
            // マッチング待ち状態で5分以上経過
            if ($room['status'] === 'waiting' && ($currentTime - $room['created_at']) > 300) {
                unset($rooms[$password]);
            }
            // マッチング済みで30分以上経過
            elseif ($room['status'] === 'matched' && isset($room['matched_at']) && ($currentTime - $room['matched_at']) > 1800) {
                unset($rooms[$password]);
            }
        }
    }

    private function getRooms() {
        $content = file_get_contents($this->roomsFile);
        return json_decode($content, true) ?? [];
    }

    private function saveRooms($rooms) {
        file_put_contents($this->roomsFile, json_encode($rooms));
    }
}

// リクエスト処理
try {
    $roomManager = new RoomManager();
    $action = $_GET['action'] ?? '';
    
    // デバッグログを追加
    error_log("Received action: " . $action);
    error_log("Raw input: " . file_get_contents('php://input'));
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // デバッグログを追加
    error_log("Decoded data: " . print_r($data, true));

    switch ($action) {
        case 'create':
            $result = $roomManager->createRoom(
                $data['password'],
                $_SESSION['user_id'],
                $data['points']
            );
            echo json_encode($result);
            break;

        case 'join':
            $result = $roomManager->joinRoom(
                $data['password'],
                $_SESSION['user_id'],
                $data['points']
            );
            echo json_encode($result);
            break;

        case 'check':
            $result = $roomManager->checkRoom($data['password']);
            echo json_encode($result);
            break;

        case 'update':
            $result = $roomManager->updatePoints(
                $data['password'],
                $data['points']
            );
            echo json_encode($result);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Error in shared_play_handler: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}