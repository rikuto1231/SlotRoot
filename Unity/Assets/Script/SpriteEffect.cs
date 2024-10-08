using UnityEngine;
using UnityEngine.Video;


[System.Serializable]
public class SpriteEffect
{
    public Sprite sprite; // 対応するスプライト
    public VideoClip specialVideo; // 特別な動画
    public AudioClip winAudioClip; // 当たり音声クリップ
}
