using UnityEngine;
using UnityEngine.Video;


[System.Serializable]
public class SpriteEffect
{
    public Sprite sprite; // スプライト
    public AudioClip winAudioClip; // 当たり音声
    public VideoClip specialVideo; // 特別な動画
    public int pointsForSprite; // スプライトごとに付与されるポイント
}

