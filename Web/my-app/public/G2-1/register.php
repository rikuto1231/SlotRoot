<?php
require '../../src/common/Db_connect.php';


header('Content-Type: application/json');

class UserRegistration {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function registerUser($name, $password) {
        try {
            // 入力値の検証
            if (empty($name) || empty($password)) {
                throw new Exception('すべての項目を入力してください。');
            }
            
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE name = ?');
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('このユーザー名は既に使用されています。');
            }
            
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $currentTime = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (name, pass, point, create_at, update_at) 
                VALUES (?, ?, ?, ?, ?)'
            );
            
            $result = $stmt->execute([
                $name,
                $hashedPassword,
                0, // 初期ポイント
                $currentTime,
                $currentTime
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => '登録が完了しました'
                ];
            } else {
                throw new Exception('登録に失敗しました。');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDatabaseConnection();
        $registration = new UserRegistration($pdo);
        
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        
        $result = $registration->registerUser($name, $password);
        echo json_encode($result);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>