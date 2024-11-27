<?php

error_reporting(0);

function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

require '../../src/common/Db_connect.php';
class UserRegistration {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function registerUser($name, $password) {
        if (empty($name) || empty($password)) {
            return [
                'success' => false,
                'message' => '名前とパスワードを入力してください。'
            ];
        }
        
        try {
            if (strlen($name) > 50) {
                return [
                    'success' => false,
                    'message' => 'ユーザー名は50文字以内で入力してください。'
                ];
            }
            
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM user WHERE name = ?');
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'このユーザー名は既に使用されています。'
                ];
            }
            
            if (strlen($password) < 8) {
                return [
                    'success' => false,
                    'message' => 'パスワードは8文字以上で入力してください。'
                ];
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $currentTime = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare(
                'INSERT INTO user (name, pass, point, create_at, update_at) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            
            $result = $stmt->execute([
                $name,
                $hashedPassword,
                500,
                $currentTime,
                $currentTime
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => '登録が完了しました'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => '登録に失敗しました。'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'データベースエラーが発生しました。' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '予期せぬエラーが発生しました。' . $e->getMessage()
            ];
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDatabaseConnection();
        
        $registration = new UserRegistration($pdo);
        
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        
        $result = $registration->registerUser($name, $password);
        
        sendJsonResponse($result);
        
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