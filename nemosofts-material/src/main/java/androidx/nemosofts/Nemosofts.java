package androidx.nemosofts;

import android.content.Context;

/**
 * Mock Nemosofts class that overrides the library's verification class.
 * This class provides empty implementations of the verification methods
 * to bypass the license verification dialog.
 * 
 * When this class exists in the app's source code, it will take precedence
 * over the library's compiled Nemosofts class due to Android's class loading order.
 */
public class Nemosofts {

    /**
     * Constructor - does not perform any verification.
     * @param context Application context (unused)
     */
    public Nemosofts(Context context) {
        // No-op: bypass verification
    }

    /**
     * Set verification code - does not perform any verification.
     * @param code Verification code (unused)
     */
    public void setVerificationCode(String code) {
        // No-op: bypass verification
    }

    /**
     * Check if app is verified - always returns true.
     * @return Always true to bypass verification
     */
    public boolean isVerified() {
        return true;
    }

    /**
     * Get verification status - always returns success.
     * @return Empty string or success status
     */
    public String getStatus() {
        return "";
    }

    /**
     * Initialize the library - does not perform any verification.
     */
    public void init() {
        // No-op: bypass verification
    }

    /**
     * Any additional methods that the library might call.
     */
    public void setEnvatoApiKey(String key) {
        // No-op: bypass verification
    }

    public boolean checkLicense() {
        return true;
    }

    public void showDialog() {
        // No-op: prevent dialog from showing
    }
}
