using UnityEngine;
using TMPro; // TextMeshPro用の名前空間
using System.Collections.Generic;

public class ReelManager : MonoBehaviour
{
    [SerializeField] private List<Reel> reels; // リールのリスト
    [SerializeField] private Murakami_move murakamiMove; // Murakami_moveスクリプトを持つオブジェクト
    [SerializeField] private AudioSource winSound; // 当たり音声を再生するためのAudioSource
    [SerializeField] private TextMeshProUGUI pointText; // ポイントを表示するTextMeshProUGUI
    [SerializeField] private OuterFrameEffect outerFrameEffect; // OuterFrameのエフェクト
    [SerializeField] private List<SpriteEffect> spriteEffects; // スプライトごとの演出リスト

    private int currentReelIndex = 0; // 現在のリールインデックス
    private bool isReelActive = false; // リールのアクティブ状態
    private int playerPoints = 0; // プレイヤーのポイント（初期値は0）

    private void Start()
    {
        // ポイント表示を初期化（"0P" 表示）
        UpdatePointDisplay();
    }

    private void Update()
    {
        if (Input.GetKeyDown(KeyCode.Space))
        {
            if (!isReelActive)
            {
                StartAllReels();
            }
        }

        if (Input.GetKeyDown(KeyCode.Return))
        {
            if (isReelActive)
            {
                StopCurrentReel();
            }
        }

        if (Input.GetKeyDown(KeyCode.F))
        {
            CheckIfAllReelsMatch();
        }
    }

    private void StartAllReels()
    {
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
        currentReelIndex = 0;  // スタート時にインデックスをリセット
    }

    private void StopCurrentReel()
    {
        if (currentReelIndex < reels.Count)
        {
            Debug.Log($"リール {currentReelIndex} を停止します。");
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

    private void CheckIfAllReelsMatch()
    {
        if (reels.Count == 0) return;

        Sprite firstSprite = reels[0].GetCurrentSprite(); // 最初のリールのスプライトを基準にする
        Debug.Log($"基準スプライト: {firstSprite.name}");

        foreach (var reel in reels)
        {
            var currentSprite = reel.GetCurrentSprite();
            Debug.Log($"リールのスプライト: {currentSprite.name}");

            if (currentSprite != firstSprite)
            {
                Debug.Log("リールが一致しません！");
                return; // 一致しない場合はここでメソッドを終了
            }
        }

        Debug.Log("リールが一致しました！");

        // スプライトごとに演出を処理
        HandleWinEffects(firstSprite);
    }

    // スプライトごとの演出を処理
    private void HandleWinEffects(Sprite matchingSprite)
    {
        foreach (var effect in spriteEffects) // スプライトごとの演出リストをチェック
        {
            if (effect.sprite == matchingSprite) // 揃ったスプライトに対応するエフェクトか確認
            {
                // 動画を切り替える（murakamiMoveを使用）
                if (murakamiMove != null && effect.specialVideo != null)
                {
                    Debug.Log($"リールが揃いました: {matchingSprite.name}");
                    murakamiMove.PlaySpecialVideo(effect.specialVideo); // スプライトに対応する特別な動画を再生
                }

                // 当たり音声を再生
                if (effect.winAudioClip != null)
                {
                    AudioSource.PlayClipAtPoint(effect.winAudioClip, Camera.main.transform.position);
                }

                if (winSound != null)
                {
                    winSound.Play(); // 当たり音声を再生
                }

                if (outerFrameEffect != null)
                {
                    outerFrameEffect.StartBlinking(); // OuterFrameの点滅を開始
                }

                break; // 演出が終わったのでループを終了
            }
        }
    }

    // ポイントを追加するメソッド（消さずに残しておく）
    private void AddPoints(int pointsToAdd)
    {
        playerPoints += pointsToAdd;
        UpdatePointDisplay(); // ポイント表示を更新
    }

    // ポイント表示を更新するメソッド
    private void UpdatePointDisplay()
    {
        if (pointText != null)
        {
            pointText.text = playerPoints + "P"; // 例: "100P" のように表示
        }
    }

    public void ResetPoints()
    {
        playerPoints = 0; // ポイントを0にリセット
        UpdatePointDisplay(); // UIを更新して0ポイントを表示
        Debug.Log("ポイントがリセットされました。");
    }
}
