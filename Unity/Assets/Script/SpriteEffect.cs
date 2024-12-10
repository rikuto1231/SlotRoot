using UnityEngine;

[System.Serializable]
public class SpriteEffect
{
    public Sprite sprite;                // スロットのリールに表示されるスプライト
    public Sprite bonusDisplaySprite;    // ボーナス中に表示される専用の画像
    public AudioClip specialAudioClip;   // 登場音声
    public string specialVideoPath;      // 登場動画のパス（例：special.mp4）
    public int pointsForSprite;          // スプライトごとに付与されるポイント

    public bool isBonusSprite;           // このスプライトがボーナス状態に関連するか
    public string battleVideoPath;       // 戦闘中動画のパス（例：battle.mp4）
    public string victoryVideoPath;      // 勝利動画のパス（例：victory.mp4）
    public string defeatVideoPath;       // 敗北動画のパス（例：defeat.mp4）

    [Range(0f, 1f)]                     // スライダーで調整可能
    public float hitProbability;         // 当たる確率（0.0～1.0）

    public AudioClip victoryAudioClip;   // 勝利時音声
    public AudioClip defeatAudioClip;    // 敗北時音声
    public bool allowsReplay;            // このスプライトがリプレイを許可するか

    // コンストラクタ
    public SpriteEffect(Sprite sprite, Sprite bonusSprite, AudioClip winAudio, string specialVidPath, int points, 
                       bool isBonus, string battleVidPath, string victoryVidPath, string defeatVidPath, 
                       AudioClip victoryAudio, AudioClip defeatAudio, bool allowsReplay)
    {
        this.sprite = sprite;
        this.bonusDisplaySprite = bonusSprite;
        this.specialAudioClip = winAudio;
        this.specialVideoPath = specialVidPath;
        this.pointsForSprite = points;
        this.isBonusSprite = isBonus;
        this.battleVideoPath = battleVidPath;
        this.victoryVideoPath = victoryVidPath;
        this.defeatVideoPath = defeatVidPath;
        this.victoryAudioClip = victoryAudio;
        this.defeatAudioClip = defeatAudio;
        this.allowsReplay = allowsReplay;
    }

    // ボーナス状態かどうかを判定するメソッド
    public bool IsBonus()
    {
        return isBonusSprite;
    }
}