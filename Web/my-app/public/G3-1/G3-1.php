<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once '../../src/common/common_head.php'; ?>
    <title>ログイン</title>
    <style>
        .neon-input {
            position: relative;
            width: 100%;
        }
        .neon-input::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 30px;
            background: linear-gradient(
                45deg,
                #ffd700,
                #ff0000,
                #ffd700,
                #ff0000
            );
            z-index: 0;
            background-size: 400% 400%;
            animation: gradient-border 6s linear infinite;
        }
        .neon-input input {
            position: relative;
            z-index: 1;
        }
        @keyframes gradient-border {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }
    </style>
</head>
<body class="m-0 bg-[url('loginback1.jpeg')] bg-cover bg-center bg-no-repeat h-screen flex items-center justify-center bg-fixed">
    <a href="../G1-1/G1-1.php" 
    class="fixed top-5 right-5 bg-black/80 border-2 border-[#ffd700] px-8 py-3 rounded-[25px] text-[#ffd700] text-xl no-underline transition-all duration-300 backdrop-blur-sm hover:scale-105 hover:shadow-[0_0_20px_rgba(255,215,0,0.4)] hover:bg-black/90">
        戻る
    </a>
    
    <div class="w-[800px] bg-black/85 backdrop-blur-md rounded-[30px] p-12 border-2 border-[#ffd700] shadow-[0_0_30px_rgba(255,215,0,0.3)]">
        <form id="loginForm" class="flex flex-col items-center">
            <h1 class="text-[#ffd700] text-5xl font-bold mb-8 text-shadow-[0_0_10px_rgba(255,215,0,0.5)]">ログイン</h1>
            
            <p class="text-white text-xl mb-10">
                ユーザ名、パスワードをご入力の上、「ログイン」ボタンをクリックしてください。
            </p>

            <div id="errorMessage" class="text-red-500 text-lg mb-6 h-6"></div>

            <div class="w-full max-w-[500px] space-y-8">
                <div class="neon-input">
                    <input type="text" 
                        name="user_name" 
                        placeholder="ユーザ名" 
                        required 
                        class="w-full py-5 px-8 bg-black text-[#ffd700] text-xl rounded-[30px] outline-none placeholder-[#ffd700]/50 transition-all duration-300">
                </div>

                <div class="neon-input">
                    <input type="password" 
                        name="password" 
                        placeholder="パスワード" 
                        required 
                        class="w-full py-5 px-8 bg-black text-[#ffd700] text-xl rounded-[30px] outline-none placeholder-[#ffd700]/50 transition-all duration-300">
                </div>

                <button type="submit"
                        class="w-full py-5 px-8 mt-10 bg-gradient-to-r from-[#ffd700] to-[#ff4500] text-black text-2xl font-bold rounded-[30px] transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(255,215,0,0.5)] hover:from-[#ff4500] hover:to-[#ffd700]">
                    ログイン
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('errorMessage').textContent = '';
            const formData = new FormData(this);
            
            fetch('./login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new TypeError('JSONレスポンスではありません');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = '../G1-1/G1-1.php';
                } else {
                    document.getElementById('errorMessage').textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorMessage').textContent = 'ログイン処理中にエラーが発生しました。';
            });
        });
    </script>
</body>
</html>