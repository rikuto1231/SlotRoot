using System.Collections;
using System.Collections.Generic;
using UnityEngine;

// 新しいスクリプトを作成：SessionManager.cs
public class SessionManager : MonoBehaviour
{
    public static SessionManager Instance { get; private set; }

    private void Awake()
    {
        if (Instance == null)
        {
            Instance = this;
        }
        else
        {
            Destroy(gameObject);
        }
    }

    public void SetUserInfo(string jsonInfo)
    {
        try
        {
            var info = JsonUtility.FromJson<UserInfo>(jsonInfo);
            UserSession.SetUserInfo(info.user_id, info.user_name);
        }
        catch (System.Exception e)
        {
            Debug.LogError($"Error parsing user info: {e.Message}");
        }
    }
}

[System.Serializable]
public class UserInfo
{
    public int user_id;
    public string user_name;
}
