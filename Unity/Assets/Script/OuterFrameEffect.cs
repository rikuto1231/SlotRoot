using System.Collections; // IEnumeratorを使用するために必要
using UnityEngine;
using UnityEngine.UI; // Image用の名前空間

public class OuterFrameEffect : MonoBehaviour
{
    [SerializeField] private Image frameRenderer; // Image コンポーネントを指定
    [SerializeField] private Color blinkColor = Color.yellow; // 点滅時の色
    [SerializeField] private float blinkDuration = 0.5f; // 点滅の持続時間
    [SerializeField] private int blinkCount = 5; // 点滅回数

    private Coroutine blinkCoroutine; // 実行中のコルーチンを管理するための変数

    //デフォルト色をstartで取得して色継続変更バグへの対応変数
    private Color defaultColor;
    

    private void Start()
    {
        // Image コンポーネントを取得し、デフォルトの色を保存
        if (frameRenderer == null)
        {
            frameRenderer = GetComponent<Image>(); // 自分自身の Image コンポーネントを取得
        }
        defaultColor = frameRenderer.color; // デフォルトの色を保存
    }

    public void StartBlinking()
    {
        // 既に点滅処理が実行中の場合は停止
        if (blinkCoroutine != null)
        {
            StopCoroutine(blinkCoroutine); // 実行中の点滅処理を停止
        }

        // 新しい点滅処理を開始
        blinkCoroutine = StartCoroutine(BlinkEffect());
    }

    private IEnumerator BlinkEffect()
    {
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
}
