using UnityEngine;

public class PlayerController : MonoBehaviour
{
    public float speed = 5f;           // キャラクターの移動速度
    public float jumpForce = 10f;      // ジャンプの力
    private Rigidbody2D rb;            // キャラクターのRigidbody2Dコンポーネント
    private bool isGrounded;           // キャラクターが地面に接しているかどうか
    private float screenLeft;          // 画面の左端
    private float screenRight;         // 画面の右端

    void Start()
    {
        rb = GetComponent<Rigidbody2D>();
        if (rb == null)
        {
            Debug.LogError("Rigidbody2D component is missing!");
        }

        // カメラの座標計算
        float cameraDistance = Camera.main.transform.position.z - transform.position.z;
        screenLeft = Camera.main.ViewportToWorldPoint(new Vector3(0, 0, cameraDistance)).x;
        screenRight = Camera.main.ViewportToWorldPoint(new Vector3(1, 0, cameraDistance)).x;

        Debug.Log("Screen Left: " + screenLeft + ", Screen Right: " + screenRight);
    }

    void Update()
    {
        float moveInput = Input.GetAxis("Horizontal");
        rb.velocity = new Vector2(moveInput * speed, rb.velocity.y);

        // 後ほどステージ入れかえのロジックを追加
        if (transform.position.x > screenRight)
        {
            transform.position = new Vector3(screenLeft, transform.position.y, transform.position.z);
            Debug.Log("Moved to left side of the screen");
        }
        else if (transform.position.x < screenLeft)
        {
            transform.position = new Vector3(screenRight, transform.position.y, transform.position.z);
            Debug.Log("Moved to right side of the screen");
        }

        
        if (Input.GetKeyDown(KeyCode.Space) && isGrounded)
        {
            rb.velocity = Vector2.up * jumpForce;
        }
    }

    private void OnCollisionEnter2D(Collision2D collision)
    {

        if (collision.gameObject.CompareTag("Ground"))
        {
            isGrounded = true;
        }
    }

    private void OnCollisionExit2D(Collision2D collision)
    {
        if (collision.gameObject.CompareTag("Ground"))
        {
            isGrounded = false;
        }
    }
}
