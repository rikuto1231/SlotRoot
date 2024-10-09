// using UnityEngine;
// using TMPro; // TextMeshPro用の名前空間
// using UnityEngine.Video; // これが必要です
// using System.Collections.Generic;

// public class sample : MonoBehaviour
// {
//     [SerializeField] private List<Reel> reels; // リールのリスト
//     [SerializeField] private VideoPlaybackController videoPlayManager; // VideoPlaybackControllerオブジェクト
//     [SerializeField] private AudioSource winSound; // 当たり音声を再生するためのAudioSource
//     [SerializeField] private TextMeshProUGUI pointText; // ポイントを表示するTextMeshProUGUI
//     [SerializeField] private OuterFrameEffect outerFrameEffect; // OuterFrameのエフェクト
//     [SerializeField] private List<SpriteEffect> spriteEffects; // スプライトごとの演出リスト

//     private int currentReelIndex = 0; // 現在のリールインデックス
//     private bool isReelActive = false; // リールのアクティブ状態
//     private int playerPoints = 0; // プレイヤーのポイント（初期値は0）
//     private int rotationCount = 0; // 回転数を管理する変数
// private int currentSpinCount = 0; // 現在のスピンカウント
//     private bool canRotate = true; // 回転が許可されているかどうかのフラグ
//     private bool isInBonusState = false; // ボーナス状態かどうかのフラグ
//     private bool IsBattleWon = false; // 勝利フラグ
// private bool IsBattleLost = false; // 敗北フラグ


//     private void Start()
//     {
//         // ポイント表示を初期化（"0P" 表示）
//         UpdatePointDisplay();
//     }

//     private void Update()
//     {
//         if (Input.GetKeyDown(KeyCode.Space) && canRotate && !isInBonusState) // ボーナス状態でない場合
//         {
//             Debug.Log("今スペースを押してます");
//             StartAllReels();
//             rotationCount++; // 回転数をインクリメント

//             // 抽選処理を行う
//             CheckForBonusDraw();
//         }

//         if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
//         {
//             StopCurrentReel();
//         }
//     }

// private void CheckForBonusDraw()
// {
//     // スプライトごとに設定された確率で抽選
//     foreach (var effect in spriteEffects)
//     {
//         // 確率チェック（0.0〜1.0の範囲で比較）
//         if (Random.value < effect.hitProbability)
//         {
//             Debug.Log("抽選をしてるところ");
//             EnterBonusState(effect); // ボーナス状態に入る
//             break; // ボーナス状態に入ったらループを抜ける
//         }
//     }
// }


//     // スロットがボーナスに入るときに呼ばれるメソッド
//     public void EnterBonusState(SpriteEffect effect)
//     {
//         if (!isInBonusState)
//         {
//             isInBonusState = true;

//             // 登場時の動画を再生
//             videoPlayManager.PlayBonusVideo(effect.specialVideo);
//             Debug.Log("登場動画を再生");
            

//             // ボーナス動画が終了したときに処理を行うイベントを登録
//             videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += (vp) => OnBonusVideoEnd(effect);
//         }
//     }


//     private void OnBonusVideoEnd(SpriteEffect effect)
//     {
//         // イベントハンドラーを解除
//         videoPlayManager.GetComponent<VideoPlayer>().loopPointReached -= (vp) => OnBonusVideoEnd(effect);
//         Debug.Log("イベントハンドラを解除");


//         // ボーナス動画が終わったら戦闘動画を再生
//         PlayBattleVideo(effect);

//         // 戦闘中の回転を開始
//         StartBattleRotations(effect);
//     }

// private void PlayBattleVideo(SpriteEffect effect)
// {
//     // ボーナス時の戦闘中動画を再生
//     videoPlayManager.videoPlayer.clip = effect.battleVideo; // 戦闘中の動画を設定
//     videoPlayManager.videoPlayer.isLooping = true; // ループ再生を設定
//     videoPlayManager.videoPlayer.Play(); // 動画を再生
//     Debug.Log("戦闘中動画を再生");
// }


// private void StartBattleRotations(SpriteEffect effect)
// {
//     // 10回転分の処理を行う
//     for (int i = 0; i < 10; i++)
//     {
//         Debug.Log($"ボーナス中の回転 {i + 1} 回目");

