package androidx.nemosofts.material;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.FrameLayout;

public class ShimmerEffects extends FrameLayout {
    public ShimmerEffects(Context context) {
        super(context);
    }

    public ShimmerEffects(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    public ShimmerEffects(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
    }
    
    public void startShimmer() {}
    public void stopShimmer() {}
}
