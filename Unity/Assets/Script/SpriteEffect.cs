using UnityEngine;
using UnityEngine.Video;

[System.Serializable]
public class SpriteEffect
{
    public Sprite sprite;                // スプライト
    public AudioClip specialAudioClip;   // 登場音声
    public VideoClip specialVideo;        // 登場動画
    public int pointsForSprite;           // スプライトごとに付与されるポイント

    public bool isBonusSprite;            // このスプライトがボーナス状態に関連するか
    public VideoClip battleVideo;         // 戦闘中動画
    public VideoClip victoryVideo;        // 勝利動画
    public VideoClip defeatVideo;         // 敗北動画

    [Range(0f, 1f)] // スライダーで調整可能
    public float hitProbability;           // 当たる確率（0.0～1.0）

    // 新しく追加されたプロパティ
    public AudioClip victoryAudioClip;    // 勝利時音声
    public AudioClip defeatAudioClip;      // 敗北時音声
    public bool allowsReplay; // このスプライトがリプレイを許可するか


public SpriteEffect(Sprite sprite, AudioClip winAudio, VideoClip specialVid, int points, 
                    bool isBonus, VideoClip battleVid, VideoClip victoryVid, VideoClip defeatVid, 
                    AudioClip victoryAudio, AudioClip defeatAudio, bool allowsReplay)
{
    this.sprite = sprite;
    this.specialAudioClip = winAudio;
    this.specialVideo = specialVid;
    this.pointsForSprite = points;
    this.isBonusSprite = isBonus;
    this.battleVideo = battleVid;
    this.victoryVideo = victoryVid;
    this.defeatVideo = defeatVid;
    this.victoryAudioClip = victoryAudio; // 勝利時音声
    this.defeatAudioClip = defeatAudio;   // 敗北時音声
    this.allowsReplay = allowsReplay;      // リプレイを許可するか
}


    // ボーナス状態かどうかを判定するメソッド
    public bool IsBonus()
    {
        return isBonusSprite;
    }
}
