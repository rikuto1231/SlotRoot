using System.Collections.Generic;
using UnityEngine;
using TMPro;
using UnityEngine.Video;

public class ReelManager : MonoBehaviour
{
    [SerializeField] private List<Reel> reels;
    [SerializeField] private VideoPlaybackController videoPlayManager;
    [SerializeField] private VideoPlaybackController videoPlaybackController; // VideoPlaybackControllerのインスタンス
    [SerializeField] private AudioSource specialAudioSource;
    [SerializeField] private TextMeshProUGUI pointText;
    [SerializeField] private OuterFrameEffect outerFrameEffect; // OuterFrameEffectを追加
    [SerializeField] private AudioClip spacePressSound; // リール回転時の稼働オブジェクト

    [SerializeField] private List<SpriteEffect> spriteEffects;

    private int currentReelIndex = 0;
    private bool isReelActive = false;
    private int playerPoints = 0;
    private int rotationCount = 0;
    private int bonusReelCount = 0;
    private bool canRotate = true; // リールを回転できるかどうか
    private bool isInBonusState = false; // ボーナス状態かどうか
    private SpriteEffect effect; // 現在のスプライト効果
    private bool isBattleResultChecked = false; // バトル結果がチェックされたかどうか
    private bool canReplay = false; //リプレイ判断


    private void Start()
    {
        videoPlaybackController = FindObjectOfType<VideoPlaybackController>();
        InitializePoints(); // 初期ポイントの設定
        UpdatePointDisplay(); // ポイントの表示を更新
    }

    private void Update()
    {

        // スペースキーでリールを回転させる
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && !isInBonusState && !isReelActive && !videoPlaybackController.IsVictoryVideoPlaying)
        {
            Debug.Log($"通常側の状態 - canRotate: {canRotate}, isInBonusState: {isInBonusState}, isReelActive: {isReelActive}, playerPoints: {playerPoints}, rotationCount: {rotationCount}, bonusReelCount: {bonusReelCount}");
            Debug.Log("通常１．リールを回転させる処理を開始"); // 通常状態のログ
            PlaySpacePressSound(); // スペースキー音声を再生
            StartAllReels(); // リールを回転させる
            rotationCount++; // 回転回数をカウント
            AddPoints(-10); // 10ポイント減少
            CheckForBonusDraw(); // ボーナス抽選のチェック
        }

