package androidx.nemosofts;

import android.content.Context;

/**
 * Mock LauncherTask class that overrides the library's verification task.
 * This class provides empty implementations to bypass the license verification.
 * 
 * When this class exists in the app's source code, it will take precedence
 * over the library's compiled LauncherTask class.
 */
public class LauncherTask {

    private LauncherListener listener;
    private Context context;

    /**
     * Constructor.
     * @param context Application context
     * @param listener Callback listener
     */
    public LauncherTask(Context context, LauncherListener listener) {
        this.context = context;
        this.listener = listener;
    }

    /**
     * Execute the verification task - immediately reports success.
     */
    public void execute() {
        // Skip verification and immediately call onConnected
        if (listener != null) {
            listener.onStartPairing();
            listener.onConnected();
        }
    }

    /**
     * Cancel the task.
     */
    public void cancel() {
        // No-op
    }
}
