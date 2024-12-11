using UnityEngine;
using TMPro;

public class StatsManager : MonoBehaviour
{
[SerializeField] private TextMeshProUGUI totalSpinsText;
[SerializeField] private TextMeshProUGUI winRateText;

private int totalSpins = 0;
private int winCount = 0;

public void IncrementSpins()
{
    totalSpins++;
    UpdateDisplay();
}

public void IncrementWins()
{
    winCount++;
    UpdateDisplay();
}

private void UpdateDisplay()
{
    totalSpinsText.text = $"{totalSpins}";

    if (winCount > 0)
    {
        int winRate = totalSpins / winCount;
        winRateText.text = $"1/{winRate}";
    }
    else
    {
        winRateText.text = "-";
    }
}
}