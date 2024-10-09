using UnityEngine;
using UnityEngine.Video;

[System.Serializable]
public class SpriteEffect
{
    public Sprite sprite;                // スプライト
    public AudioClip winAudioClip;        // 当たり音声
    public VideoClip specialVideo;        // 登場動画
    public int pointsForSprite;           // スプライトごとに付与されるポイント

    public bool isBonusSprite;            // このスプライトがボーナス状態に関連するか
    public VideoClip battleVideo;         // 戦闘中動画
    public VideoClip victoryVideo;        // 勝利動画
    public VideoClip defeatVideo;         // 敗北動画

        [Range(0f, 1f)] // スライダーで調整可能
    public float hitProbability; // 当たる確率（0.0～1.0）

    // コンストラクタで初期化を行う（必要に応じて省略可能）
    public SpriteEffect(Sprite sprite, AudioClip winAudio, VideoClip specialVid, int points, 
                        bool isBonus, VideoClip battleVid, VideoClip victoryVid, VideoClip defeatVid)
    {
        this.sprite = sprite;
        this.winAudioClip = winAudio;
        this.specialVideo = specialVid;
        this.pointsForSprite = points;
        this.isBonusSprite = isBonus;
        this.battleVideo = battleVid;
        this.victoryVideo = victoryVid;
        this.defeatVideo = defeatVid;
    }

    // ボーナス状態かどうかを判定するメソッド
    public bool IsBonus()
    {
        return isBonusSprite;
    }
}
