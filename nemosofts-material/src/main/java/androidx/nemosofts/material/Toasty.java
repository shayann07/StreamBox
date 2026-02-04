package androidx.nemosofts.material;

import android.content.Context;
import android.widget.Toast;

/**
 * Shadow Toasty class that replaces the compiled library version.
 * Uses standard Android Toast instead of custom dialogs to avoid blocking UI.
 */
public class Toasty {
    
    public static final int SUCCESS = 0;
    public static final int ERROR = 1;
    public static final int WARNING = 2;
    public static final int INFO = 3;
    public static final int NORMAL = 4;
    
    /**
     * Simple toast - no dialog, no blocking UI
     */
    public static void makeText(Context context, String message, int type) {
        if (context != null && message != null && !message.isEmpty()) {
            Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
        }
    }
    
    public static void makeText(Context context, boolean showIcon, String message, int type) {
        if (context != null && message != null && !message.isEmpty()) {
            Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
        }
    }
    
    public static void makeText(Context context, CharSequence message, int type) {
        if (context != null && message != null && message.length() > 0) {
            Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
        }
    }
    
    public static void makeText(Context context, int resId, int type) {
        if (context != null) {
            Toast.makeText(context, resId, Toast.LENGTH_SHORT).show();
        }
    }
    
    // Additional overloads that might exist
    public static void show(Context context, String message, int type) {
        makeText(context, message, type);
    }
    
    public static void success(Context context, String message) {
        makeText(context, message, SUCCESS);
    }
    
    public static void error(Context context, String message) {
        makeText(context, message, ERROR);
    }
    
    public static void warning(Context context, String message) {
        makeText(context, message, WARNING);
    }
    
    public static void info(Context context, String message) {
        makeText(context, message, INFO);
    }
}
