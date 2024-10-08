using UnityEngine;
using UnityEngine.Video;

public class Murakami_move : MonoBehaviour
{
    [SerializeField] private VideoPlayer videoPlayer; // 現在のビデオプレイヤー
    [SerializeField] private VideoClip defaultClip;   // デフォルトの動画
    [SerializeField] private VideoClip[] specialClips; // 特別な動画を配列で管理

    private void Awake()
    {
        if (videoPlayer == null)
        {
            videoPlayer = GetComponent<VideoPlayer>(); // 自動でビデオプレイヤーを取得
        }

        videoPlayer.isLooping = true;
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の音声をミュート
        videoPlayer.clip = defaultClip; // デフォルトの動画をセット
        videoPlayer.Play(); // デフォルトの動画を再生
    }

    // 特別な動画をスプライトに対応して再生するメソッド
    public void PlaySpecialVideo(VideoClip specialClip)
    {
        if (videoPlayer != null && specialClip != null)
        {
            videoPlayer.isLooping = false; // 特別な動画はループしない
            videoPlayer.clip = specialClip; // 指定された動画をセット
            videoPlayer.Play(); // 特別な動画を再生

            // 動画が終わった時に元の動画に戻すイベントを設定
            videoPlayer.loopPointReached += OnSpecialVideoEnd;
        }
    }

    // 特別な動画が終わったときに呼ばれる
    private void OnSpecialVideoEnd(VideoPlayer vp)
    {
        videoPlayer.clip = defaultClip; // デフォルトの動画に戻す
        videoPlayer.isLooping = true;   // デフォルト動画はループするように設定
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の音声をミュート
        videoPlayer.Play();             // デフォルトの動画を再生

        // イベントリスナーを解除
        videoPlayer.loopPointReached -= OnSpecialVideoEnd;
    }
}
