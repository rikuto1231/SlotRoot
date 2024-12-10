using UnityEngine;
using System.Collections.Generic;
using TMPro;
using UnityEngine.UI;

// 追加要件：フレームとライトの点滅処理を同期するために共通管理クラス方式に移行。
public class ReelManager : MonoBehaviour
{
    [SerializeField] private List<Reel> reels;
    [SerializeField] private VideoPlaybackController videoPlayManager;
    [SerializeField] private AudioSource specialAudioSource;
    [SerializeField] private TextMeshProUGUI pointText;
    [SerializeField] private OuterFrameEffect outerFrameEffect;
    [SerializeField] private AudioClip spacePressSound;
    [SerializeField] private Image bonusImage;
    [SerializeField] private ParticleEffectManager effectManager;
    [SerializeField] private List<SpriteEffect> spriteEffects;
    

    private int currentReelIndex = 0;
    private bool isReelActive = false;
    private int point = 0; 
    private int rotationCount = 0;
    private int bonusReelCount = 0;
    private bool canRotate = true;
    private bool isInBonusState = false;
    private SpriteEffect currentEffect;
    private bool isBattleResultChecked = false;
    private bool canReplay = false;
    private bool isPointDirty = false;
    private const int SAVE_THRESHOLD = 10;
    private int changeCount = 0;

    private void Start()
    {
        InitializeGame();
    }

    private void InitializeGame()
    {
        ApiManager.GetUserPoint(this, UserSession.UserId, point => {
            this.point = point;
            UpdatePointDisplay();
        });
    }

    private void Update()
    {
        if (Input.GetKeyDown(KeyCode.Space) && canRotate && !videoPlayManager.IsVictoryVideoPlaying)
        {
            if (!isReelActive)
            {
                if (isInBonusState)
                {
                    HandleBonusRotation();
                }
                else
                {
                    HandleNormalRotation();
                }
            }
        }

        if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
        {
            StopCurrentReel();
        }
    }

    private void HandleNormalRotation()
    {
        PlaySpacePressSound();
        StartAllReels();
        rotationCount++;
        AddPoints(-100);
        CheckForBonusDraw();
    }

    private void HandleBonusRotation()
    {
        PlaySpacePressSound();
        StartAllReels();
        rotationCount++;
        bonusReelCount++;
        AddPoints(150);

        if (bonusReelCount >= 10 && !isBattleResultChecked)
        {
            HandleDefeat();
        }
    }

    private void StartAllReels()
    {
        foreach (var reel in reels)
        {
            reel.StartReel();
        }
        isReelActive = true;
        currentReelIndex = 0;
    }

    private void StopCurrentReel()
    {
        if (currentReelIndex < reels.Count)
        {
            reels[currentReelIndex].StopReel();
            currentReelIndex++;

            if (currentReelIndex >= reels.Count)
            {
                isReelActive = false;
                CheckForWin();
            }
        }
    }

    private void CheckForWin()
    {
        bool isWinningCombination = true;
        
        for (int i = 1; i < reels.Count; i++)
        {
            if (reels[i].GetCurrentSprite() != reels[0].GetCurrentSprite())
            {
                isWinningCombination = false;
                break;
            }
        }

        if (isWinningCombination && currentEffect != null)
        {
            if (isInBonusState && reels[0].GetCurrentSprite() == currentEffect.sprite)
            {
                PlayVictoryVideo();
            }

            if (currentEffect.allowsReplay)
            {
                canReplay = true;
            }
        }
    }

    private void CheckForBonusDraw()
    {
        float totalProbability = 0f;
        foreach (var effect in spriteEffects)
        {
            totalProbability += effect.hitProbability;
        }

        float randomValue = Random.value;
        float currentSum = 0f;

        if (randomValue < totalProbability)
        {
            foreach (var effect in spriteEffects)
            {
                currentSum += effect.hitProbability;
                if (randomValue < currentSum)
                {
                    currentEffect = effect;
                    EnterBonusState();
                    break;
                }
            }
        }
    }

