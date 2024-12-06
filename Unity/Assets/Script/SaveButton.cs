using UnityEngine;
using UnityEngine.UI;

public class SaveButton : MonoBehaviour
{
    [SerializeField] private Button saveButton;  // 変数名を修正
    [SerializeField] private ReelManager reelManager;

    private void Start()
    {
        if (saveButton != null)  // 変数名を修正
        {
            saveButton.onClick.RemoveAllListeners();
            saveButton.onClick.AddListener(OnSaveButtonPressed);
        }
        else
        {
            Debug.LogError("Save Button is not assigned!");  // エラーログを追加
        }
    }

    private void OnSaveButtonPressed()
    {
        Debug.Log("Save button pressed. Saving current points.");
        if (reelManager != null)
        {
            reelManager.SaveCurrentPoints();
        }
        else
        {
            Debug.LogError("ReelManager is not assigned to SaveButton!");
        }
    }
}