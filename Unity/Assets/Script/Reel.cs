using System.Collections;
using UnityEngine;

public class Reel : MonoBehaviour
{
    [SerializeField] private float resetPosition = -3.0f; // リールがリセットされる位置
    [SerializeField] private SpriteRenderer spriteRenderer; // 画像を表示するためのSpriteRenderer
    [SerializeField] private Sprite[] sprites; // スプライト配列
    [SerializeField] private Transform reelFlame; // Reel_Flameオブジェクト

    [SerializeField] private float startPositionY = 0.5f; // スタート位置のY座標
    [SerializeField] private float reelSpeed = 150f; // 回転速度（`Time.deltaTime` を使うために正の値に変更）
    [SerializeField] private float reelStep = 0.12f; // ステップサイズ

    // 音声関連
    [SerializeField] private AudioSource stopSound; // 停止時の効果音用のAudioSource
    [SerializeField] private AudioClip stopSoundClip; // 停止時の効果音用のAudioClip

    private bool reelStart = false; // リールが回転しているかどうか
    private int currentSpriteIndex = 0; // 現在のスプライトインデックス
    private Vector2 startPosition;

    private void Awake()
    {
        // スタート位置の設定
        startPosition = new Vector2(transform.position.x, startPositionY);

        if (spriteRenderer == null)
        {
            Debug.LogError("SpriteRenderer がアタッチされていません。");
        }
        if (sprites.Length == 0)
        {
            Debug.LogError("スプライトが設定されていません。");
        }
        if (sprites.Length > 0)
        {
            spriteRenderer.sprite = sprites[0]; // 最初のスプライトを設定
        }

        // AudioSourceが設定されていない場合は追加する
        if (stopSound == null)
        {
            stopSound = gameObject.AddComponent<AudioSource>();
        }
        stopSound.clip = stopSoundClip; // 効果音を設定
    }

    public void StartReel()
    {
        if (sprites.Length == 0)
        {
            Debug.LogError("スプライト配列が空です。リールを開始できません。");
            return;
        }

        reelStart = true;
        StartCoroutine(ReelRotate());
    }

    public void StopReel()
    {
        reelStart = false;

        // 効果音を再生
        if (stopSound != null && stopSoundClip != null)
        {
            stopSound.Play(); // リール停止時に効果音を再生
        }

        // 停止時にReel_Flameの縦中央位置で止まるように調整
        AdjustPositionToReelFlameCenter();
    }

    private IEnumerator ReelRotate()
    {
        while (reelStart)
        {
            // リセット位置に達したらスタート位置に戻す
            if (transform.position.y <= resetPosition)
            {
                transform.position = startPosition;

                // スプライトのインデックスを更新
                currentSpriteIndex = (currentSpriteIndex + 1) % sprites.Length;
                spriteRenderer.sprite = sprites[currentSpriteIndex];
                Debug.Log($"リールのスプライトを {currentSpriteIndex} に更新しました。");
            }

            // フレームレート非依存の回転処理
            transform.position = new Vector2(transform.position.x, transform.position.y - (reelStep * Time.deltaTime * reelSpeed));

            // 次のフレームまで待機
            yield return null; 
        }
    }

    private void AdjustPositionToReelFlameCenter()
    {
        if (reelFlame == null)
        {
            Debug.LogError("Reel_Flameが設定されていません。");
            return;
        }

        // Reel_Flameの中央位置を取得
        float reelFlameCenterY = reelFlame.position.y;

        // リールの位置をReel_Flameの中央位置に合わせる
        transform.position = new Vector2(transform.position.x, reelFlameCenterY);
    }

    public Sprite GetCurrentSprite()
    {
        return spriteRenderer.sprite;
    }
}
