package nemosofts.streambox.activity;

import android.app.Application;
import android.content.Context;
import android.content.ContextWrapper;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.StrictMode;

import androidx.annotation.Nullable;
import androidx.multidex.MultiDex;
import androidx.nemosofts.optimized.PicassoOptimized;

import com.google.firebase.analytics.FirebaseAnalytics;
import com.onesignal.OneSignal;
import com.squareup.picasso.Picasso;

import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.atomic.AtomicBoolean;

import nemosofts.streambox.BuildConfig;
import nemosofts.streambox.R;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.BlockingContextWrapper;
import nemosofts.streambox.utils.DialogBlockerCallback;
import nemosofts.streambox.utils.helper.DBHelper;
import nemosofts.streambox.utils.helper.Helper;
import nemosofts.streambox.utils.OneSignalNotificationHandler;

public class MyApplication extends Application {

    private static final String TAG = "MyApplication";
    private static final String BLOCKED_DOMAIN = "nemosofts.com";

    // Override startActivity to block nemosofts.com Custom Tabs
    @Override
    public void startActivity(Intent intent) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity (Application): " + getIntentUrl(intent), null);
            return;
        }
        super.startActivity(intent);
    }

    @Override
    public void startActivity(Intent intent, @Nullable Bundle options) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity with options (Application): " + getIntentUrl(intent), null);
            return;
        }
        super.startActivity(intent, options);
    }

    private boolean shouldBlockIntent(Intent intent) {
        if (intent == null) return false;
        
        String url = getIntentUrl(intent);
        if (url != null && url.contains(BLOCKED_DOMAIN)) {
            return true;
        }
        
        // Check action and data
        String action = intent.getAction();
        if (Intent.ACTION_VIEW.equals(action)) {
            Uri data = intent.getData();
            if (data != null) {
                String host = data.getHost();
                String dataString = data.toString();
                if ((host != null && host.contains(BLOCKED_DOMAIN)) || dataString.contains(BLOCKED_DOMAIN)) {
                    return true;
                }
            }
        }
        
        // Check extras
        Bundle extras = intent.getExtras();
        if (extras != null) {
            for (String key : extras.keySet()) {
                Object value = extras.get(key);
                if (value instanceof String && ((String) value).contains(BLOCKED_DOMAIN)) {
                    return true;
                } else if (value instanceof Uri && value.toString().contains(BLOCKED_DOMAIN)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    private String getIntentUrl(Intent intent) {
        if (intent == null) return null;
        Uri data = intent.getData();
        return data != null ? data.toString() : null;
    }

    private final AtomicBoolean databaseInitScheduled = new AtomicBoolean(false);
    private final AtomicBoolean databaseReady = new AtomicBoolean(false);

    @Override
    public void onCreate() {
        super.onCreate();

        // Block nemosofts verification dialog - MUST be first!
        registerActivityLifecycleCallbacks(new DialogBlockerCallback());

        // Analytics Initialization
        FirebaseAnalytics.getInstance(getApplicationContext());

        StrictMode.VmPolicy.Builder builder = new StrictMode.VmPolicy.Builder();
        StrictMode.setVmPolicy(builder.build());

        initDatabase();

        // OneSignal Initialization
        OneSignal.initWithContext(this, getString(R.string.onesignal_app_id));

        // Create high-importance notification channel for heads-up popup display
        OneSignalNotificationHandler.createNotificationChannel(this);

        initPicasso();

        new Helper(getApplicationContext()).initializeAds();
    }

    private void initDatabase() {
        if (databaseReady.get() || databaseInitScheduled.get()) {
            return;
        }

        if (!databaseInitScheduled.compareAndSet(false, true)) {
            return;
        }

        final ExecutorService executor = Executors.newSingleThreadExecutor();
        executor.execute(() -> {
            try {
                DBHelper dbHelper = new DBHelper(getApplicationContext());
                dbHelper.getWritableDatabase();
                dbHelper.close();
                databaseReady.set(true);
            } catch (Exception initializationError) {
                databaseInitScheduled.set(false);
                ApplicationUtil.log("MyApplication", "Database initialization failed", initializationError);
            } finally {
                executor.shutdown();
            }
        });
    }

    private void initPicasso() {
        try {
            Picasso.setSingletonInstance(PicassoOptimized.create(getApplicationContext()));
        } catch (IllegalStateException alreadySet) {
            ApplicationUtil.log("MyApplication", "Picasso already initialized, using existing instance", alreadySet);
        }
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
}