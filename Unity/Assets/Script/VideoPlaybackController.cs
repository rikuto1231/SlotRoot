using UnityEngine;
using UnityEngine.Video;

public class VideoPlaybackController : MonoBehaviour
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
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の映像の音声をミュート
        videoPlayer.clip = defaultClip; // デフォルトの動画をセット
        videoPlayer.Play(); // デフォルトの動画を再生
    }


        // 登場動画の管理
    private bool isVictoryVideoPlaying = false; // 勝利動画が再生中かどうかを示すフラグ
        // 勝利動画が流れているかどうかを確認するためのプロパティ
    public bool IsVictoryVideoPlaying => isVictoryVideoPlaying; 

    // 特別な動画をスプライトに対応して再生するメソッド
    public void PlaySpecialVideo(VideoClip specialClip)
    {
        if (videoPlayer != null && specialClip != null)
        {
            isVictoryVideoPlaying = true; // 勝利動画が再生中に設定
            videoPlayer.isLooping = false; // 特別な動画はループしない
            videoPlayer.clip = specialClip; // 指定された動画をセット
            videoPlayer.Play(); // 特別な動画を再生

            // 動画が終わった時に元の動画に戻すイベントを設定
            videoPlayer.loopPointReached += OnSpecialVideoEnd;
        }
    }

    // ボーナス動画の管理
    public void PlayBonusVideo(VideoClip bonusClip)
    {
        if (videoPlayer != null && bonusClip != null)
        {
            isVictoryVideoPlaying = true; // 勝利動画が再生中に設定
            videoPlayer.isLooping = false; // ループなし
            videoPlayer.clip = bonusClip;  // 指定されたボーナス動画をセット
            videoPlayer.Play();            // ボーナス動画を再生

            // ボーナス動画が終了したらデフォルトに戻す
            videoPlayer.loopPointReached += OnBonusVideoEnd;
        }
    }

    // ボーナス動画が終了した時に呼ばれる
    private void OnBonusVideoEnd(VideoPlayer vp)
    {
        isVictoryVideoPlaying = false; // 勝利動画が終了したのでフラグをリセット
        videoPlayer.clip = defaultClip; // デフォルトの動画に戻す
        videoPlayer.isLooping = true;   // デフォルト動画はループするように設定
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の映像の音声をミュート
        videoPlayer.Play();             // デフォルトの動画を再生

        // イベントリスナーを解除
        videoPlayer.loopPointReached -= OnBonusVideoEnd;
    }

    // 特別な動画が終わったときに呼ばれる
    private void OnSpecialVideoEnd(VideoPlayer vp)
    {
        isVictoryVideoPlaying = false; // 勝利動画が終了したのでフラグをリセット
        videoPlayer.clip = defaultClip; // デフォルトの動画に戻す
        videoPlayer.isLooping = true;   // デフォルト動画はループするように設定
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の映像の音声をミュート
        videoPlayer.Play();             // デフォルトの動画を再生

        // イベントリスナーを解除
        videoPlayer.loopPointReached -= OnSpecialVideoEnd;
    }


        // 戦闘動画をループ再生するメソッド
    public void PlayBattleVideo(VideoClip battleClip)
    {
        if (videoPlayer != null && battleClip != null)
        {
            videoPlayer.isLooping = true;    // 戦闘動画はループ再生を有効化
            videoPlayer.clip = battleClip;   // 指定された戦闘動画をセット
            videoPlayer.SetDirectAudioMute(0, true); // トラック0の映像の音声を無効
            videoPlayer.Play();              // 戦闘動画を再生

            // ボーナス終了時にループを無効にする処理
            videoPlayer.loopPointReached += OnBattleVideoEnd; // 動画が終わった際のイベントを設定
        }
    }

    // 戦闘動画が終了したときに呼ばれる処理
    private void OnBattleVideoEnd(VideoPlayer vp)
    {
        videoPlayer.isLooping = false;   // ループ再生を無効化
        videoPlayer.clip = defaultClip;  // デフォルトの動画に戻す
        videoPlayer.SetDirectAudioMute(0, true); // トラック0の映像の音声をミュート
        videoPlayer.Play();              // デフォルトの動画を再生

        // イベントリスナーを解除
        videoPlayer.loopPointReached -= OnBattleVideoEnd;
    }


}