        // ボーナス状態中の回転処理
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && isInBonusState && !isReelActive && !videoPlaybackController.IsVictoryVideoPlaying)
        {
            Debug.Log($"ボーナス側の状態 - canRotate: {canRotate}, isInBonusState: {isInBonusState}, isReelActive: {isReelActive}, playerPoints: {playerPoints}, rotationCount: {rotationCount}, bonusReelCount: {bonusReelCount}");
            Debug.Log("ボーナス１．ボーナス状態中のリール回転処理を開始"); // ボーナス状態のログ
            PlaySpacePressSound(); // スペースキー音声を再生
            StartAllReels(); // リールを回転させる
            rotationCount++; // 回転回数をカウント
            bonusReelCount++; // ボーナス回数をカウント
            AddPoints(30); // 30ポイント加算

            // ボーナス回数が10回に達した場合の処理
            if (bonusReelCount >= 10 && !isBattleResultChecked)
            {
                HandleDefeat(); // 敗北処理
            }
        }

        // リールを停止する
        if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
        {
            Debug.Log("通常２．現在のリールを停止する処理を開始"); // 通常状態のログ
            StopCurrentReel(); // 現在のリールを停止
        }
    }

    private void InitializePoints()
    {
        playerPoints = 500; // 初期ポイントを500に設定
    }
    
    private void PlaySpacePressSound()
    {
        if (spacePressSound != null)
        {
            // specialAudioSource.volume = 1.0f; // 最大音量
            specialAudioSource.PlayOneShot(spacePressSound); // スペースキー音声を再生
        }
    }

    private void CheckForBonusDraw()
    {
        foreach (var effect_in in spriteEffects)
        {
            if (Random.value < effect_in.hitProbability) // 確率に基づいてボーナスを抽選
            {
                Debug.Log("通常３．ボーナス抽選に成功"); // 通常状態のログ
                effect = effect_in; // ボーナス効果を設定
                EnterBonusState(); // ボーナス状態に入る
                break; // ボーナスが決定したらループを抜ける
            }
        }
    }

    private void EnterBonusState()
    {
        if (!isInBonusState) // ボーナス状態でない場合
        {
            Debug.Log("ボーナス２．ボーナス状態に入る"); // ボーナス状態のログ
            isInBonusState = true; // ボーナス状態に設定
            bonusReelCount = 0; // ボーナス回数をリセット
            outerFrameEffect.StartBlinking(2); // 点滅を開始
            videoPlayManager.PlayBonusVideo(effect.specialVideo); // ボーナス動画を再生

            AudioSource.PlayClipAtPoint(effect.specialAudioClip, Camera.main.transform.position);

            canRotate = false; // 登場動画が流れている間はリールを回せないようにする

            // 既存のリスナーを解除してから新しいリスナーを追加
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached -= OnBonusVideoEnd; 
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += OnBonusVideoEnd;
        }
    }

    private void OnBonusVideoEnd(VideoPlayer vp)
    {
        Debug.Log("ボーナス３．登場動画終了後にフレームを赤色にしています"); // ボーナス状態のログ
        // 登場動画終了後にフレームを赤色に変更
        outerFrameEffect.ChangeColor(2);
        canRotate = true; // 動画が終了したらリールを回せるようにする
        PlayBattleVideo(); // バトル動画を再生

        // イベントリスナーを解除
        videoPlayManager.GetComponent<VideoPlayer>().loopPointReached -= OnBonusVideoEnd;

        // 戦闘中動画をループ再生
        videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += OnBattleVideoEnd; // 新しいイベントリスナー
    }

    // 戦闘中動画が終了したときに再度再生する処理
    private void OnBattleVideoEnd(VideoPlayer vp)
    {
        if (isInBonusState)
        {
            Debug.Log("ボーナス４．戦闘動画を再生します"); // ボーナス状態のログ
            PlayBattleVideo(); // 戦闘動画を再度再生
        }
        else
        {
            // ボーナスが終了したらループを停止
            videoPlayManager.GetComponent<VideoPlayer>().loopPointReached -= OnBattleVideoEnd;
        }
    }

    private void PlayBattleVideo()
    {
        videoPlayManager.PlayBattleVideo(effect.battleVideo); // バトル動画を再生
    }

    private void PlayVictoryVideo()
    {
        outerFrameEffect.StartBlinking(1); // 点滅を開始
        videoPlayManager.PlaySpecialVideo(effect.victoryVideo); // 勝利動画を再生
        AudioSource.PlayClipAtPoint(effect.victoryAudioClip, Camera.main.transform.position); // 勝利音声を再生
        AddPoints(effect.pointsForSprite); // スプライトのポイントを加算
        Debug.Log("ボーナス５．勝利動画を再生"); // ボーナス状態のログ
        EndBonusState(); // ボーナス状態を終了

    }

    private void HandleDefeat()
    {
        Debug.Log("ボーナス５．敗北動画を再生"); // ボーナス状態のログ
        PlayDefeatVideo(); // 敗北動画を再生
        EndBonusState(); // ボーナス状態を終了
    }

    private void PlayDefeatVideo()
    {
        videoPlayManager.PlaySpecialVideo(effect.defeatVideo); // 敗北動画を再生
        AudioSource.PlayClipAtPoint(effect.defeatAudioClip, Camera.main.transform.position); // 敗北音声を再生
        EndBonusState(); // ボーナス状態を終了
    }

    private void EndBonusState()
    {
        Debug.Log("ボーナス６．ボーナス状態を終了"); // ボーナス状態のログ
        isInBonusState = false;
        isBattleResultChecked = false;
        bonusReelCount = 0;
        videoPlayManager.PlaySpecialVideo(null); // デフォルト動画を再生
    }

    private void StartAllReels()
    {
        Debug.Log("通常４．すべてのリールを開始"); // 通常状態のログ
        foreach (var reel in reels)
        {
            reel.StartReel(); // すべてのリールを開始
        }
        isReelActive = true; // リールがアクティブ状態に
        currentReelIndex = 0; // 現在のリールインデックスをリセット
    }

    private void StopCurrentReel()
    {
        if (currentReelIndex < reels.Count) // リールがまだ残っている場合
        {
            Debug.Log("通常５．現在のリールを停止"); // 通常状態のログ
            reels[currentReelIndex].StopReel(); // 現在のリールを停止
            currentReelIndex++; // 次のリールに進む

            if (currentReelIndex >= reels.Count) // すべてのリールが停止した場合
            {
                isReelActive = false; // リールがアクティブでなくなる
                CheckForWin(); // 勝利条件のチェック
            }
        }
    }

    private void CheckForWin()
    {
        // スプライトの整列チェック
        bool isWinningCombination = true; // 勝利条件が満たされたかどうかのフラグ

        for (int i = 1; i < reels.Count; i++)
        {
            if (reels[i].GetCurrentSprite() != reels[0].GetCurrentSprite())
            {
                isWinningCombination = false; // 一致しないスプライトが見つかった場合
                break; // ループを抜ける
            }
        }

        if (isWinningCombination)
        {
            // 現在のスプライトがボーナス効果に関連するかチェック
            if (reels[0].GetCurrentSprite() == effect.sprite && isInBonusState)
            {
                PlayVictoryVideo(); // 勝利動画を再生
            }
            else
            {
                Debug.Log("通常６．スプライトがボーナス効果に関連していない"); // 通常状態のログ
            }

            // リプレイ可能かどうかをチェック
            if (effect.allowsReplay)
            {
                canReplay = true; // リプレイフラグをセット
                Debug.Log("リプレイが可能です");
            }
        }
        else
        {
            Debug.Log("通常７．スプライトが整列していない"); // 通常状態のログ
        }
    }


    private void UpdatePointDisplay()
    {
        pointText.text = playerPoints + "P"; // ポイントを表示
    }

    private void AddPoints(int amount)
    {
        playerPoints += amount; // ポイントを加算
        UpdatePointDisplay(); // ポイントの表示を更新
    }

        public void ResetPoints()
    {
        playerPoints = 0; // ポイントをリセット
        UpdatePointDisplay(); // ポイント表示を更新
        Debug.Log("ポイントがリセットされました。");
    }
}

