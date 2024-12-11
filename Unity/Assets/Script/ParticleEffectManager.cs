using System.Collections;
using UnityEngine;
using UnityEngine.UI;
using System.Collections.Generic;

public class ParticleEffectManager : MonoBehaviour
{
    [System.Serializable]
    public class LightEffect
    {
        public Image lightImage;
        public ParticleSystem particle;
    }

    [SerializeField] private List<LightEffect> lightEffects;
    [SerializeField] private Color defaultColor = Color.white;
    [SerializeField] private float blinkDuration = 0.6f;
    [SerializeField] private int blinkCount = 6;

    private Coroutine blinkCoroutine;

    private void Start()
    {
        SetDefaultState();
    }

    public void StartBlinking(int blinkType)
    {
        if (blinkCoroutine != null)
        {
            StopCoroutine(blinkCoroutine);
        }

        blinkCoroutine = StartCoroutine(BlinkEffect(blinkType));
    }

    private IEnumerator BlinkEffect(int blinkType)
    {
        Color blinkColor;

        switch (blinkType)
        {
            case 1:
                blinkColor = Color.yellow;
                break;
            case 2:
                blinkColor = Color.red;
                break;
            case 3:
                blinkColor = Color.green;
                break;
            default:
                blinkColor = defaultColor;
                break;
        }

        for (int i = 0; i < blinkCount; i++)
        {
            SetLightsColor(blinkColor);
            yield return new WaitForSeconds(blinkDuration);
            SetLightsColor(defaultColor);
            yield return new WaitForSeconds(blinkDuration);
        }

        // 点滅終了後は指定された色を維持
        SetLightsColor(blinkColor);
        blinkCoroutine = null;
    }

private void SetLightsColor(Color color)
{
    foreach (var effect in lightEffects)
    {
        // Imageの色変更
        if (effect.lightImage != null)
        {
            effect.lightImage.color = color;
        }

        // パーティクルの制御
        if (effect.particle != null)
        {
            if (color == defaultColor)
            {
                effect.particle.Stop();
                effect.particle.Clear();  // 既存のパーティクルをクリア
            }
            else
            {
                var main = effect.particle.main;
                main.startColor = color;

                effect.particle.Stop();
                effect.particle.Clear();
                effect.particle.Play();  // 確実に再生

                Debug.Log($"Playing particle with color: {color}");  // デバッグログ
            }
        }
    }
}

    public void ChangeColor(int colorType)
    {
        Color newColor;

        switch (colorType)
        {
            case 1:
                newColor = Color.yellow;
                break;
            case 2:
                newColor = Color.red;
                break;
            case 3:
                newColor = Color.green;
                break;
            default:
                newColor = defaultColor;
                break;
        }

        SetLightsColor(newColor);
    }

private void StopAllParticles()
{
    foreach (var effect in lightEffects)
    {
        if (effect.particle != null)
        {
            effect.particle.Stop();
        }
    }
}

    public void SetDefaultState()
    {
        if (blinkCoroutine != null)
        {
            StopCoroutine(blinkCoroutine);
            blinkCoroutine = null;
        }
        SetLightsColor(defaultColor);
    }
}

