package nemosofts.streambox.utils;

import android.os.Handler;
import android.os.Looper;
import android.util.Log;

import androidx.annotation.NonNull;

import org.json.JSONException;

import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.ThreadPoolExecutor;
import java.util.concurrent.TimeUnit;

public abstract class AsyncTaskExecutor<P, B, R> {

    private static final ExecutorService executor;
    private Handler handler;

    static {
        int availableProcessors = Runtime.getRuntime().availableProcessors();
        int corePoolSize = Math.max(2, availableProcessors / 2);
        int maxPoolSize = Math.max(4, availableProcessors);

        executor = new ThreadPoolExecutor(
                corePoolSize,
                maxPoolSize,
                60L, TimeUnit.SECONDS,
                new LinkedBlockingQueue<>(), // Unbounded queue to prevent CallerRunsPolicy from blocking Main Thread
                Executors.defaultThreadFactory()
        );
    }

    protected AsyncTaskExecutor() {
        // Executor is now static and shared
    }

    public ExecutorService getExecutor() {
        return executor;
    }

    public Handler getHandler() {
        if (handler == null) {
            synchronized (AsyncTaskExecutor.class) {
                handler = new Handler(Looper.getMainLooper());
            }
        }
        return handler;
    }

    protected void onPreExecute() {
        // Override this method wherever you want to perform task before background execution get started
    }

    protected abstract R doInBackground(P params) throws JSONException;

    protected abstract void onPostExecute(R result);

    protected void onProgressUpdate(@NonNull B value) {
        // Override this method wherever you want update a progress result
    }

    // used for push progress report to UI ---------------------------------------------------------
    public void publishProgress(@NonNull B value) {
        getHandler().post(() -> onProgressUpdate(value));
    }

    public void execute() {
        execute(null);
    }

    public void execute(P params) {
        getHandler().post(() -> {
            onPreExecute();
            executor.execute(() -> {
                try {
                    R result = doInBackground(params);
                    getHandler().post(() -> onPostExecute(result));
                } catch (Exception e) {
                    // Handle exceptions that might occur during background task
                    Log.e("AsyncTaskExecutor", "Error executing task", e);
                }
            });
        });
    }

    // Cancel the task -----------------------------------------------------------------------------
    // Cancel the task -----------------------------------------------------------------------------
    public void shutDown() {
        // Do not shut down the shared executor
    }

    // Shutdown the executor if no more tasks are pending ------------------------------------------
    public void checkAndShutDown() {
        // Do not checking shut down the shared executor
    }

    // Check if the task is running ----------------------------------------------------------------
    public boolean isRunning() {
        return executor != null && !executor.isShutdown() && !executor.isTerminated();
    }

    // Check if the task is cancelled --------------------------------------------------------------
    public boolean isCancelled() {
        return executor == null || executor.isTerminated() || executor.isShutdown();
    }
}