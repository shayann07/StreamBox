package androidx.nemosofts.material;

import android.content.Context;
import android.util.AttributeSet;
import android.view.View;

public class SnowfallView extends View {
    public SnowfallView(Context context) {
        super(context);
    }

    public SnowfallView(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    public SnowfallView(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
    }
    
    public void restartFalling() {}
    public void stopFalling() {}
}
