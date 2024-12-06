// --- ApiManager.cs ---
using UnityEngine;
using UnityEngine.Networking;
using System;
using System.Threading.Tasks;

public static class ApiManager
{
    private const string API_BASE_URL = "."; 

// ApiManager.cs のログ追加部分
public static async void GetUserPoint(MonoBehaviour context, int userId, System.Action<int> callback)
{
    string url = $"{API_BASE_URL}/point_api.php?user_id={userId}";
    Debug.Log($"Fetching user point from: {url}");
    
    using (UnityWebRequest request = UnityWebRequest.Get(url))
    {
        try
        {
            await request.SendWebRequest();
            
            Debug.Log($"Response Code: {request.responseCode}");
            Debug.Log($"Response Headers: {request.GetResponseHeaders()}");
            
            if (request.result == UnityWebRequest.Result.Success)
            {
                string response = request.downloadHandler.text;
                Debug.Log($"API Response: {response}");
                
                if (int.TryParse(response, out int point))
                {
                    Debug.Log($"Successfully retrieved point for user {userId}: {point}");
                    callback(point);
                }
                else
                {
                    Debug.LogWarning($"Failed to parse point value from API response: {response}");
                    callback(-1);
                }
            }
            else
            {
                Debug.LogError($"API request failed: {request.error}");
                Debug.LogError($"Error details: {request.downloadHandler.text}");
                callback(-1);
            }
        }
        catch (Exception e)
        {
            Debug.LogError($"Exception during API request: {e.Message}");
            Debug.LogError($"Stack trace: {e.StackTrace}");
            callback(-1);
        }
    }
}

    public static async void SaveUserPoint(MonoBehaviour context, int userId, int point)
    {
        string url = $"{API_BASE_URL}/point_api.php";
        Debug.Log($"Saving user point to: {url}");
        
        WWWForm form = new WWWForm();
        form.AddField("user_id", userId);
        form.AddField("point", point);

        using (UnityWebRequest request = UnityWebRequest.Post(url, form))
        {
            try
            {
                await request.SendWebRequest();
                
                if (request.result == UnityWebRequest.Result.Success)
                {
                    Debug.Log($"Successfully saved point for user {userId}: {point}");
                }
                else
                {
                    Debug.LogError($"Failed to save point: {request.error}");
                }
            }
            catch (Exception e)
            {
                Debug.LogError($"Exception during save point: {e.Message}");
            }
        }
    }
}