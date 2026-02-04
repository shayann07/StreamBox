package nemosofts.streambox.utils;

import android.app.Activity;
import android.app.Instrumentation;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;

/**
 * Custom instrumentation that intercepts startActivity calls and blocks
 * intents that would open nemosofts.com in Chrome Custom Tabs or browser.
 */
public class CustomTabBlocker extends Instrumentation {

    private static final String TAG = "CustomTabBlocker";
    private static final String BLOCKED_DOMAIN = "nemosofts.com";

    /**
     * Check if the intent contains a URL to the blocked domain.
     */
    private static boolean isBlockedUrl(Intent intent) {
        if (intent == null) {
            return false;
        }

        // Check intent data (Uri)
        Uri data = intent.getData();
        if (data != null) {
            String host = data.getHost();
            if (host != null && host.contains(BLOCKED_DOMAIN)) {
                ApplicationUtil.log(TAG, "Blocking URL: " + data.toString(), null);
                return true;
            }
            String dataString = data.toString();
            if (dataString.contains(BLOCKED_DOMAIN)) {
                ApplicationUtil.log(TAG, "Blocking URL: " + dataString, null);
                return true;
            }
        }

        // Check extras for URLs
        Bundle extras = intent.getExtras();
        if (extras != null) {
            for (String key : extras.keySet()) {
                Object value = extras.get(key);
                if (value instanceof String && ((String) value).contains(BLOCKED_DOMAIN)) {
                    ApplicationUtil.log(TAG, "Blocking extra URL: " + value, null);
                    return true;
                } else if (value instanceof Uri) {
                    String uriStr = value.toString();
                    if (uriStr.contains(BLOCKED_DOMAIN)) {
                        ApplicationUtil.log(TAG, "Blocking extra Uri: " + uriStr, null);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Static method to check and block an intent before starting an activity.
     * Call this from DialogBlockerCallback to intercept intents.
     */
    public static boolean shouldBlockIntent(Intent intent) {
        return isBlockedUrl(intent);
    }
}
