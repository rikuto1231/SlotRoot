public class PlayerController : MonoBehaviour
{
    [Header("Movement Parameters")]
    [SerializeField] private float moveSpeed = 5f;
    [SerializeField] private float jumpForce = 7f;
    [SerializeField] private float groundCheckRadius = 0.2f;

    [Header("Key Config")]
    [SerializeField] private KeyCode jumpKey = KeyCode.Space;    // ジャンプキー
    [SerializeField] private KeyCode leftKey = KeyCode.A;        // 左移動キー
    [SerializeField] private KeyCode rightKey = KeyCode.D;       // 右移動キー
    [SerializeField] private bool useArrowKeys = true;          // 矢印キーを使用するかどうか

    private Rigidbody2D rb;
    private Animator animator;
    private bool isGrounded;
    private float moveInput;
    private bool isFacingRight = true;

    private void Start()
    {
        rb = GetComponent<Rigidbody2D>();
        animator = GetComponent<Animator>();
    }

    private void Update()
    {
        // キーボード入力の処理
        HandleKeyboardInput();

        // ジャンプ処理
        HandleJump();

        // アニメーション更新
        UpdateAnimations();
    }

    private void HandleKeyboardInput()
    {
        // WASDキーの処理
        if (Input.GetKey(leftKey))
            moveInput = -1f;
        else if (Input.GetKey(rightKey))
            moveInput = 1f;
        else
            moveInput = 0f;

        // 矢印キーの処理（有効な場合）
        if (useArrowKeys)
        {
            if (Input.GetKey(KeyCode.LeftArrow))
                moveInput = -1f;
            else if (Input.GetKey(KeyCode.RightArrow))
                moveInput = 1f;
        }
    }

    private void HandleJump()
    {
        // スペースキーまたは設定されたジャンプキーでジャンプ
        if ((Input.GetKeyDown(jumpKey) || (useArrowKeys && Input.GetKeyDown(KeyCode.UpArrow))) && isGrounded)
        {
            Jump();
        }
    }

    private void FixedUpdate()
    {
        // 接地判定
        CheckGrounded();
        
        // 移動処理
        Move();

        // 向きの更新
        CheckFlip();
    }

    private void Move()
    {
        rb.velocity = new Vector2(moveInput * moveSpeed, rb.velocity.y);
    }

    private void Jump()
    {
        rb.velocity = new Vector2(rb.velocity.x, jumpForce);
        // ジャンプ効果音を再生する場合はここに追加
    }

    // デバッグ用のキー設定表示
    private void OnGUI()
    {
        if (Debug.isDebugBuild)
        {
            GUILayout.BeginArea(new Rect(10, 10, 200, 100));
            GUILayout.Label($"Move: {leftKey}/{rightKey} or Arrow Keys");
            GUILayout.Label($"Jump: {jumpKey} or Up Arrow");
            GUILayout.EndArea();
        }
    }
}