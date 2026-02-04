package androidx.nemosofts.material;

import android.content.Context;

public class ProgressDialog extends android.app.ProgressDialog {

    public ProgressDialog(Context context) {
        super(context);
    }

    public ProgressDialog(Context context, boolean isTheme) {
        super(context);
        // The boolean 'isTheme' likely controlled some internal branding or theme in the original library.
        // We ignore it and just use the default dialog.
    }
    
    public ProgressDialog(Context context, int theme) {
        super(context, theme);
    }
}
