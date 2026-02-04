package nemosofts.streambox.utils;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Application;
import android.app.Dialog;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.webkit.WebView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import java.lang.reflect.Field;
import java.lang.reflect.Method;
import java.util.ArrayList;

import nemosofts.streambox.activity.WebActivity;

/**
 * ActivityLifecycleCallbacks that blocks the nemosofts license verification dialog.
 * 
 * This callback monitors all activities and:
 * 1. Intercepts startActivity calls to block Custom Tabs to nemosofts.com
 * 2. Automatically dismisses any dialogs containing nemosofts.com content
 * 3. Closes any activities that are launched to show verification content
 */
public class DialogBlockerCallback implements Application.ActivityLifecycleCallbacks {

    private static final String TAG = "DialogBlockerCallback";
    private static final String BLOCKED_DOMAIN = "nemosofts.com";
    private static final int RECURSION_DEPTH = 10;
    private final Handler handler = new Handler(Looper.getMainLooper());
    private Activity currentActivity;
    private boolean isMonitoring = false;
    
    // CONTINUOUS BACKGROUND MONITOR - runs every 2000ms
    private final Runnable continuousMonitor = new Runnable() {
        @Override
        public void run() {
            if (isMonitoring && currentActivity != null) {
                // Throttle checks slightly but keep them frequent enough to catch dialogs before they flicker
                long startTime = System.currentTimeMillis();
                
                dismissBlockedDialogs(currentActivity);
                
                long duration = System.currentTimeMillis() - startTime;
                if (duration > 5) {
                    ApplicationUtil.log(TAG, "Dialog check took " + duration + "ms", null);
                }

                handler.postDelayed(this, 500); // Check every 500ms (Reverted from 2000ms)
            }
        }
    };

    @Override
    public void onActivityCreated(@NonNull Activity activity, @Nullable Bundle savedInstanceState) {
        checkAndBlockActivity(activity);
    }

    @Override
    public void onActivityStarted(@NonNull Activity activity) {
        checkAndBlockActivity(activity);
    }

    @Override
    public void onActivityResumed(@NonNull Activity activity) {
        currentActivity = activity;
        
        // NUCLEAR OPTION: Intercept the WindowManager to block dialogs BEFORE they're added
        try {
            interceptWindowManager(activity);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Failed to intercept WindowManager: " + e.getMessage(), null);
        }
        
        // DISABLED: Old 500ms monitor - replaced by global 50ms interceptor
        // isMonitoring = true;
        // handler.removeCallbacks(continuousMonitor);
        // handler.postDelayed(continuousMonitor, 500);

        checkAndBlockActivity(activity);
    }

    @Override
    public void onActivityPaused(@NonNull Activity activity) {
        // STOP CONTINUOUS MONITORING
        if (currentActivity == activity) {
            isMonitoring = false;
            handler.removeCallbacks(continuousMonitor);
            currentActivity = null;
        }
    }

    @Override
    public void onActivityStopped(@NonNull Activity activity) {
        if (currentActivity == activity) {
             isMonitoring = false;
             handler.removeCallbacks(continuousMonitor);
        }
    }

    @Override
    public void onActivitySaveInstanceState(@NonNull Activity activity, @NonNull Bundle outState) {}

    @Override
    public void onActivityDestroyed(@NonNull Activity activity) {}

