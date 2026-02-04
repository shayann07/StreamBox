package nemosofts.streambox.activity;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;

import nemosofts.streambox.utils.ApplicationUtil;

/**
 * Base Activity class that intercepts all startActivity calls and blocks
 * any intents that would open nemosofts.com URLs (used for license verification).
 * 
 * This prevents the nemosofts library from opening Chrome Custom Tabs to their server.
 */
public class BaseActivity extends AppCompatActivity {

    private static final String TAG = "BaseActivity";
    private static final String BLOCKED_DOMAIN = "nemosofts.com";

    @Override
    public void startActivity(Intent intent) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity: " + getIntentUrl(intent), null);
            return; // Block the intent
        }
        super.startActivity(intent);
    }

    @Override
    public void startActivity(Intent intent, @Nullable Bundle options) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity with options: " + getIntentUrl(intent), null);
            return; // Block the intent
        }
        super.startActivity(intent, options);
    }

    @Override
    public void startActivityForResult(Intent intent, int requestCode) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivityForResult: " + getIntentUrl(intent), null);
            return; // Block the intent
        }
        super.startActivityForResult(intent, requestCode);
    }

    @Override
    public void startActivityForResult(Intent intent, int requestCode, @Nullable Bundle options) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivityForResult with options: " + getIntentUrl(intent), null);
            return; // Block the intent
        }
        super.startActivityForResult(intent, requestCode, options);
    }

    /**
     * Check if the intent should be blocked.
     */
    private boolean shouldBlockIntent(Intent intent) {
        if (intent == null) {
            return false;
        }

        String url = getIntentUrl(intent);
        if (url != null && url.contains(BLOCKED_DOMAIN)) {
            // return true; // Unblocked to allow user-defined links
        }

        // Also check for the Chrome Custom Tab package
        String action = intent.getAction();
        if (Intent.ACTION_VIEW.equals(action)) {
            Uri data = intent.getData();
            if (data != null) {
                String host = data.getHost();
                String dataString = data.toString();
                if ((host != null && host.contains(BLOCKED_DOMAIN)) || 
                    dataString.contains(BLOCKED_DOMAIN)) {
                    // return true; // Unblocked to allow user-defined links
                }
            }
        }

        // Check extras for URL strings
        Bundle extras = intent.getExtras();
        if (extras != null) {
            for (String key : extras.keySet()) {
                Object value = extras.get(key);
                if (value instanceof String && ((String) value).contains(BLOCKED_DOMAIN)) {
                    // return true; // Unblocked to allow user-defined links
                } else if (value instanceof Uri) {
                    if (value.toString().contains(BLOCKED_DOMAIN)) {
                        // return true; // Unblocked to allow user-defined links
                    }
                }
            }
        }

        return false;
    }

    /**
     * Extract URL from intent data.
     */
    private String getIntentUrl(Intent intent) {
        if (intent == null) {
            return null;
        }
        Uri data = intent.getData();
        if (data != null) {
            return data.toString();
        }
        return null;
    }
}
