package androidx.nemosofts.theme;

import android.content.Context;
import android.content.SharedPreferences;

public class ThemeEngine {
    private final SharedPreferences sharedPreferences;
    private final Context context;

    public ThemeEngine(Context context) {
        this.context = context;
        this.sharedPreferences = context.getSharedPreferences("nemosofts_theme", Context.MODE_PRIVATE);
    }

    public int getThemePage() {
        return sharedPreferences.getInt("theme_page", 0);
    }

    public void setThemePage(int themePage) {
        sharedPreferences.edit().putInt("theme_page", themePage).apply();
    }

    public void setThemeMode(boolean isDark) {
        sharedPreferences.edit().putBoolean("is_dark", isDark).apply();
    }
    
    public void setDark(boolean isDark) {
         sharedPreferences.edit().putBoolean("is_dark", isDark).apply();
    }

    public boolean getThemeMode() {
        return sharedPreferences.getBoolean("is_dark", false);
    }
}
