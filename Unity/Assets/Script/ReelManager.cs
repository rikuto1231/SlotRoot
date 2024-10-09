using UnityEngine;
using TMPro; // TextMeshPro用の名前空間
using UnityEngine.Video; // Video名前空間
using System.Collections.Generic;

public class ReelManager : MonoBehaviour
{
    [SerializeField] private List<Reel> reels; // リールのリスト
    [SerializeField] private VideoPlaybackController videoPlayManager; // 動画系を操作するときのマネジメントクラス
    [SerializeField] private AudioSource winSound; // 当たり音声を再生するためのAudioSource。
    [SerializeField] private TextMeshProUGUI pointText; // ポイントを表示するTextMeshProUGUI
    [SerializeField] private OuterFrameEffect outerFrameEffect; // OuterFrameのエフェクト
    [SerializeField] private List<SpriteEffect> spriteEffects; // スプライトごとの演出リスト

    private int currentReelIndex = 0; // 現在のリールインデックス
    private bool isReelActive = false; // リールのアクティブ状態
    private int playerPoints = 0; // プレイヤーのポイント（初期値は0）
    private int rotationCount = 0; // 回転数を管理する変数
    private int currentSpinCount = 0; // 現在のスピンカウント
    private bool canRotate = true; // リール回転制御で使う
    private bool isInBonusState = false; // ボーナス状態かどうかのフラグ
    private bool IsBattleWon = false; // 勝利フラグ
    private bool IsBattleLost = false; // 敗北フラグ

// 初期ポイントの取得と表示。>>>完成 >>>後ほどDB接続取得形式に変更
    private void Start()
    {
        // ポイント表示を初期化（"0P" 表示）
        UpdatePointDisplay();
    }


    private void Update()
    {
        // ボーナス状態じゃないときのリール回転スタート
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && !isInBonusState) // ボーナス状態でない場合
        {
            Debug.Log("ボーナス状態じゃないときのリール回転スタート");
            StartAllReels();
            rotationCount++; // 総回転数を記録

            // 抽選処理を行う
            CheckForBonusDraw();
        }

        // Enterキーが押されたら現在のリールを停止
        if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
        {
            // リールを1ずつ停止。
            StopCurrentReel();
        }
    }


    private void CheckForBonusDraw()
    {
        // スプライトごとに設定された確率で抽選
        foreach (var effect in spriteEffects)
        {
            // 確率チェック（0.0〜1.0の範囲で比較）
            if (Random.value < effect.hitProbability)
            {
                Debug.Log("抽選成功 >>> ボーナスメソッドに移行");
                EnterBonusState(effect); // ボーナス状態に入る
                break; // ボーナス状態に入ったらループを抜ける
            }
        }
    }

    // スロットがボーナスに入るときに呼ばれるメソッド
    private void EnterBonusState(SpriteEffect effect)
    {
        
        if (!isInBonusState)
        {
            // このフラグはボーナス終了時にfalseに変更予定
            isInBonusState = true;

            // マネージャで登場動画を再生
            videoPlayManager.PlayBonusVideo(effect.specialVideo);
            Debug.Log("登場動画を再生");

            // ここはリール停止状態管理メソッド呼び出し必要かも
            ToggleReelRotation();



            // ボーナス動画が終了したあとの戦闘動画再生メソッドの呼び出し
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += (vp) => OnBonusVideoEnd(effect);
        }
    }

    // private void OnBonusVideoEnd(SpriteEffect effect)
    // {
    //     // ボーナス動画が終わった後の処理
    // }


// 戦闘時の動画を再生する処理
    private void PlayBattleVideo(SpriteEffect effect)
    {
        // 戦闘時の動画を流すために動画管理クラスのループタイプメソッド呼び出し

        // 
    }

    private void StartBattleRotations(SpriteEffect effect)
    {
        // 10回転分の処理を行う
    }

    private void CheckBattleResults(SpriteEffect effect)
    {
        // スプライトをチェックしてポイントを付与する処理
    }

    private void PlayVictoryVideo(SpriteEffect effect)
    {
        // 勝利時の動画を流す処理
    }

    private void PlayDefeatVideo(SpriteEffect effect)
    {
        // 敗北時の動画を流す処理
    }

    private void StartAllReels()
    {
        // 回転が許可されている場合のみリールを開始
    }


    // 現在のリールを止める。>>> 通常状態停止（完成）
    private void StopCurrentReel()
    {
        if (currentReelIndex < reels.Count)
        {
            var reel = reels[currentReelIndex];
            if (reel != null)
            {
                reel.StopReel();
                currentReelIndex++;
            }
            else
            {
                Debug.LogError($"リール {currentReelIndex} が `null` です。");
            }

            if (currentReelIndex >= reels.Count)
            {
                isReelActive = false;
                Debug.Log("全てのリールが停止しました。");
                CheckIfAllReelsMatch();
            }
        }
    }


    //対応したeffectのpointsForSpriteを加算 >>> 完成。SpriteEffect変更注意
    private void AddPoints(SpriteEffect effect)
    {
        playerPoints += effect.pointsForSprite; // スプライトに設定されたポイントを付与
        UpdatePointDisplay(); // ポイント表示を更新
    }

    // ポイント表示を更新するメソッド >>> AddPointsから送られるだけなので完成
    private void UpdatePointDisplay()
    {
        if (pointText != null)
        {
            pointText.text = playerPoints + "P";
        }
    }

    public void ResetPoints()
    {
        // ポイントをリセットする処理
    }

    // リール回転許可の制御切り替えメソッド
    public void ToggleReelRotation()
    {
        canRotate = !canRotate; // 回転の状態を反転
        string state = canRotate ? "許可されました。" : "禁止されました。";
        Debug.Log($"リールの回転が{state}");
    }

    private void StopAllReels()
    {
        // すべてのリールを停止する処理
    }

    private void CheckIfAllReelsMatch()
    {
        // すべてのリールのスプライトが一致するか確認する処理
    }
}
