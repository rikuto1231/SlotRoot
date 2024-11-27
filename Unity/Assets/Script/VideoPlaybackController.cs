using UnityEngine;
using UnityEngine.Video;

public class VideoPlaybackController : MonoBehaviour
{
    // デリゲートとイベントの定義
    public delegate void VideoEndHandler();
    public event VideoEndHandler OnBonusVideoEnded;
    public event VideoEndHandler OnBattleVideoEnded;
    public event VideoEndHandler OnSpecialVideoEnded;

    [SerializeField] private VideoPlayer videoPlayer;
    [SerializeField] private string defaultVideoPath = "default.mp4";
    
    public bool IsVictoryVideoPlaying { get; private set; }

    private void Awake()
    {
        SetupVideoPlayer();
    }

    private void SetupVideoPlayer()
    {
        if (videoPlayer == null)
        {
            videoPlayer = GetComponent<VideoPlayer>();
        }

        videoPlayer.source = VideoSource.Url;
        videoPlayer.isLooping = true;
        videoPlayer.SetDirectAudioMute(0, true);
        
        string fullPath = System.IO.Path.Combine(Application.streamingAssetsPath, defaultVideoPath);
        videoPlayer.url = fullPath;
        videoPlayer.Play();
    }

    public void PlaySpecialVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) 
        {
            PlayDefaultVideo();
            return;
        }

        IsVictoryVideoPlaying = true;
        PlayVideo(videoPath, false);
        videoPlayer.loopPointReached += OnSpecialVideoEnd;
    }

    public void PlayBonusVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) return;

        IsVictoryVideoPlaying = true;
        PlayVideo(videoPath, false);
        videoPlayer.loopPointReached += OnBonusVideoEnd;
    }

    public void PlayBattleVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) return;

        PlayVideo(videoPath, true);  // バトル動画はループ再生
        videoPlayer.loopPointReached += OnBattleVideoEnd;
    }

    private void PlayVideo(string videoPath, bool looping)
    {
        string fullPath = System.IO.Path.Combine(Application.streamingAssetsPath, videoPath);
        videoPlayer.url = fullPath;
        videoPlayer.isLooping = looping;
        videoPlayer.Play();
    }

    public void PlayDefaultVideo()
    {
        string fullPath = System.IO.Path.Combine(Application.streamingAssetsPath, defaultVideoPath);
        videoPlayer.url = fullPath;
        videoPlayer.isLooping = true;
        videoPlayer.Play();
    }

    private void OnSpecialVideoEnd(VideoPlayer vp)
    {
        IsVictoryVideoPlaying = false;
        OnSpecialVideoEnded?.Invoke();
        videoPlayer.loopPointReached -= OnSpecialVideoEnd;
    }

    private void OnBonusVideoEnd(VideoPlayer vp)
    {
        IsVictoryVideoPlaying = false;
        OnBonusVideoEnded?.Invoke();
        videoPlayer.loopPointReached -= OnBonusVideoEnd;
    }

    private void OnBattleVideoEnd(VideoPlayer vp)
    {
        OnBattleVideoEnded?.Invoke();
    }

    public void StopBattleVideo()
    {
        videoPlayer.loopPointReached -= OnBattleVideoEnd;
        PlayDefaultVideo();
    }
}