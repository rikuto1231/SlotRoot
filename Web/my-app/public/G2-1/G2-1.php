    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新規登録</title>
  <script src="https://cdn.jsdelivr.net/npm/react@18/umd/react.development.js" crossorigin></script>
  <script src="https://cdn.jsdelivr.net/npm/react-dom@18/umd/react-dom.development.js" crossorigin></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <link rel="stylesheet" href="./G2-1.css">

</head>
<body>
  <div id="app"></div>

  <script>
    const { useState } = React;

    const App = () => {
      const [formData, setFormData] = useState({ user_id: "", password: "" });
      const [message, setMessage] = useState("");

      const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
      };

      const handleSubmit = async (e) => {
        e.preventDefault();

        
        if (!formData.user_id || !formData.password) {
          setMessage("名前とパスワードを入力してください。");
          return;
        }

        try {
          const response = await axios.post("../../src/api/register.php", formData, {
            headers: { "Content-Type": "application/json" },
          });
          setMessage(response.data); /
        } catch (error) {
          setMessage("エラーが発生しました。もう一度お試しください。");
          console.error(error);
        }
      };

      return (
        <div>
          <h1>新規登録</h1>
          <p>名前、パスワードをご入力の上、「新規登録」ボタンをクリックしてください。</p>
          <form onSubmit={handleSubmit}>
            <input
              type="text"
              name="user_id"
              placeholder="名前"
              value={formData.user_id}
              onChange={handleChange}
              required
            />
            <input
              type="password"
              name="password"
              placeholder="パスワード"
              value={formData.password}
              onChange={handleChange}
              required
            />
            <button type="submit">新規登録</button>
          </form>
          {message && <p className="message">{message}</p>}
        </div>
      );
    };

    ReactDOM.createRoot(document.getElementById("app")).render(<App />);
  </script>
</body>
</html>
