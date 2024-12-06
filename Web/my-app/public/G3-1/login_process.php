<?php

error_reporting(0);

function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

require '../../src/common/Db_connect.php';

session_start();

class UserLogin {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($user_name, $password) {
        if (empty($user_name) || empty($password)) {
            return [
                'success' => false,
                'message' => 'ユーザ名とパスワードを入力してください。'
            ];
        }
        
        try {
            $sql = "SELECT user_id, name, pass, point FROM user WHERE name = :user_name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['pass'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['point'] = $user['point'];
                
                return [
                    'success' => true,
                    'message' => 'ログイン成功'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'ユーザ名またはパスワードが正しくありません。'
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
        
        $login = new UserLogin($pdo);
        
        $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        
        $result = $login->login($user_name, $password);
        
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