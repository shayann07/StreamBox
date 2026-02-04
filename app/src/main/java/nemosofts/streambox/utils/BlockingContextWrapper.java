package nemosofts.streambox.utils;

import android.content.Context;
import android.content.ContextWrapper;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.Nullable;

/**
 * A Context wrapper that intercepts startActivity calls to block URLs to nemosofts.com.
 * This is used to wrap the base context and prevent the nemosofts library from opening
 * Chrome Custom Tabs to their domain for license verification.
 */
public class BlockingContextWrapper extends ContextWrapper {
    
    private static final String TAG = "BlockingContextWrapper";
    private static final String BLOCKED_DOMAIN = "nemosofts.com";

    public BlockingContextWrapper(Context base) {
        super(base);
    }

    @Override
    public void startActivity(Intent intent) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity: " + getIntentUrl(intent), null);
            return;
        }
        super.startActivity(intent);
    }

    @Override
    public void startActivity(Intent intent, @Nullable Bundle options) {
        if (shouldBlockIntent(intent)) {
            ApplicationUtil.log(TAG, "Blocked startActivity with options: " + getIntentUrl(intent), null);
            return;
        }
        super.startActivity(intent, options);
    }

    @Override
    public void startActivities(Intent[] intents) {
        for (Intent intent : intents) {
            if (shouldBlockIntent(intent)) {
                ApplicationUtil.log(TAG, "Blocked startActivities: " + getIntentUrl(intent), null);
                return;
            }
        }
        super.startActivities(intents);
    }

    @Override
    public void startActivities(Intent[] intents, @Nullable Bundle options) {
        for (Intent intent : intents) {
            if (shouldBlockIntent(intent)) {
                ApplicationUtil.log(TAG, "Blocked startActivities with options: " + getIntentUrl(intent), null);
                return;
            }
        }
        super.startActivities(intents, options);
    }

    private String getIntentUrl(Intent intent) {
        if (intent == null) return null;
        Uri data = intent.getData();
        return data != null ? data.toString() : null;
    }

    /**
     * Determines if an intent should be blocked based on its URL content or Component name.
     */
    public static boolean shouldBlockIntent(Intent intent) {
        if (intent == null) return false;

        try {
            // Check Component Name (Explicit Intents)
            if (intent.getComponent() != null) {
                String className = intent.getComponent().getClassName();
                if (className.contains("nemosofts") || className.contains("License") || className.contains("verification")) {
                    return true;
                }
            }

            // Check the intent data URI
            Uri data = intent.getData();
            if (data != null) {
                String url = data.toString();
                if (url.contains(BLOCKED_DOMAIN)) {
                    return true;
                }
                String host = data.getHost();
                if (host != null && host.contains(BLOCKED_DOMAIN)) {
                    return true;
                }
            }

            // Check the action
            String action = intent.getAction();
            if (Intent.ACTION_VIEW.equals(action) && data != null) {
                String url = data.toString();
                if (url.contains(BLOCKED_DOMAIN)) {
                    return true;
                }
            }

            // Check all extras for URL strings
            Bundle extras = intent.getExtras();
            if (extras != null) {
                for (String key : extras.keySet()) {
                    Object value = extras.get(key);
                    if (value instanceof String) {
                        String strValue = (String) value;
                        if (strValue.contains(BLOCKED_DOMAIN)) {
                            return true;
                        }
                    } else if (value instanceof Uri) {
                        if (value.toString().contains(BLOCKED_DOMAIN)) {
                            return true;
                        }
                    }
                }
            }

        } catch (Exception e) {
            // Ignore any exceptions during intent inspection
        }

        return false;
    }
}
