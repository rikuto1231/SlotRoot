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
        SetupParticles();
        SetDefaultState();
    }

    private void SetupParticles()
    {
        if (lightEffects == null || lightEffects.Count == 0)
        {
            Debug.LogError("No light effects configured");
            return;
        }

        foreach (var effect in lightEffects)
        {
            if (effect.particle != null)
            {
                ConfigureParticleSystem(effect.particle);
            }
        }
    }

    private void ConfigureParticleSystem(ParticleSystem particle)
    {
        particle.Stop();
        particle.Clear();

        var main = particle.main;
        main.loop = true;
        main.playOnAwake = false;
        main.duration = 1.0f;
        main.startLifetime = 0.8f;
        main.startSpeed = 2f;
        main.startSize = 0.05f;
        main.maxParticles = 30;
        main.simulationSpace = ParticleSystemSimulationSpace.World;
        main.scalingMode = ParticleSystemScalingMode.Local;
        main.gravityModifier = 0;

        var emission = particle.emission;
        emission.enabled = true;
        emission.rateOverTime = 20;

        var shape = particle.shape;
        shape.enabled = true;
        shape.shapeType = ParticleSystemShapeType.Circle;
        shape.radius = 0.05f;
        shape.radiusThickness = 0;
        shape.arc = 360;
        shape.randomDirectionAmount = 0.2f;

        var colorOverLifetime = particle.colorOverLifetime;
        colorOverLifetime.enabled = true;
        var gradient = new Gradient();
        gradient.SetKeys(
            new GradientColorKey[] { 
                new GradientColorKey(Color.white, 0.0f),
                new GradientColorKey(Color.white, 1.0f) 
            },
            new GradientAlphaKey[] {
                new GradientAlphaKey(1.0f, 0.0f),
                new GradientAlphaKey(0.0f, 1.0f)
            }
        );
        colorOverLifetime.color = gradient;

        var sizeOverLifetime = particle.sizeOverLifetime;
        sizeOverLifetime.enabled = true;
        var curve = new AnimationCurve(
            new Keyframe(0f, 0.5f),
            new Keyframe(0.5f, 1f),
            new Keyframe(1f, 0f)
        );
        sizeOverLifetime.size = new ParticleSystem.MinMaxCurve(1f, curve);

        var velocityOverLifetime = particle.velocityOverLifetime;
        velocityOverLifetime.enabled = true;
        velocityOverLifetime.radial = 1.0f;

        var renderer = particle.GetComponent<ParticleSystemRenderer>();
        if (renderer != null)
        {
            renderer.renderMode = ParticleSystemRenderMode.Billboard;
            renderer.sortingLayerID = SortingLayer.NameToID("UI");
            renderer.sortingOrder = 5;
            renderer.material = new Material(Shader.Find("Particles/Additive"));

            // パーティクル描画位置を親のImageに合わせる
            renderer.pivot = Vector3.zero;
            renderer.alignment = ParticleSystemRenderSpace.View;
        }

        // パーティクルのTransformは親のImageに合わせる
        particle.transform.localPosition = Vector3.zero;
        particle.transform.localScale = Vector3.one;
    }

private void CreateBurstEffect(ParticleSystem particle, Color targetColor)
{
    var emission = particle.emission;
    
    // バーストの設定
    var burst = new ParticleSystem.Burst(0.0f, 10, 1, 0.01f);
    emission.SetBursts(new ParticleSystem.Burst[] { burst });

    // 色の設定
    var main = particle.main;
    var startColor = main.startColor;
    startColor.mode = ParticleSystemGradientMode.TwoColors;
    startColor.colorMin = Color.white;
    startColor.colorMax = targetColor;
    main.startColor = startColor;
}

    private void SetLightsColor(Color color)
    {
        foreach (var effect in lightEffects)
        {
            if (effect.lightImage != null)
            {
                effect.lightImage.color = color;
            }

            if (effect.particle != null)
            {
                if (color == defaultColor)
                {
                    effect.particle.Stop();
                    effect.particle.Clear();
                }
                else
                {
                    CreateBurstEffect(effect.particle, color);
                    effect.particle.Play();
                }
            }
        }
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

        SetLightsColor(blinkColor);
        blinkCoroutine = null;
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