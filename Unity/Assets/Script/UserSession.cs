// UserSession.cs
public static class UserSession
{
    private const int DEFAULT_USER_ID = 1;
    private const int DEFAULT_POINT = 500;
    
    private static int? _userId;
    public static int UserId
    {
        get
        {
            if (!_userId.HasValue)
            {
                Debug.Log("User ID not found in session, using default: " + DEFAULT_USER_ID);
                _userId = DEFAULT_USER_ID;
            }
            return _userId.Value;
        }
        private set => _userId = value;
    }

    public static string UserName { get; private set; }

    public static void SetUserInfo(int userId, string userName)
    {
        UserId = userId;
        UserName = userName;
        Debug.Log($"Session updated - UserID: {userId}, UserName: {userName}");
    }
}