//         // 通常のリール回転処理を呼び出す
//         StartAllReels();  // すべてのリールを回転させる
//         StopAllReels();   // リールを停止させる

//         // そろったかどうかを判定する
//         CheckBattleResults(effect);

//         // 勝利または敗北が決定した場合は、処理を終了
//         if (IsBattleWon || IsBattleLost)
//         {
//             break; // 10回転の途中で勝敗が決定したらループを抜ける
//         }
//     }
// }


//     private void CheckBattleResults(SpriteEffect effect)
//     {
//         // スプライトをチェックしてポイントを付与
//         Sprite firstSprite = reels[0].GetCurrentSprite(); // 最初のリールのスプライトを基準にする
//         bool matched = true;

//         foreach (var reel in reels)
//         {
//             if (reel.GetCurrentSprite() != firstSprite)
//             {
//                 matched = false; // 一致しなければフラグをfalseに
//                 break;
//             }
//         }

//         if (matched)
//         {
//             IsBattleWon = true; 
//             AddPoints(effect); // ポイントを追加
//             PlayVictoryVideo(effect); // 勝利動画を流す
        
            
//         }
//         else
//         {
//             IsBattleLost = false;
//             PlayDefeatVideo(effect); // 敗北動画を流す
//         }

//         // ボーナス状態を解除し、通常状態に戻る
//         isInBonusState = false;
//         UpdatePointDisplay(); // ポイント表示を更新
//     }

//     private void PlayVictoryVideo(SpriteEffect effect)
//     {
//         // 勝利時の動画を流す
//         videoPlayManager.PlayBonusVideo(effect.victoryVideo); // 勝利動画を再生
//     }

//     private void PlayDefeatVideo(SpriteEffect effect)
//     {
//         // 敗北時の動画を流す
//         videoPlayManager.PlayBonusVideo(effect.defeatVideo); // 敗北動画を再生
//     }

//     private void StartAllReels()
//     {
//         // 回転が許可されている場合のみリールを開始
//         if (!canRotate)
//         {
//             Debug.Log("リールの回転が禁止されているため、開始できません。");
//             return; // 回転が禁止されている場合はメソッドを終了
//         }

//         Debug.Log("全てのリールを開始します。");
//         foreach (var reel in reels)
//         {
//             if (reel != null)
//             {
//                 reel.StartReel();
//             }
//             else
//             {
//                 Debug.LogError("リールが `null` です。");
//             }
//         }
//         isReelActive = true;
//         currentReelIndex = 0;  // スタート時にインデックスをリセット
//     }

//     private void StopCurrentReel()
//     {
//         if (currentReelIndex < reels.Count)
//         {
//             var reel = reels[currentReelIndex];
//             if (reel != null)
//             {
//                 reel.StopReel();
//                 currentReelIndex++;
//             }
//             else
//             {
//                 Debug.LogError($"リール {currentReelIndex} が `null` です。");
//             }

//             if (currentReelIndex >= reels.Count)
//             {
//                 isReelActive = false;
//                 Debug.Log("全てのリールが停止しました。");
//                 CheckIfAllReelsMatch();
//             }
//         }
//     }

//     // ポイントを追加するメソッド
//     private void AddPoints(SpriteEffect effect)
//     {
//         playerPoints += effect.pointsForSprite; // スプライトに設定されたポイントを付与
//         UpdatePointDisplay(); // ポイント表示を更新
//     }

//     // ポイント表示を更新するメソッド
//     private void UpdatePointDisplay()
//     {
//         if (pointText != null)
//         {
//             pointText.text = playerPoints + "P"; // 例: "100P" のように表示
//         }
//     }

//     public void ResetPoints()
//     {
//         playerPoints = 0; // ポイントを0にリセット
//         UpdatePointDisplay(); // UIを更新して0ポイントを表示
//         Debug.Log("ポイントがリセットされました。");
//     }

//     // 回転の禁止と解放を切り替えるメソッド
//     public void ToggleReelRotation()
//     {
//         canRotate = !canRotate; // 回転の状態を反転
//         string state = canRotate ? "許可されました。" : "禁止されました。";
//         Debug.Log($"リールの回転が{state}");
//     }

