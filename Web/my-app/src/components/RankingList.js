import React, { useEffect, useState } from 'react';

const RankingList = () => {
    const [rankings, setRankings] = useState([]);

    useEffect(() => {
        const fetchRankings = async () => {
            try {
                const response = await fetch('/api/get_rankings.php'); // APIのパスを指定
                if (!response.ok) {
                    throw new Error('ネットワークエラー');
                }
                const data = await response.json();
                console.log('Rankings fetched:', data); // デバッグログ
                setRankings(data);
            } catch (error) {
                console.error('データの取得に失敗しました:', error);
            }
        };

        fetchRankings();
    }, []);

    const handleUserClick = (userId) => {
        fetch('/api/set_user_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${encodeURIComponent(userId)}`,
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/user_profile'; // プロフィールページにリダイレクト
            } else {
                console.error('エラーが発生しました');
            }
        })
        .catch(error => {
            console.error('ユーザセッションの設定に失敗しました:', error);
        });
    };

    return (
        <div>
            <h1>ランキング</h1>
            <table>
                <thead>
                    <tr>
                        <th>順位</th>
                        <th>名前</th>
                        <th>ポイント</th>
                    </tr>
                </thead>
                <tbody>
                    {rankings.map((ranking, index) => (
                        <tr key={ranking.user_id}>
                            <td>{index + 1}</td>
                            <td>
                                <a href="#" onClick={() => handleUserClick(ranking.user_id)}>
                                    {ranking.name}
                                </a>
                            </td>
                            <td>{ranking.point}P</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default RankingList;
