package androidx.nemosofts;

public class TaskExecutor {
    // Shadow class to replace the library's TaskExecutor.
    public static void execute(Runnable runnable) {
        if (runnable != null) {
            new Thread(runnable).start();
        }
    }
}