//     private void StopAllReels()
//     {
//         foreach (var reel in reels)
//         {
//             if (reel != null)
//             {
//                 reel.StopReel(); // 各リールを停止
//             }
//             else
//             {
//                 Debug.LogError("リールが `null` です。");
//             }
//         }
//         isReelActive = false; // リールがアクティブでない状態にする
//         Debug.Log("全てのリールが停止しました。");
//     }

//     private void CheckIfAllReelsMatch()
//     {
//         if (reels.Count == 0) return;

//         Sprite firstSprite = reels[0].GetCurrentSprite(); // 最初のリールのスプライトを基準にする
//         Debug.Log($"基準スプライト: {firstSprite.name}");

//         foreach (var reel in reels)
//         {
//             var currentSprite = reel.GetCurrentSprite();
//             Debug.Log($"リールのスプライト: {currentSprite.name}");

//             if (currentSprite != firstSprite)
//             {
//                 Debug.Log("一致しません。");
//                 return; // 一致しない場合は処理を終了
//             }
//         }

//         Debug.Log("全てのリールが一致しました！");
//     }
// }


// using UnityEngine;
// using TMPro;
// using UnityEngine.Video;
// using System.Collections.Generic;

// public class ReelManager : MonoBehaviour
// {
//     [SerializeField] private List<Reel> reels; // リールのリスト
//     [SerializeField] private VideoPlaybackController videoPlayManager; // 動画再生を管理するオブジェクト
//     [SerializeField] private AudioSource winSound; // 勝利音声
//     [SerializeField] private TextMeshProUGUI pointText; // ポイントを表示するTextMeshProUGUI
//     [SerializeField] private OuterFrameEffect outerFrameEffect; // 外枠エフェクト
//     [SerializeField] private List<SpriteEffect> spriteEffects; // スプライトごとの演出リスト

//     private int currentReelIndex = 0; // 現在のリールインデックス
//     private bool isReelActive = false; // リールのアクティブ状態
//     private int playerPoints = 0; // プレイヤーのポイント
//     private int rotationCount = 0; // 回転数
//     private int currentSpinCount = 0; // 現在のスピンカウント
//     private bool canRotate = true; // 回転の許可フラグ
//     private bool isInBonusState = false; // ボーナス状態かどうか
//     private SpriteEffect effect; // 現在使用しているスプライトエフェクト

//     private void Start()
//     {
//         UpdatePointDisplay(); // ポイント表示を初期化
//     }

//     private void Update()
//     {
//         HandleInput(); // 入力処理
//     }

//     private void HandleInput()
//     {
//         // スペースキーによるスピン
//         if (Input.GetKeyDown(KeyCode.Space) && canRotate)
//         {
//             if (isInBonusState)
//             {
//                 Debug.Log("ボーナス状態のスピン");
//                 PerformSpin();
//                 CheckBattleResults(); // 戦闘結果を確認
//             }
//             else
//             {
//                 Debug.Log("通常状態のスピン");
//                 PerformSpin();
//                 CheckForBonusDraw(); // ボーナス抽選を行う
//             }
//         }

//         // Enterキーでリールを停止
//         if (Input.GetKeyDown(KeyCode.Return) && isReelActive)
//         {
//             StopCurrentReel(); // 現在のリールを停止
//         }
//     }

//     private void PerformSpin()
//     {
//         StartAllReels(); // 全てのリールを開始
//         rotationCount++; // 回転数をインクリメント
//         currentSpinCount++; // 現在のスピンカウントをインクリメント
//     }

//     private void CheckForBonusDraw()
//     {
//         foreach (var spriteEffect in spriteEffects)
//         {
//             // 確率によるボーナス抽選
//             if (Random.value < spriteEffect.hitProbability)
//             {
//                 effect = spriteEffect; // ボーナスエフェクトを設定
//                 Debug.Log("ボーナス状態に入ります");
//                 EnterBonusState(effect); // ボーナス状態に入る
//                 break; // 一度ボーナス状態に入ったらループを抜ける
//             }
//         }
//     }

//     private void EnterBonusState(SpriteEffect effect)
//     {
//         if (!isInBonusState)
//         {
//             isInBonusState = true;

//             // 登場動画を再生
//             videoPlayManager.PlayBonusVideo(effect.specialVideo);
//             Debug.Log("ボーナス動画を再生中...");

//             // ボーナス動画が終了したときの処理
//             videoPlayManager.GetComponent<VideoPlayer>().loopPointReached += (vp) => OnBonusVideoEnd(effect);
//         }
//     }

