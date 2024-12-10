using System.Collections;
using UnityEngine;
using UnityEngine.UI;

public class OuterFrameEffect : MonoBehaviour
{
    [SerializeField] private Image frameRenderer;
    [SerializeField] private Color defaultColor;
    [SerializeField] private float blinkDuration = 0.6f;
    [SerializeField] private int blinkCount = 8;

    private Coroutine blinkCoroutine;

    private void Start()
    {
        if (frameRenderer == null)
        {
            frameRenderer = GetComponent<Image>();
        }
        defaultColor = Color.white;
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
            frameRenderer.color = blinkColor;
            yield return new WaitForSeconds(blinkDuration);
            frameRenderer.color = defaultColor;
            yield return new WaitForSeconds(blinkDuration);
        }

        frameRenderer.color = defaultColor;
        blinkCoroutine = null;
    }

    public void ChangeColor(int colorType)
    {
        Debug.Log("ChangeColor");
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

        frameRenderer.color = newColor;
    }

    public void ResetColor()
    {
        frameRenderer.color = defaultColor;
    }
}