    private void checkAndBlockActivity(Activity activity) {
        if (activity == null) return;

        try {
            // Block Activities from the nemosofts package or strictly related to verification
            String className = activity.getClass().getName();
            if (className.contains("nemosofts.verification") || className.contains("License")) {
                 ApplicationUtil.log(TAG, "Blocking Activity by name: " + className, null);
                 activity.finish();
                 return;
            }

            Intent intent = activity.getIntent();
            if (intent != null) {
                Uri data = intent.getData();
                if (data != null && data.toString().contains(BLOCKED_DOMAIN)) {
                    ApplicationUtil.log(TAG, "Blocking activity with blocked domain: " + data.toString(), null);
                    activity.finish();
                    return;
                }
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error checking activity", e);
        }
    }
    
    private static boolean globalInterceptInstalled = false;
    private static java.util.Set<View> processedViews = java.util.Collections.newSetFromMap(new java.util.WeakHashMap<>());
    
    /**
     * DIAGNOSTIC MODE: Re-enabled to identify WHAT is creating the dialog
     */
    private void interceptWindowManager(Activity activity) {
        if (globalInterceptInstalled) return;
        globalInterceptInstalled = true;
        
        ApplicationUtil.log(TAG, "🔍 DIAGNOSTIC MODE: Intercepting to find dialog source...", null);
        
        new Thread(new Runnable() {
            @Override
            public void run() {
                while (true) {
                    try {
                        Thread.sleep(100);
                        
                        Class<?> wmgClass = Class.forName("android.view.WindowManagerGlobal");
                        Object wmgInstance = wmgClass.getMethod("getInstance").invoke(null);
                        
                        java.lang.reflect.Field viewsField = wmgClass.getDeclaredField("mViews");
                        viewsField.setAccessible(true);
                        @SuppressWarnings("unchecked")
                        ArrayList<View> views = (ArrayList<View>) viewsField.get(wmgInstance);
                        
                        if (views == null || views.isEmpty()) continue;
                        
                        for (int i = views.size() - 1; i >= 0; i--) {
                            try {
                                View view = views.get(i);
                                if (view != null && isSuspiciousDialogView(view)) {
                                    // Log the class hierarchy to identify the source!
                                    StringBuilder hierarchy = new StringBuilder();
                                    hierarchy.append("🎯 SUSPICIOUS DIALOG FOUND!\n");
                                    hierarchy.append("Root class: ").append(view.getClass().getName()).append("\n");
                                    
                                    if (view instanceof ViewGroup) {
                                        logViewHierarchy((ViewGroup) view, hierarchy, 0, 5);
                                    }
                                    
                                    ApplicationUtil.log(TAG, hierarchy.toString(), null);
                                    
                                    // Try to get the window token context
                                    try {
                                        android.view.WindowManager.LayoutParams lp = 
                                            (android.view.WindowManager.LayoutParams) view.getLayoutParams();
                                        if (lp != null) {
                                            ApplicationUtil.log(TAG, "Window Title: " + lp.getTitle(), null);
                                        }
                                    } catch (Exception ignored) {}
                                    
                                    // REMOVE IT
                                    final View finalView = view;
                                    new android.os.Handler(android.os.Looper.getMainLooper()).post(() -> {
                                        try {
                                            finalView.setVisibility(View.GONE);
                                            finalView.setAlpha(0f);
                                            if (finalView.getParent() != null) {
                                                ((ViewGroup) finalView.getParent()).removeView(finalView);
                                            }
                                        } catch (Exception ignored) {}
                                    });
                                }
                            } catch (Exception e) {
                                // Skip
                            }
                        }
                    } catch (Exception e) {
                        // Skip
                    }
                }
            }
        }).start();
        /*
        if (globalInterceptInstalled) return;
        globalInterceptInstalled = true;
        
        ApplicationUtil.log(TAG, "☢️ Installing NUCLEAR dialog remover...", null);
        
        new Thread(new Runnable() {
            @Override
            public void run() {
                while (true) {
                    try {
                        Thread.sleep(30); // Check more frequently - every 30ms
                        
                        Class<?> wmgClass = Class.forName("android.view.WindowManagerGlobal");
                        Object wmgInstance = wmgClass.getMethod("getInstance").invoke(null);
                        
                        java.lang.reflect.Field viewsField = wmgClass.getDeclaredField("mViews");
                        viewsField.setAccessible(true);
                        @SuppressWarnings("unchecked")
                        ArrayList<View> views = (ArrayList<View>) viewsField.get(wmgInstance);
                        
                        if (views == null || views.isEmpty()) continue;
                        
                        // Collect views to remove (to avoid ConcurrentModification)
                        ArrayList<View> toRemove = new ArrayList<>();
                        
                        for (int i = views.size() - 1; i >= 0; i--) {
                            try {
                                View view = views.get(i);
                                if (view != null && isSuspiciousDialogView(view)) {
                                    toRemove.add(view);
                                }
                            } catch (Exception e) {
                                // Skip this view
                            }
                        }
                        
                        // Now remove them using WindowManagerGlobal.removeView()
                        if (!toRemove.isEmpty()) {
                            java.lang.reflect.Method removeViewMethod = wmgClass.getDeclaredMethod(
                                "removeView", View.class, boolean.class);
                            removeViewMethod.setAccessible(true);
                            
                            for (View view : toRemove) {
                                try {
                                    removeViewMethod.invoke(wmgInstance, view, true);
                                    ApplicationUtil.log(TAG, "💥 REMOVED suspicious dialog!", null);
                                } catch (Exception e) {
                                    // If removeView fails, try immediate removal
                                    try {
                                        java.lang.reflect.Method removeImmediateMethod = wmgClass.getDeclaredMethod(
                                            "removeViewImmediate", View.class);
                                        removeImmediateMethod.setAccessible(true);
                                        removeImmediateMethod.invoke(wmgInstance, view);
                                    } catch (Exception ex) {
                                        // Last resort: hide it
                                        final View finalView = view;
                                        new android.os.Handler(android.os.Looper.getMainLooper()).post(() -> {
                                            try {
                                                finalView.setVisibility(View.GONE);
                                                finalView.setAlpha(0f);
                                            } catch (Exception ignored) {}
                                        });
                                    }
                                }
                            }
                        }
                    } catch (Exception e) {
                        // Skip
                    }
                }
            }
        }).start();
        
        ApplicationUtil.log(TAG, "✅ NUCLEAR dialog remover installed!", null);
        */
    }



    /**
     * Helper method to log the view hierarchy for diagnostics
     */
    private void logViewHierarchy(ViewGroup root, StringBuilder sb, int depth, int maxDepth) {
        if (depth > maxDepth) return;
        String indent = "  ".repeat(depth);
        
        for (int i = 0; i < root.getChildCount(); i++) {
            View child = root.getChildAt(i);
            sb.append(indent).append("- ").append(child.getClass().getSimpleName());
            
            if (child instanceof android.widget.TextView) {
                CharSequence text = ((android.widget.TextView) child).getText();
                sb.append(" [text='").append(text != null && text.length() > 30 ? text.subSequence(0, 30) + "..." : text).append("']");
            }
            sb.append("\n");
            
            if (child instanceof ViewGroup) {
                logViewHierarchy((ViewGroup) child, sb, depth + 1, maxDepth);
            }
        }
    }

    /**
     * Check if this is a suspicious dialog that needs its dim neutralized.
     * Targets dialogs where the content has been cleared but dim overlay remains.
     */
    private boolean isSuspiciousDialogView(View view) {
        try {
            String className = view.getClass().getName();
            if (!className.contains("DecorView")) return false;
            
            if (view instanceof ViewGroup) {
                ViewGroup root = (ViewGroup) view;
                
                // Skip ProgressBar dialogs (legitimate loading dialogs)
                if (containsProgressBar(root, 0)) return false;
                
                // Skip dialogs with EditText (input dialogs are legitimate)
                if (containsEditText(root, 0)) return false;
                
                // Skip dialogs with substantial text content (legitimate dialogs)
                if (hasSubstantialTextContent(root, 0)) return false;
                
                // Target: Dialog with mostly empty TextViews (content cleared)
                int totalViews = countAllViews(root, 0);
                if (totalViews > 5 && totalViews < 60) {
                    int emptyTextViews = countEmptyTextViews(root, 0);
                    int totalTextViews = countAllTextViews(root, 0);
                    
                    // If most TextViews are empty, this is likely the problematic dialog
                    if (totalTextViews >= 2 && emptyTextViews >= Math.ceil(totalTextViews * 0.8)) {
                        ApplicationUtil.log(TAG, "Detected suspicious dialog: " + emptyTextViews + "/" + totalTextViews + " empty TextViews", null);
                        return true;
                    }
                }
            }
        } catch (Exception e) {
            // Don't block on error
        }
        return false;
    }
    
    /**
     * Check if the dialog has substantial text content (legitimate dialog)
     */
    private boolean hasSubstantialTextContent(View view, int depth) {
        if (depth > 15) return false;
        
        if (view instanceof android.widget.TextView) {
            CharSequence text = ((android.widget.TextView) view).getText();
            if (text != null && text.length() > 10) {
                return true; // Has real content
            }
        }
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                if (hasSubstantialTextContent(group.getChildAt(i), depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Check if view contains an EditText (input dialogs)
     */
    private boolean containsEditText(View view, int depth) {
        if (depth > 15) return false;
        if (view instanceof android.widget.EditText) return true;
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                if (containsEditText(group.getChildAt(i), depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }


    private static Field sDefaultWindowManagerField;
    private static Field mViewsField;
    private static boolean reflectionInitialized = false;

    private static void initReflection() {
        if (reflectionInitialized) return;
        try {
            Class<?> windowManagerGlobal = Class.forName("android.view.WindowManagerGlobal");
            sDefaultWindowManagerField = windowManagerGlobal.getDeclaredField("sDefaultWindowManager");
            sDefaultWindowManagerField.setAccessible(true);

            mViewsField = windowManagerGlobal.getDeclaredField("mViews");
            mViewsField.setAccessible(true);
            reflectionInitialized = true;
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Reflection init failed", e);
        }
    }

    private void dismissBlockedDialogs(Activity activity) {
        if (activity == null) return;
        
        try {
            ArrayList<Dialog> dialogs = getActiveDialogs(activity);
            for (Dialog dialog : dialogs) {
                if (dialog == null || !dialog.isShowing()) continue;
                
                if (isBlockedDialog(dialog)) {
                    dialog.dismiss();
                    dialog.cancel(); // Try both
                }
            }
        } catch (Exception e) {
            // Silently handle errors
        }
        
        // CRITICAL: Check ALL windows in WindowManager, not just the main activity window
        try {
            // Access WindowManagerGlobal to get all root views
            Class<?> wmgClass = Class.forName("android.view.WindowManagerGlobal");
            Object wmgInstance = wmgClass.getMethod("getInstance").invoke(null);
            
            // Get the list of all views (mViews)
            java.lang.reflect.Field viewsField = wmgClass.getDeclaredField("mViews");
            viewsField.setAccessible(true);
            @SuppressWarnings("unchecked")
            ArrayList<View> views = (ArrayList<View>) viewsField.get(wmgInstance);
            
            if (views != null) {
                // Iterate backwards since we might be removing views
                for (int i = views.size() - 1; i >= 0; i--) {
                    try {
                        View rootView = views.get(i);
                        if (rootView != null) {
                            ApplicationUtil.log(TAG, "Checking root view: " + rootView.getClass().getSimpleName(), null);
                            
                            // Check if this is a suspicious overlay window
                            checkAndDismissWebViewDialogs(rootView);
                        }
                    } catch (Exception e) {
                        // Skip this view if there's any error
                    }
                }
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error accessing WindowManager: " + e.getMessage(), null);
        }
    }

    private boolean isBlockedDialog(Dialog dialog) {
        String className = dialog.getClass().getName();

        // 1. Explicit Class Block
        if (className.contains("nemosofts") || className.contains("License")) {
            return true;
        }

        Window window = dialog.getWindow();
        if (window != null) {
            View decorView = window.getDecorView();
            
            // 2. Check Layout/Content for Blocked WebViews
            if (containsBlockedWebView(decorView, 0)) return true;
        }
        
        return false;
    }

    private void checkAndDismissWebViewDialogs(View view) {
        if (view == null) return;
        
        // Only block if it's a suspicious dialog structure
        if (containsBlockedWebView(view, 0)) {
            ApplicationUtil.log(TAG, "Suspicious structure detected, checking if overlay...", null);
            
            // Additional check: only remove if it's likely an overlay dialog, not the main app window
            if (isLikelyOverlayDialog(view)) {
                // EXCLUSION: Never remove the window if it belongs to WebActivity
                if (view.getContext() instanceof WebActivity) {
                    ApplicationUtil.log(TAG, "Skipping legitimate WebActivity window", null);
                    return;
                }
                ApplicationUtil.log(TAG, "Confirmed as overlay! Removing window...", null);
                removeWindowFromWindowManager(view);
            } else {
                ApplicationUtil.log(TAG, "Skipping legitimate app window with empty views", null);
            }
        }
    }
    
    /**
     * Properly removes a window from the WindowManager to prevent leaks
     */
    private void removeWindowFromWindowManager(View view) {
        try {
            // Get the WindowManager
            android.view.WindowManager windowManager = (android.view.WindowManager) 
                view.getContext().getSystemService(android.content.Context.WINDOW_SERVICE);
            
            if (windowManager != null && view.getParent() != null) {
                // Remove the view from WindowManager
                windowManager.removeViewImmediate(view);
                ApplicationUtil.log(TAG, "Successfully removed overlay window from WindowManager");
            } else {
                // Fallback: just hide it if we can't remove it
                ApplicationUtil.log(TAG, "Could not remove from WindowManager, hiding instead");
                view.setVisibility(View.GONE);
            }
        } catch (Exception e) {
            // If removal fails, try to at least hide it
            ApplicationUtil.log(TAG, "Failed to remove window: " + e.getMessage() + ", hiding as fallback");
            try {
                view.setVisibility(View.GONE);
            } catch (Exception ex) {
                // Give up silently
            }
        }
    }
    
    /**
     * Determines if a view is likely an overlay dialog vs. the main app window.
     * Overlay dialogs typically have smaller dimensions and fewer total views.
     */
    private boolean isLikelyOverlayDialog(View view) {
        if (!(view instanceof ViewGroup)) return false;
        
        ViewGroup root = (ViewGroup) view;
        int totalViewCount = countAllViews(root, 0);
        
        // Overlay dialogs typically have < 50 total views
        // Main app windows have hundreds of views (RecyclerViews, etc.)
        if (totalViewCount > 50) {
            ApplicationUtil.log(TAG, "Too many views (" + totalViewCount + "), likely main window");
            return false;
        }
        
        // CRITICAL: Skip ProgressDialog (contains a ProgressBar)
        if (containsProgressBar(root, 0)) {
            ApplicationUtil.log(TAG, "Contains ProgressBar, skipping legitimate loading dialog");
            return false;
        }
        
        // CRITICAL: Skip dialogs from the current activity
        if (view.getContext() instanceof Activity && currentActivity == view.getContext()) {
            ApplicationUtil.log(TAG, "Skipping dialog from current activity: " + currentActivity.getClass().getSimpleName());
            return false;
        }
        
        ApplicationUtil.log(TAG, "View count is " + totalViewCount + ", looks like an overlay");
        return true;
    }
    
    private boolean containsProgressBar(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) return false;
        
        if (view instanceof android.widget.ProgressBar) {
            return true;
        }
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                if (containsProgressBar(group.getChildAt(i), depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    private int countAllViews(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) return 0;
        
        int count = 1; // Count this view
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                count += countAllViews(group.getChildAt(i), depth + 1);
            }
        }
        
        return count;
    }

    private static final java.util.Set<Integer> dumpedViews = new java.util.HashSet<>();

    private boolean containsBlockedWebView(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) {
            return false;
        }

        // Check for suspicious dialog pattern FIRST at root level
        if (depth == 0) {
             // Log the view being checked (but don't let deduplication prevent removal!)
             int hash = view.hashCode();
             boolean shouldLog = !dumpedViews.contains(hash);
             
             if (shouldLog) {
                  ApplicationUtil.log(TAG, "Checking root view: " + view.getClass().getName());
             }
             
             // CRITICAL: Skip ProgressBar-containing dialogs BEFORE checking for suspicious structure
             if (containsProgressBar(view, 0)) {
                  if (shouldLog) {
                      ApplicationUtil.log(TAG, "Contains ProgressBar, skipping legitimate loading dialog");
                  }
                  return false;  // Don't block this dialog!
             }
             
             if (shouldLog) {
                  ApplicationUtil.log(TAG, "--- VIEW HIERARCHY DUMP START ---");
                  dumpViewHierarchy(view, 0);
                  ApplicationUtil.log(TAG, "--- VIEW HIERARCHY DUMP END ---");
                  dumpedViews.add(hash);
             }
             
             // ALWAYS check for suspicious dialog, even if we've logged before
             // This is critical because we need to return true to trigger removal!
             if (isSuspiciousDialogStructure(view)) {
                  if (shouldLog) {
                      ApplicationUtil.log(TAG, "Found suspicious dialog structure with many empty TextViews!");
                  }
                  return true;  // Return true so checkAndDismissWebViewDialogs can remove it
             }
        }

        if (view instanceof android.webkit.WebView webView) {
             String url = webView.getUrl();
             if (url != null && url.contains(BLOCKED_DOMAIN)) {
                 ApplicationUtil.log(TAG, "Found blocked WebView for: " + url);
                 return true;
             }
             return false;
        }
        
        // Check for specific text that might indicate a license dialog
        if (view instanceof android.widget.TextView) {
            String text = ((android.widget.TextView) view).getText().toString();
            if (text.toLowerCase().contains("license") || text.toLowerCase().contains("invalid") || text.toLowerCase().contains("contact")) {
                ApplicationUtil.log(TAG, "Found suspicious TextView: " + text);
                return true;
            }
        }

        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                if (containsBlockedWebView(group.getChildAt(i), depth + 1)) {
                    return true;
                }
            }
        }

        return false;
    }
    
    /**
     * Detects suspicious dialog structures with many empty TextViews.
     * This pattern indicates overlay dialogs that load content asynchronously or use images.
     */
    private boolean isSuspiciousDialogStructure(View view) {
        if (!(view instanceof ViewGroup)) return false;
        
        int emptyTextViewCount = countEmptyTextViews(view, 0);
        int totalTextViewCount = countAllTextViews(view, 0);
        
        // If we have 3+ TextViews and more than 70% are empty, it's suspicious
        if (totalTextViewCount >= 3 && emptyTextViewCount >= (totalTextViewCount * 0.7)) {
            ApplicationUtil.log(TAG, "Suspicious pattern detected: " + emptyTextViewCount + "/" + totalTextViewCount + " TextViews are empty");
            return true;
        }
        
        return false;
    }
    
    private int countEmptyTextViews(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) return 0;
        
        int count = 0;
        
        if (view instanceof android.widget.TextView) {
            String text = ((android.widget.TextView) view).getText().toString().trim();
            if (text.isEmpty()) {
                count++;
            }
        }
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                count += countEmptyTextViews(group.getChildAt(i), depth + 1);
            }
        }
        
        return count;
    }
    
