package nemosofts.streambox.utils.advertising;

import android.content.Context;

import androidx.annotation.NonNull;

import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.ads.rewarded.RewardedAd;
import com.google.android.gms.ads.rewarded.RewardedAdLoadCallback;

import nemosofts.streambox.callback.Callback;

public record RewardAdAdmob(Context ctx) {

    static RewardedAd rewardedAd;

    public void createAd() {
        // Ads Disabled
    }

    public RewardedAd getAd() {
        return rewardedAd;
    }

    public static void setAd(RewardedAd rewardAd) {
        rewardedAd = rewardAd;
    }
}