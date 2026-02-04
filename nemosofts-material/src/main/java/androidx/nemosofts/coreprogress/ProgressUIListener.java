package androidx.nemosofts.coreprogress;

public abstract class ProgressUIListener {
    public void onUIProgressStart(long totalBytes) {
        // Stub
    }
    public abstract void onUIProgressChanged(long numBytes, long totalBytes, float percent, float speed);
}
