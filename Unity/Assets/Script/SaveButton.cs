using UnityEngine;
using UnityEngine.UI;
using System.Collections;

public class SaveButton : MonoBehaviour
{
    [SerializeField] private Button saveButton;
    [SerializeField] private ReelManager reelManager;
    [SerializeField] private Image saveNotification;  // 追加: 通知用画像
    [SerializeField] private float notificationDuration = 3f;  // 追加: 表示時間

    private Coroutine showNotificationCoroutine;  // 追加: コルーチン管理用

    private void Start()
    {
        if (saveButton != null)
        {
            saveButton.onClick.RemoveAllListeners();
            saveButton.onClick.AddListener(OnSaveButtonPressed);
        }
        else
        {
            Debug.LogError("Save Button is not assigned!");
        }

        // 追加: 最初は非表示に
        if (saveNotification != null)
        {
            saveNotification.gameObject.SetActive(false);
        }
    }

    private void OnSaveButtonPressed()
    {
        Debug.Log("Save button pressed. Saving current points.");
        if (reelManager != null)
        {
            reelManager.SaveCurrentPoints();
            ShowSaveNotification();  // 追加: 通知表示
        }
        else
        {
            Debug.LogError("ReelManager is not assigned to SaveButton!");
        }
    }

    // 追加: 通知表示用メソッド
    private void ShowSaveNotification()
    {
        if (saveNotification == null) return;

        // 既存のコルーチンがあれば停止
        if (showNotificationCoroutine != null)
        {
            StopCoroutine(showNotificationCoroutine);
        }

        showNotificationCoroutine = StartCoroutine(ShowNotificationCoroutine());
    }

    // 追加: 通知表示用コルーチン
    private IEnumerator ShowNotificationCoroutine()
    {
        saveNotification.gameObject.SetActive(true);
        yield return new WaitForSeconds(notificationDuration);
        saveNotification.gameObject.SetActive(false);
        showNotificationCoroutine = null;
    }
}