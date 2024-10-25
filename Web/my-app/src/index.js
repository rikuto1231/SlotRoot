import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// パフォーマンス測定の関数
// 例: reportWebVitals(console.log)
// 分析エンドポイントに送信可能: https://bit.ly/CRA-vitals
reportWebVitals();
