package nemosofts.streambox.utils.advertising;

import android.content.Context;
import android.os.Bundle;

import androidx.annotation.NonNull;

import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.interstitial.InterstitialAd;
import com.google.android.gms.ads.interstitial.InterstitialAdLoadCallback;

import nemosofts.streambox.callback.Callback;

public record AdManagerInterAdmob(Context ctx) {

    private static InterstitialAd interAd;

    public void createAd() {
        // Ads Disabled
    }

    public InterstitialAd getAd() {
        return interAd;
    }

    public static void setAd(InterstitialAd interstitialAd) {
        interAd = interstitialAd;
    }
}