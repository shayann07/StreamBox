package nemosofts.streambox.utils.helper;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;
import androidx.nemosofts.utils.DeviceUtils;
import androidx.nemosofts.utils.EncrypterUtils;
import androidx.nemosofts.utils.NetworkUtils;

import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.FullScreenContentCallback;
import com.google.android.gms.ads.MobileAds;
import com.google.gson.JsonObject;

import org.jetbrains.annotations.NotNull;

import java.io.File;
import java.util.Objects;

import nemosofts.streambox.R;
import nemosofts.streambox.activity.DownloadService;
import nemosofts.streambox.callback.Callback;
import nemosofts.streambox.callback.Method;
import nemosofts.streambox.interfaces.InterAdListener;
import nemosofts.streambox.interfaces.RewardAdListener;
import nemosofts.streambox.item.ItemVideoDownload;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.advertising.AdManagerInterAdmob;
import nemosofts.streambox.utils.advertising.GDPRChecker;
import nemosofts.streambox.utils.advertising.RewardAdAdmob;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;

public class Helper {

    private final Context ctx;
    private InterAdListener interAdListener;
    boolean isRewarded = false;

    public Helper(Context ctx) {
        this.ctx = ctx;
    }

    public Helper(Context ctx, InterAdListener interAdListener) {
        this.ctx = ctx;
        this.interAdListener = interAdListener;
    }

    public void initializeAds() {
        // Ads Disabled
    }

    public Object showBannerAd(LinearLayout linearLayout, Boolean isShowAd) {
        // Ads Disabled
        return null;
    }

    public void showInterAd(final int pos, final String type) {
        // Ads Disabled - Directly execute listener
        if (interAdListener != null) {
            interAdListener.onClick(pos, type);
        }
    }

    public void loadRewardAds(RewardAdListener rewardAdListener, boolean playWhenReady) {
        // Ads Disabled - Directly execute listener
        if (rewardAdListener != null) {
            rewardAdListener.isPlaying(playWhenReady);
        }
    }

    public void showRewardAds(Boolean isShowAd, Boolean playWhenReady, RewardAdListener rewardAdListener) {
        // Ads Disabled - Directly execute listener
        if (rewardAdListener != null) {
            rewardAdListener.isPlaying(playWhenReady);
        }
    }

    private boolean isBannerAd() {
        return false;
    }

    private boolean isInterAd() {
        return false;
    }

    public RequestBody getAPIRequestNSofts(String helperName, String reportTitle,
                                           String reportMessages, String userName, String userPass) {
        JsonObject jsObj = new JsonObject();
        jsObj.addProperty("helper_name", helperName);
        jsObj.addProperty("application_id", ctx.getPackageName());
        if (Method.METHOD_REPORT.equals(helperName)) {
            jsObj.addProperty("user_name", userName);
            jsObj.addProperty("user_pass", userPass);
            jsObj.addProperty("report_title", reportTitle);
            jsObj.addProperty("report_msg", reportMessages);
        } else if (Method.METHOD_GET_DEVICE_ID.equals(helperName)) {
            jsObj.addProperty("device_id", userPass);
        } else if (Method.METHOD_GET_ACTIVATION_CODE.equals(helperName)) {
            jsObj.addProperty("activation_code", userPass);
        } else if (Method.METHOD_POSTER.equals(helperName)) {
            jsObj.addProperty("poster_type", reportTitle);
        } else if (Method.METHOD_GET_TRIAL.equals(helperName) || Method.METHOD_ADD_TRIAL.equals(helperName)) {
            jsObj.addProperty("device_id", reportTitle);
        }

        return new MultipartBody.Builder()
            .setType(MultipartBody.FORM)
            .addFormDataPart("data", EncrypterUtils.toBase64(jsObj.toString()))
            .build();
    }

    public void download(final ItemVideoDownload itemDownload, String table) {
        File root = new File(Objects.requireNonNull(ctx.getExternalFilesDir("")).getAbsolutePath() + File.separator + "/temp");
        if (!root.exists() && !root.mkdirs()) {
            ApplicationUtil.log("Helper", "Failed to create temp directory");
            return;
        }

        if (itemDownload != null) {
            itemDownload.setDownloadTable(table);
        }

        String a = String.valueOf(System.currentTimeMillis());
        String name = ApplicationUtil.getRandomValue((999999 - 100000) + 100000) + a.substring(a.length() - 6, a.length() - 1);

        File file = new File(root, name + ApplicationUtil.containerExtension(itemDownload.getContainerExtension()));

        new nemosofts.streambox.utils.AsyncTaskExecutor<Void, Void, Boolean>() {
            @Override
            protected Boolean doInBackground(Void params) {
                DBHelper dbHelper = new DBHelper(ctx);
                try {
                    return dbHelper.checkDownload(table, itemDownload.getStreamID(),
                            ApplicationUtil.containerExtension(itemDownload.getContainerExtension()));
                } finally {
                    dbHelper.close();
                }
            }

            @Override
            protected void onPostExecute(Boolean isDownloaded) {
                if (Boolean.FALSE.equals(isDownloaded)) {
                    if (DownloadService.getInstance() == null){
                        return;
                    }

                    Intent serviceIntent = new Intent(ctx, DownloadService.class);
                    serviceIntent.setAction(Boolean.FALSE.equals(DownloadService.isDownloading())
                            ? DownloadService.ACTION_START
                            : DownloadService.ACTION_ADD
                    );
                    serviceIntent.putExtra("downloadUrl", itemDownload.getVideoURL());
                    serviceIntent.putExtra("file_path", root.toString());
                    serviceIntent.putExtra("file_name", file.getName());
                    serviceIntent.putExtra("file_container", ApplicationUtil.containerExtension(itemDownload.getContainerExtension()));
                    serviceIntent.putExtra("item", itemDownload);
                    ContextCompat.startForegroundService(ctx, serviceIntent);
                } else {
                    Toast.makeText(ctx, ctx.getResources().getString(R.string.already_download), Toast.LENGTH_SHORT).show();
                }
            }
        }.execute();
    }
}
