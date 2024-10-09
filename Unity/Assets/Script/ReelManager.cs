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
    private int bonusReelCount = 0; // 現在のスピンカウント
    private bool canRotate = true; // リール回転制御で使う
    private bool isInBonusState = false; // ボーナス状態かどうかのフラグ
    private bool IsBattleWon = false; // 勝利フラグ
    private bool IsBattleLost = false; // 敗北フラグ
    private SpriteEffect effect; // クラス変数としてのeffect

    // 初期ポイントの取得と表示。>>>完成 >>>後ほどDB接続取得形式に変更
    private void Start()
    {
        // 現状0リセット >>> 後でDB取得
        UpdatePointDisplay();
    }

    private void Update()
    {
        // スペース＆リール禁止されてない＆ボーナス状態じゃない＆リールがアクティブじゃない
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && !isInBonusState && !isReelActive) // ボーナス状態でない場合
        {
            Debug.Log("ボーナス状態じゃないときのリール回転スタート");
            StartAllReels();
            rotationCount++; // 総回転数を記録

            // 抽選処理を行う
            CheckForBonusDraw();
        }

        // スペース＆リール禁止されてない＆ボーナス状態
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && isInBonusState && !isReelActive) // ボーナス状態でない場合
        {
            Debug.Log("ボーナス状態の時のリール回転スタート");
            StartAllReels();
            rotationCount++; // 総回転数を記録
            bonusReelCount++;// ボーナス中の10回転までを管理

            // ポイントを付与する（ボーナスなら毎回ここを通る）
            AddPoints(30);
            
            // 10回転を超えたらボーナスフラグの変更
            if (bonusReelCount <= 10)
            {
                isInBonusState = false;
            }
        }

        // Enterキーが押されたら現在のリールを停止
        if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
        {
            // リールを1ずつ停止。CheckIfAllReelsMatchがこの中でチェックを行う
            StopCurrentReel();
        }
    }

    private void CheckForBonusDraw()
    {
        // スプライトごとに設定された確率で抽選
        foreach (var effect_in in spriteEffects)
        {
            // 確率チェック（0.0〜1.0の範囲で比較）
            if (Random.value < effect_in.hitProbability)
            {
                Debug.Log("CheckForBonusDraw:抽選成功 >>> ボーナスメソッドに移行");
                effect = effect_in; // クラス変数に設定
                EnterBonusState(); // ボーナス状態に入る
                break;
            }
        }
    }

    // スロットがボーナスに入るときに呼ばれるメソッド
    private void EnterBonusState()
    {
        Debug.Log("EnterBonusState: マネージャ登場動画再生前");

        if (!isInBonusState)
        {
            isInBonusState = true;
            Debug.Log("EnterBonusState: マネージャ登場動画再生中");

            // マネージャで登場動画を再生
            videoPlayManager.PlayBonusVideo(effect.specialVideo);
            Debug.Log("登場動画を再生");

            // リール停止状態管理メソッド呼び出し
            ToggleReelRotation();

            // ボーナス動画が終了した後、戦闘動画再生メソッドの呼び出し
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += (vp) => PlayBattleVideo();
        }
    }

    // 戦闘時の動画を再生する処理
    private void PlayBattleVideo()
    {
        // 戦闘時の動画を再生
        videoPlayManager.PlayBattleVideo(effect.battleVideo);
        ToggleReelRotation();

    }

    private void CheckBattleResults()
    {
        // スプライトをチェックしてポイントを付与する処理
    }

    private void PlayVictoryVideo()
    {
        // 勝利時の動画を再生する処理
    }

    private void PlayDefeatVideo()
    {
        // 敗北時の動画を再生する処理
    }

    private void StartAllReels()
    {
        // 回転が許可されている場合のみリールを開始
        Debug.Log("全てのリールを開始します。");
        foreach (var reel in reels)
        {
            if (reel != null)
            {
                reel.StartReel();
            }
            else
            {
                Debug.LogError("リールが `null` です。");
            }
        }
        isReelActive = true;
        currentReelIndex = 0; // スタート時にインデックスをリセット
    }

    // 現在のリールを止める
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

    // ボーナス毎回転加算用
    private void AddPoints(int pointsToAdd)
    {
        playerPoints += pointsToAdd; // 指定された数値を加算
        UpdatePointDisplay(); // ポイント表示を更新
    }

    // ボーナス勝利時のスプライト設定ポイント加算
    private void AddPoints()
    {
        playerPoints += effect.pointsForSprite; // スプライトに設定されたポイントを付与
        UpdatePointDisplay(); // ポイント表示を更新
    }


    // ポイント表示を更新するメソッド
    private void UpdatePointDisplay()
    {
        if (pointText != null)
        {
            pointText.text = playerPoints + "P";
        }
    }

    public void ResetPoints()
    {
        playerPoints = 0; // ポイントを0にリセット
        UpdatePointDisplay(); // UIを更新して0ポイントを表示
        Debug.Log("ポイントがリセットされました。");
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

    // リール停止後の一致チェックメソッド
    private void CheckIfAllReelsMatch()
    {
        if (reels.Count == 0) return;

        Sprite firstSprite = reels[0].GetCurrentSprite(); // 最初のリールのスプライトを基準にする
        Debug.Log($"基準スプライト: {firstSprite.name}");

        foreach (var reel in reels)
        {
            var currentSprite = reel.GetCurrentSprite();

            if (currentSprite != firstSprite)
            {
                Debug.Log("一致しません。");
                return; // 一致しない場合は処理を終了
            }
        }

        Debug.Log("全てのリールが一致しました！");
    }
}
