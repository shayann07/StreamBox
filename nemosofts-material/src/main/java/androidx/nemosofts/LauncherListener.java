package androidx.nemosofts;

/**
 * Mock LauncherListener interface that overrides the library's verification interface.
 */
public interface LauncherListener {
    
    /**
     * Called when the verification/pairing process starts.
     */
    void onStartPairing();

    /**
     * Called when verification is successful and the app is connected.
     */
    void onConnected();

    /**
     * Called when there's an error during verification.
     * @param message Error message
     */
    void onError(String message);
}
