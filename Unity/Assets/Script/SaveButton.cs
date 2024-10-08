using UnityEngine;
using UnityEngine.UI; // Buttonコンポーネント用

public class QuitButton : MonoBehaviour
{
    [SerializeField] private Button quitButton; // UIの終了ボタン
    [SerializeField] private ReelManager reelManager; // ReelManagerスクリプトへの参照（ポイントリセット用）

    private void Start()
    {
        if (quitButton != null)
        {
            quitButton.onClick.AddListener(OnQuitButtonPressed); // ボタンが押されたときにOnQuitButtonPressedを呼び出す
        }
    }

    // 終了ボタンが押されたときに呼び出されるメソッド
    private void OnQuitButtonPressed()
    {
        Debug.Log("終了ボタンが押されました。ポイントをリセットします。");
        if (reelManager != null)
        {
            reelManager.ResetPoints(); // ReelManagerのポイントリセットメソッドを呼び出す
        }
    }
}
