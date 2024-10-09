using System.Collections; // IEnumeratorを使用するために必要
using UnityEngine;
using UnityEngine.UI; // Image用の名前空間

public class OuterFrameEffect : MonoBehaviour
{
    [SerializeField] private Image frameRenderer; // Image コンポーネントを指定
    [SerializeField] private Color defaultColor; // デフォルトの色
    [SerializeField] private float blinkDuration = 0.5f; // 点滅の持続時間
    [SerializeField] private int blinkCount = 6; // 点滅回数

    private Coroutine blinkCoroutine; // 実行中のコルーチンを管理するための変数

    private void Start()
    {
        // Image コンポーネントを取得し、デフォルトの色を保存
        if (frameRenderer == null)
        {
            frameRenderer = GetComponent<Image>(); // 自分自身の Image コンポーネントを取得
        }
        defaultColor = Color.white;
    }

    public void StartBlinking(int blinkType)
    {
        // 既に点滅処理が実行中の場合は停止
        if (blinkCoroutine != null)
        {
            StopCoroutine(blinkCoroutine); // 実行中の点滅処理を停止
        }

        // 新しい点滅処理を開始
        blinkCoroutine = StartCoroutine(BlinkEffect(blinkType));
    }

    private IEnumerator BlinkEffect(int blinkType)
    {
        Color blinkColor;

        // 引数に基づいて色を設定
        switch (blinkType)
        {
            case 1:
                blinkColor = Color.yellow; // 黄色
                break;
            case 2:
                blinkColor = Color.red; // 赤色
                break;
            case 3:
                blinkColor = Color.green; // 緑色
                break;
            default:
                blinkColor = defaultColor; // デフォルトの色
                break;
        }

        // 点滅処理
        for (int i = 0; i < blinkCount; i++) // 点滅回数分ループ
        {
            frameRenderer.color = blinkColor; // 点滅色に変更
            yield return new WaitForSeconds(blinkDuration);
            frameRenderer.color = defaultColor; // デフォルトの色に戻す
            yield return new WaitForSeconds(blinkDuration);
        }

        frameRenderer.color = defaultColor; // 最後にデフォルトの色に戻す
        blinkCoroutine = null; // コルーチンが終了したことを示す
    }


    public void ChangeColor(int colorType)
    {
        Debug.Log("ChangeColor");
        Color newColor;

        // 引数に基づいて色を設定
        switch (colorType)
        {
            case 1:
                newColor = Color.yellow; // 黄色
                break;
            case 2:
                newColor = Color.red; // 赤色
                break;
            case 3:
                newColor = Color.green; // 緑色
                break;
            default:
                newColor = defaultColor; // デフォルトの色
                break;
        }

        frameRenderer.color = newColor; // 色を変更
    }

    public void ResetColor()
    {
        frameRenderer.color = defaultColor; // デフォルトの色に戻す
    }

}
