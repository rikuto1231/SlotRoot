using UnityEngine;

public class CameraFollow : MonoBehaviour
{
    public Transform player;  // キャラクターのTransform
    public float smoothSpeed = 0.125f;  // カメラ追従のスムーズさ
    public Vector3 offset;  // プレイヤーとのオフセット

    public float minX, maxX;  // カメラが移動できるX軸の最小値と最大値
    public float minY, maxY;  // カメラが移動できるY軸の最小値と最大値

    void LateUpdate()
    {
        // カメラの目標位置
        Vector3 desiredPosition = player.position + offset;
        Vector3 smoothedPosition = Vector3.Lerp(transform.position, desiredPosition, smoothSpeed);

        // カメラのX軸とY軸の位置を制限する
        float clampedX = Mathf.Clamp(smoothedPosition.x, minX, maxX);
        float clampedY = Mathf.Clamp(smoothedPosition.y, minY, maxY);

        // 制限された位置にカメラを移動
        transform.position = new Vector3(clampedX, clampedY, smoothedPosition.z);
    }
}
