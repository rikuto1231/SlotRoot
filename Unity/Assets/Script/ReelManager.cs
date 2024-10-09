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
    private int bonusReelCount = 0; // ボーナス中のスピンカウント
    private bool canRotate = true; // リール回転制御で使う
    private bool isInBonusState = false; // ボーナス状態かどうかのフラグ
    private SpriteEffect effect; // 現在のボーナス対象のスプライト情報
    private bool isBattleResultChecked = false; // 戦闘結果が確認されたか

    private void Start()
    {
        UpdatePointDisplay();
    }

    private void Update()
    {
        // 通常モード時のスピン処理
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && !isInBonusState && !isReelActive)
        {
            StartAllReels();
            rotationCount++;
            CheckForBonusDraw();
        }

        // ボーナスモード時のスピン処理
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && isInBonusState && !isReelActive)
        {
            StartAllReels();
            rotationCount++;
            bonusReelCount++;

            // ボーナス中のスピンごとにポイント付与
            AddPoints(30);

            // 10回転以内にスプライトが揃わなかったら敗北
            if (bonusReelCount >= 10 && !isBattleResultChecked)
            {
                HandleDefeat(); // 敗北処理を呼び出し
            }
        }

        // リールを停止する処理
        if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
        {
            StopCurrentReel();
        }
    }

    // ボーナス抽選を行うメソッド
    private void CheckForBonusDraw()
    {
        foreach (var effect_in in spriteEffects)
        {
            if (Random.value < effect_in.hitProbability)
            {
                effect = effect_in;
                EnterBonusState();
                break;
            }
        }
    }

    // ボーナス状態に入る処理
    private void EnterBonusState()
    {
        if (!isInBonusState)
        {
            isInBonusState = true;
            bonusReelCount = 0; // ボーナススピンカウントをリセット
            videoPlayManager.PlayBonusVideo(effect.specialVideo); // ボーナス開始動画

            // 当たり音声を設定して再生
            winSound.clip = effect.winAudioClip; // 当たり音声を設定
            winSound.Play(); // 当たり音声を再生

            canRotate = false; // 登場動画中は回せない
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += (vp) => {
                canRotate = true; // 動画終了後、リールを回せるように
                PlayBattleVideo();
            }; 
        }
    }

    // 戦闘動画を再生する処理
    private void PlayBattleVideo()
    {
        videoPlayManager.PlayBattleVideo(effect.battleVideo);
    }

    // 勝利動画を再生する処理
    private void PlayVictoryVideo()
    {
        videoPlayManager.PlaySpecialVideo(effect.victoryVideo);
        AddPoints(effect.pointsForSprite); // 勝利時に追加ポイントを付与
        EndBonusState();
    }

    // 敗北処理を行う
    private void HandleDefeat()
    {
        PlayDefeatVideo();
        EndBonusState();
    }

    // 敗北動画を再生する処理
    private void PlayDefeatVideo()
    {
        videoPlayManager.PlaySpecialVideo(effect.defeatVideo);
        EndBonusState();
    }

    // ボーナス状態を終了する処理
    private void EndBonusState()
    {
        isInBonusState = false;
        isBattleResultChecked = false;
        bonusReelCount = 0;
        videoPlayManager.PlaySpecialVideo(null); // 動画をリセット
    }

    // リールを回転させる
    private void StartAllReels()
    {
        foreach (var reel in reels)
        {
            reel.StartReel();
        }
        isReelActive = true;
        currentReelIndex = 0;
    }

    // リールを停止する
    private void StopCurrentReel()
    {
        if (currentReelIndex < reels.Count)
        {
            var reel = reels[currentReelIndex];
            reel.StopReel();
            currentReelIndex++;

            if (currentReelIndex >= reels.Count)
            {
                isReelActive = false;
                CheckIfAllReelsMatch();
            }
        }
    }

    // 全てのリールが一致しているか確認し、ボーナス対象のスプライトが揃った場合のみ勝利動画を再生
    private void CheckIfAllReelsMatch()
    {
        // まず、全てのリールのスプライトが一致しているかを確認
        Sprite firstSprite = reels[0].GetCurrentSprite();
        
        foreach (var reel in reels)
        {
            // いずれかのリールが異なるスプライトならば終了
            if (reel.GetCurrentSprite() != firstSprite)
            {
                Debug.Log("リールが一致していません");
                isBattleResultChecked = false;
                return;
            }
        }

        // 全てのリールが一致している場合、特定のスプライト（effectで定義されるもの）が揃ったかをチェック
        if (firstSprite == effect.sprite) // `effect.spriteImage` でボーナス対象のスプライトを指定
        {
            Debug.Log("特定のスプライトが揃いました！");
            isBattleResultChecked = true;
            PlayVictoryVideo(); // 勝利動画を再生
        }
        else
        {
            Debug.Log("一致しましたが、勝利対象のスプライトではありません");
            isBattleResultChecked = false;
        }
    }

    // ポイント加算
    private void AddPoints(int pointsToAdd)
    {
        playerPoints += pointsToAdd;
        UpdatePointDisplay();
    }

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
}
