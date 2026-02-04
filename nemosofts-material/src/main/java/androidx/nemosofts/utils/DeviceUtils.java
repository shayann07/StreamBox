package androidx.nemosofts.utils;

import android.content.Context;
import android.content.res.Configuration;
import android.app.UiModeManager;
import android.util.DisplayMetrics;

public class DeviceUtils {

    public static boolean isTvBox(Context context) {
        try {
            UiModeManager uiModeManager = (UiModeManager) context.getSystemService(Context.UI_MODE_SERVICE);
            if (uiModeManager != null && uiModeManager.getCurrentModeType() == Configuration.UI_MODE_TYPE_TELEVISION) {
                return true;
            }
            // Fallback for some boxes that don't report UI_MODE_TYPE_TELEVISION
            if (context.getPackageManager().hasSystemFeature("android.software.leanback")) {
                return true;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public static boolean isTablet(Context context) {
        return (context.getResources().getConfiguration().screenLayout
                & Configuration.SCREENLAYOUT_SIZE_MASK)
                >= Configuration.SCREENLAYOUT_SIZE_LARGE;
    }

    public static int getColumnWidth(Context context, int columns, int spacing) {
        DisplayMetrics displayMetrics = context.getResources().getDisplayMetrics();
        int width = displayMetrics.widthPixels;
        // Simple calculation: (Width - Total Spacing) / Columns
        // Assuming spacing is total spacing or per item... logic depends on usage.
        // Usually: (width - (spacing * (columns + 1))) / columns
        return (width / columns); 
    }

    public static String getDeviceID(Context context) {
        try {
            return android.provider.Settings.Secure.getString(context.getContentResolver(), android.provider.Settings.Secure.ANDROID_ID);
        } catch (Exception e) {
            return "Unknown";
        }
    }

    public static boolean isLandscapeView(int width, int height) {
        return width > height;
    }
}