//     private void OnBonusVideoEnd(SpriteEffect effect)
//     {
//         // イベントハンドラを解除
//         videoPlayManager.GetComponent<VideoPlayer>().loopPointReached -= (vp) => OnBonusVideoEnd(effect);
//         Debug.Log("ボーナス動画が終了しました。戦闘動画を再生します。");

//         // 戦闘動画を再生
//         PlayBattleVideo(effect);
//         StartBattleRotations(); // 戦闘ローテーションを開始
//     }

//     private void PlayBattleVideo(SpriteEffect effect)
//     {
//         videoPlayManager.videoPlayer.clip = effect.battleVideo; // 戦闘動画を設定
//         videoPlayManager.videoPlayer.isLooping = true; // ループ再生
//         videoPlayManager.videoPlayer.Play(); // 動画を再生
//         Debug.Log("戦闘動画を再生中...");
//     }

//     private void StartBattleRotations()
//     {
//         currentSpinCount = 0; // スピンカウントをリセット

//         // 10回のスピンを実行
//         for (int i = 0; i < 10; i++)
//         {
//             currentSpinCount++;
//             Debug.Log($"戦闘中の回転 {currentSpinCount} 回目");
//             CheckBattleResults(); // 結果を確認
//         }
//     }

//     private void CheckBattleResults()
//     {
//         if (CheckIfAllReelsMatch())
//         {
//             AddPoints(effect); // ポイントを追加
//             PlayVictoryVideo(effect); // 勝利動画を再生
//         }
//         else
//         {
//             PlayDefeatVideo(effect); // 敗北動画を再生
//         }

//         // ボーナス状態を解除し、通常状態に戻る
//         isInBonusState = false;
//         UpdatePointDisplay(); // ポイント表示を更新
//     }

//     private bool CheckIfAllReelsMatch()
//     {
//         Sprite firstSprite = reels[0].GetCurrentSprite(); // 最初のリールを基準に
//         foreach (var reel in reels)
//         {
//             if (reel.GetCurrentSprite() != firstSprite)
//             {
//                 return false; // 一致しない場合はfalseを返す
//             }
//         }
//         return true; // 一致する場合はtrueを返す
//     }

//     private void PlayVictoryVideo(SpriteEffect effect)
//     {
//         videoPlayManager.PlayBonusVideo(effect.victoryVideo); // 勝利動画を再生
//     }

//     private void PlayDefeatVideo(SpriteEffect effect)
//     {
//         videoPlayManager.PlayBonusVideo(effect.defeatVideo); // 敗北動画を再生
//     }

//     private void StartAllReels()
//     {
//         if (!canRotate)
//         {
//             Debug.Log("リールの回転が禁止されています。");
//             return; // 回転禁止の場合は終了
//         }

//         Debug.Log("全てのリールを開始します。");
//         foreach (var reel in reels)
//         {
//             reel?.StartReel(); // 各リールを開始
//         }
//         isReelActive = true; // リールがアクティブに
//         currentReelIndex = 0; // インデックスをリセット
//     }

//     private void StopCurrentReel()
//     {
//         if (currentReelIndex < reels.Count)
//         {
//             var reel = reels[currentReelIndex];
//             reel?.StopReel(); // 現在のリールを停止
//             currentReelIndex++; // インデックスをインクリメント

//             if (currentReelIndex >= reels.Count)
//             {
//                 isReelActive = false; // 全てのリールが停止
//                 Debug.Log("全てのリールが停止しました。");
//             }
//         }
//     }

//     private void AddPoints(SpriteEffect effect)
//     {
//         playerPoints += effect.pointsForSprite; // ポイントを追加
//         UpdatePointDisplay(); // ポイント表示を更新
//         winSound.Play(); // 勝ち音を再生
//         Debug.Log($"現在のポイント: {playerPoints}");
//     }

//     private void UpdatePointDisplay()
//     {
//         pointText.text = $"{playerPoints}P"; // ポイントをUIに表示
//     }

//         public void ResetPoints()
//     {
//         playerPoints = 0; // ポイントを0にリセット
//         UpdatePointDisplay(); // UIを更新して0ポイントを表示
//         Debug.Log("ポイントがリセットされました。");
//     }
// }
