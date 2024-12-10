using UnityEngine;
using UnityEngine.Video;
using System.Collections.Generic;

public class VideoPlaybackController : MonoBehaviour
{
    public delegate void VideoEndHandler();
    public event VideoEndHandler OnBonusVideoEnded;
    public event VideoEndHandler OnBattleVideoEnded;
    public event VideoEndHandler OnSpecialVideoEnded;

    [SerializeField] private VideoPlayer videoPlayer;
    [SerializeField] private List<string> defaultVideoPaths;

    public bool IsVictoryVideoPlaying { get; private set; }
    private bool isPlayingDefaultVideo = false;

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
        videoPlayer.isLooping = false;
        videoPlayer.SetDirectAudioMute(0, true);
        videoPlayer.skipOnDrop = true;

        videoPlayer.loopPointReached += OnDefaultVideoEnd;
        
        PlayDefaultVideo();
    }

    public void PlaySpecialVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) 
        {
            PlayDefaultVideo();
            return;
        }

        isPlayingDefaultVideo = false;
        IsVictoryVideoPlaying = true;
        PlayVideo(videoPath, false);
        videoPlayer.loopPointReached += OnSpecialVideoEnd;
    }

    public void PlayBonusVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) return;

        isPlayingDefaultVideo = false;
        IsVictoryVideoPlaying = true;
        PlayVideo(videoPath, false);
        videoPlayer.loopPointReached += OnBonusVideoEnd;
    }

    public void PlayBattleVideo(string videoPath)
    {
        if (string.IsNullOrEmpty(videoPath)) return;

        isPlayingDefaultVideo = false;
        PlayVideo(videoPath, true);
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
        if (defaultVideoPaths == null || defaultVideoPaths.Count == 0)
        {
            Debug.LogError("Default video paths are not set!");
            return;
        }

        isPlayingDefaultVideo = true;
        int randomIndex = Random.Range(0, defaultVideoPaths.Count);
        string randomVideoPath = defaultVideoPaths[randomIndex];
        string fullPath = System.IO.Path.Combine(Application.streamingAssetsPath, randomVideoPath);

        videoPlayer.url = fullPath;
        videoPlayer.isLooping = false;
        videoPlayer.Play();
    }

    private void OnDefaultVideoEnd(VideoPlayer vp)
    {
        if (isPlayingDefaultVideo)
        {
            PlayDefaultVideo();
        }
    }

    private void OnSpecialVideoEnd(VideoPlayer vp)
    {
        IsVictoryVideoPlaying = false;
        OnSpecialVideoEnded?.Invoke();
        videoPlayer.loopPointReached -= OnSpecialVideoEnd;
        PlayDefaultVideo();
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
        isPlayingDefaultVideo = true;
        PlayDefaultVideo();
    }

    private void OnDisable()
    {
        if (videoPlayer != null)
        {
            videoPlayer.loopPointReached -= OnDefaultVideoEnd;
        }
    }
}