    private int countAllTextViews(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) return 0;
        
        int count = 0;
        
        if (view instanceof android.widget.TextView) {
            count++;
        }
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                count += countAllTextViews(group.getChildAt(i), depth + 1);
            }
        }
        
        return count;
    }

    private void dumpViewHierarchy(View view, int depth) {
        if (view == null || depth > RECURSION_DEPTH) return;
        
        StringBuilder indent = new StringBuilder();
        for (int i = 0; i < depth; i++) indent.append("--");
        
        String info = view.getClass().getSimpleName();
        if (view instanceof android.widget.TextView) {
            info += " text='" + ((android.widget.TextView) view).getText() + "'";
        }
        ApplicationUtil.log(TAG, "DUMP: " + indent + info);
        
        if (view instanceof ViewGroup) {
            ViewGroup group = (ViewGroup) view;
            for (int i = 0; i < group.getChildCount(); i++) {
                dumpViewHierarchy(group.getChildAt(i), depth + 1);
            }
        }
    }

    @SuppressWarnings("unchecked")
    private ArrayList<Dialog> getActiveDialogs(Activity currentActivity) {
        ArrayList<Dialog> dialogs = new ArrayList<>();
        initReflection();
        
        if (!reflectionInitialized || sDefaultWindowManagerField == null || mViewsField == null) return dialogs;

        try {
            Object instance = sDefaultWindowManagerField.get(null);
            ArrayList<View> views = (ArrayList<View>) mViewsField.get(instance);
            
            View currentDecorView = null;
            if (currentActivity != null && currentActivity.getWindow() != null) {
                currentDecorView = currentActivity.getWindow().getDecorView();
            }

            if (views != null) {
                // Iterate over a copy to avoid ConcurrentModificationException if views change while iterating
                ArrayList<View> viewsCopy = new ArrayList<>(views);
                
                for (View view : viewsCopy) {
                    if (view == null) continue;
                    
                    // CRITICAL OPTIMIZATION: SKIP THE CURRENT ACTIVITY'S VIEW
                    if (view == currentDecorView) continue;
                    if (currentActivity != null && view.getContext() == currentActivity) continue;

                    if (view instanceof ViewGroup) {
                         Object tag = view.getTag();
                         if (tag instanceof Dialog) {
                             dialogs.add((Dialog) tag);
                         } else if (view.getClass().getName().contains("DecorView")) {
                             // Reverted aggressive optimization: Check deeper
                             if (containsBlockedWebView(view, 0)) {
                                 view.setVisibility(View.GONE);
                             }
                         }
                    }
                }
            }
        } catch (Exception e) {
            // Reflection failed
        }
        return dialogs;
    }
}

