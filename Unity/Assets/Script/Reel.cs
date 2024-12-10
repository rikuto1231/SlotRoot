using System.Collections;
using UnityEngine;

public class Reel : MonoBehaviour
{
    [SerializeField] private float resetPosition = -3.0f;
    [SerializeField] private SpriteRenderer spriteRenderer;
    [SerializeField] private Sprite[] sprites;
    [SerializeField] private Transform reelFlame;

    [SerializeField] private float startPositionY = 0.5f;
    [SerializeField] private float reelSpeed = 200f;
    [SerializeField] private float reelStep = 0.12f;

    [SerializeField] private AudioSource stopSound;
    [SerializeField] private AudioClip stopSoundClip;

    private bool reelStart = false;
    private int currentSpriteIndex = 0;
    private Vector2 startPosition;

    private void Awake()
    {
        startPosition = new Vector2(transform.position.x, startPositionY);

        if (spriteRenderer == null)
        {
            Debug.LogError("SpriteRenderer がアタッチされていません。");
        }
        if (sprites.Length == 0)
        {
            Debug.LogError("スプライトが設定されていません。");
        }
        if (sprites.Length > 0)
        {
            spriteRenderer.sprite = sprites[0];
        }

        if (stopSound == null)
        {
            stopSound = gameObject.AddComponent<AudioSource>();
        }
        stopSound.clip = stopSoundClip;
    }

    public void StartReel()
    {
        if (sprites.Length == 0)
        {
            Debug.LogError("スプライト配列が空です。リールを開始できません。");
            return;
        }

        reelStart = true;
        StartCoroutine(ReelRotate());
    }

    public void StopReel()
    {
        reelStart = false;

        if (stopSound != null && stopSoundClip != null)
        {
            stopSound.Play();
        }

        AdjustPositionToReelFlameCenter();
    }

    private IEnumerator ReelRotate()
    {
        while (reelStart)
        {
            if (transform.position.y <= resetPosition)
            {
                transform.position = startPosition;
                currentSpriteIndex = (currentSpriteIndex + 1) % sprites.Length;
                spriteRenderer.sprite = sprites[currentSpriteIndex];
            }

            transform.position = new Vector2(transform.position.x, transform.position.y - (reelStep * Time.deltaTime * reelSpeed));

            yield return null;
        }
    }

    private void AdjustPositionToReelFlameCenter()
    {
        if (reelFlame == null)
        {
            Debug.LogError("Reel_Flameが設定されていません。");
            return;
        }

        float reelFlameCenterY = reelFlame.position.y;
        transform.position = new Vector2(transform.position.x, reelFlameCenterY);
    }

    public Sprite GetCurrentSprite()
    {
        return spriteRenderer.sprite;
    }
}