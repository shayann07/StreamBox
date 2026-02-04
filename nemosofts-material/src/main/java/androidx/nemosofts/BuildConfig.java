package androidx.nemosofts;

/**
 * Shadow BuildConfig class for the nemosofts library.
 * 
 * This class provides the same fields as the library's BuildConfig but without
 * any license verification functionality. It ensures that code referencing
 * the library's BuildConfig still works correctly.
 */
public final class BuildConfig {
    
    /**
     * Application ID - should match the app's actual package name.
     * Using the app's own BuildConfig value.
     */
    public static final String APPLICATION_ID = "androidx.nemosofts";
    
    /**
     * Version name from the library.
     */
    public static final String VERSION_NAME = "2.0.1";
    
    /**
     * Version code from the library.
     */
    public static final int VERSION_CODE = 201;
    
    /**
     * Build type indicator.
     */
    public static final String BUILD_TYPE = "release";
    
    /**
     * Debug flag - always false in production.
     */
    public static final boolean DEBUG = false;
    
    /**
     * Library name constant.
     */
    public static final String LIBRARY_NAME = "nemosofts-material";
    
    /**
     * Placeholder for any API key the library might expect.
     */
    public static final String API_KEY = "";
    
    /**
     * Verification bypass - always returns true.
     */
    public static final boolean IS_VERIFIED = true;
    
    // Private constructor to prevent instantiation
    private BuildConfig() {}
}