    private void EnterBonusState()
    {
        if (!isInBonusState)
        {
            isInBonusState = true;
            bonusReelCount = 0;
            
            outerFrameEffect.StartBlinking(2);
            if (effectManager != null)
            {
                effectManager.StartBlinking(2);
            }
            
            if (bonusImage != null && currentEffect != null && currentEffect.bonusDisplaySprite != null)
            {
                bonusImage.sprite = currentEffect.bonusDisplaySprite;
                bonusImage.gameObject.SetActive(true);
            }
            
            videoPlayManager.OnBonusVideoEnded += HandleBonusVideoEnd;
            videoPlayManager.PlayBonusVideo(currentEffect.specialVideoPath);
            
            if (currentEffect.specialAudioClip != null)
            {
                AudioSource.PlayClipAtPoint(currentEffect.specialAudioClip, Camera.main.transform.position);
            }

            canRotate = false;
        }
    }

    private void HandleBonusVideoEnd()
    {
        videoPlayManager.OnBonusVideoEnded -= HandleBonusVideoEnd;
        canRotate = true;
        
        outerFrameEffect.ChangeColor(2);
        if (effectManager != null)
        {
            effectManager.ChangeColor(2);
        }
        
        videoPlayManager.PlayBattleVideo(currentEffect.battleVideoPath);
    }

    private void PlayVictoryVideo()
    {
        outerFrameEffect.StartBlinking(1);
        if (effectManager != null)
        {
            effectManager.StartBlinking(1); 
        }
        
        videoPlayManager.StopBattleVideo();
        
        videoPlayManager.OnSpecialVideoEnded += HandleVictoryVideoEnd;
        videoPlayManager.PlaySpecialVideo(currentEffect.victoryVideoPath);
        
        if (currentEffect.victoryAudioClip != null)
        {
            AudioSource.PlayClipAtPoint(currentEffect.victoryAudioClip, Camera.main.transform.position);
        }
        
        AddPoints(currentEffect.pointsForSprite);
        canRotate = false;
    }

    private void PlayDefeatVideo()
    {
        outerFrameEffect.ResetColor();
        videoPlayManager.StopBattleVideo();
        
        videoPlayManager.OnSpecialVideoEnded += HandleDefeatVideoEnd;
        videoPlayManager.PlaySpecialVideo(currentEffect.defeatVideoPath);
        
        if (currentEffect.defeatAudioClip != null)
        {
            AudioSource.PlayClipAtPoint(currentEffect.defeatAudioClip, Camera.main.transform.position);
        }
        
        canRotate = false;
    }

    private void HandleVictoryVideoEnd()
    {
        videoPlayManager.OnSpecialVideoEnded -= HandleVictoryVideoEnd;
        canRotate = true;
        EndBonusState();
    }

    private void HandleDefeatVideoEnd()
    {
        videoPlayManager.OnSpecialVideoEnded -= HandleDefeatVideoEnd;
        canRotate = true;
        EndBonusState();
    }

    private void HandleDefeat()
    {
        PlayDefeatVideo();
    }

    private void EndBonusState()
    {
        isInBonusState = false;
        isBattleResultChecked = false;
        bonusReelCount = 0;
        canRotate = true;
        
        if (bonusImage != null)
        {
            bonusImage.gameObject.SetActive(false);
        }
        
        outerFrameEffect.ResetColor();
        if (effectManager != null)
        {
            effectManager.SetDefaultState();
        }
        
        videoPlayManager.PlayDefaultVideo();
    }

    private void PlaySpacePressSound()
    {
        if (spacePressSound != null && specialAudioSource != null)
        {
            specialAudioSource.PlayOneShot(spacePressSound);
        }
    }

    private void AddPoints(int amount)
    {
        point += amount;
        UpdatePointDisplay();
        isPointDirty = true;
        changeCount++;

        if (changeCount >= SAVE_THRESHOLD)
        {
            SaveCurrentPoints();
            changeCount = 0;
        }
    }

    public void SaveCurrentPoints()
    {
        if (isPointDirty)
        {
            Debug.Log($"ポイントを保存します: {point}");
            ApiManager.SaveUserPoint(this, UserSession.UserId, point);
            isPointDirty = false;
        }
    }

    private void UpdatePointDisplay()
    {
        if (pointText != null)
        {
            pointText.text = $"{point}P";
        }
    }
}