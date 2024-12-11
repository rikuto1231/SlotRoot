using UnityEngine;
using UnityEngine.Networking;
using System;
using System.Collections;

public class SharedPlayManager : MonoBehaviour
{
    [SerializeField] private ReelManager reelManager;
    [SerializeField] private float pollInterval = 0.3f;  // 通常のポーリング間隔を短縮
    [SerializeField] private GameObject sharedModeUI;

    private string roomPassword;
    private bool isSharedMode = false;
    private bool isUpdating = false;
    private Coroutine pollCoroutine;
    private const string API_BASE_URL = ".";

    private void Start()
    {
        if (sharedModeUI != null)
        {
            sharedModeUI.SetActive(false);
        }
    }

    public void InitializeSharedPlay(string jsonData)
    {
        try
        {
            var data = JsonUtility.FromJson<SharedPlayData>(jsonData);
            roomPassword = data.roomPassword;
            isSharedMode = true;

            if (sharedModeUI != null)
            {
                sharedModeUI.SetActive(true);
            }

            if (pollCoroutine != null)
            {
                StopCoroutine(pollCoroutine);
            }
            pollCoroutine = StartCoroutine(PollRoomStatus());

            reelManager.InitializeSharedPlay(data.totalPoints);

            Debug.Log($"Shared play initialized with room: {roomPassword}, points: {data.totalPoints}");
        }
        catch (Exception e)
        {
            Debug.LogError($"Error initializing shared play: {e.Message}");
        }
    }

    private IEnumerator PollRoomStatus()
    {
        var waitForInterval = new WaitForSeconds(pollInterval);
        var shortWait = new WaitForSeconds(0.1f);  // 更新直後の短い待機時間

        while (isSharedMode)
        {
            yield return StartCoroutine(CheckRoomStatus());
            
            // ポイントの更新があった直後は、より頻繁にチェック
            if (isUpdating)
            {
                yield return shortWait;
            }
            else
            {
                yield return waitForInterval;
            }
        }
    }

    private IEnumerator CheckRoomStatus()
    {
        string url = $"{API_BASE_URL}/shared_play_handler.php?action=check";
        var request = new UnityWebRequest(url, "POST");
        
        try
        {
            var jsonData = JsonUtility.ToJson(new RoomCheckData { password = roomPassword });
            byte[] bodyRaw = System.Text.Encoding.UTF8.GetBytes(jsonData);
            request.uploadHandler = new UploadHandlerRaw(bodyRaw);
            request.downloadHandler = new DownloadHandlerBuffer();
            request.SetRequestHeader("Content-Type", "application/json");

            yield return request.SendWebRequest();

            if (request.result == UnityWebRequest.Result.Success)
            {
                try
                {
                    var response = JsonUtility.FromJson<RoomResponse>(request.downloadHandler.text);
                    HandleRoomUpdate(response);
                }
                catch (Exception e)
                {
                    Debug.LogError($"Error parsing room status: {e.Message}");
                }
            }
            else
            {
                Debug.LogError($"Error checking room status: {request.error}");
            }
        }
        finally
        {
            request.Dispose();
        }
    }

    private void HandleRoomUpdate(RoomResponse response)
    {
        if (response.status == "error")
        {
            EndSharedPlay();
            return;
        }

        if (reelManager.GetCurrentPoints() != response.total_points)
        {
            reelManager.UpdateSharedPoints(response.total_points);
        }
    }

    public void UpdatePoints(int newTotalPoints)
    {
        if (!isSharedMode) return;

        isUpdating = true;
        
        // 即座にローカルの値を更新
        reelManager.UpdateSharedPoints(newTotalPoints);
        
        // サーバーへの更新を非同期で実行
        StartCoroutine(SendPointUpdate(newTotalPoints));
    }

    private IEnumerator SendPointUpdate(int points)
    {
        string url = $"{API_BASE_URL}/shared_play_handler.php?action=update";
        var request = new UnityWebRequest(url, "POST");
        
        try
        {
            var jsonData = JsonUtility.ToJson(new PointUpdateData 
            { 
                password = roomPassword,
                points = points
            });
            
            byte[] bodyRaw = System.Text.Encoding.UTF8.GetBytes(jsonData);
            request.uploadHandler = new UploadHandlerRaw(bodyRaw);
            request.downloadHandler = new DownloadHandlerBuffer();
            request.SetRequestHeader("Content-Type", "application/json");

            yield return request.SendWebRequest();

            if (request.result != UnityWebRequest.Result.Success)
            {
                Debug.LogError($"Error updating points: {request.error}");
            }
        }
        finally
        {
            request.Dispose();
            isUpdating = false;
        }
    }

    public void EndSharedPlay()
    {
        if (!isSharedMode) return;

        isSharedMode = false;
        isUpdating = false;
        
        if (pollCoroutine != null)
        {
            StopCoroutine(pollCoroutine);
            pollCoroutine = null;
        }

        if (sharedModeUI != null)
        {
            sharedModeUI.SetActive(false);
        }

        reelManager.SaveSharedPlayPoints();

        Debug.Log("Shared play ended");
    }

    private void OnDestroy()
    {
        if (isSharedMode)
        {
            EndSharedPlay();
        }
    }

    [Serializable]
    private class SharedPlayData
    {
        public string roomPassword;
        public int totalPoints;
    }

    [Serializable]
    private class RoomCheckData
    {
        public string password;
    }

    [Serializable]
    private class PointUpdateData
    {
        public string password;
        public int points;
    }

    [Serializable]
    private class RoomResponse
    {
        public string status;
        public int total_points;
        public string message;
    }
}