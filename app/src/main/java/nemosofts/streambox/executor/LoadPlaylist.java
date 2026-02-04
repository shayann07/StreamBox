package nemosofts.streambox.executor;

import android.content.Context;
import android.net.Uri;
import android.util.Log;

import androidx.annotation.NonNull;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.concurrent.TimeUnit;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import nemosofts.streambox.R;
import nemosofts.streambox.interfaces.LoadPlaylistListener;
import nemosofts.streambox.item.ItemPlaylist;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.AsyncTaskExecutor;
import nemosofts.streambox.utils.HttpsTrustManager;
import nemosofts.streambox.utils.helper.DBHelper;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import okhttp3.logging.HttpLoggingInterceptor;

public class LoadPlaylist extends AsyncTaskExecutor<String, String, String> {

    private final Context ctx;
    private final LoadPlaylistListener listener;
    private final Boolean isFile;
    private final String filePath;
    private final DBHelper dbHelper; // Use DBHelper for SQLite storage
    private String msg = "";
    
    // Batch processing configuration
    private final ArrayList<ItemPlaylist> liveBatch = new ArrayList<>();
    private final ArrayList<ItemPlaylist> movieBatch = new ArrayList<>();
    private static final int BATCH_SIZE = 5000; // Batch size for SQLite bulk inserts (per type)

    private static final String EXTINF_PREFIX = "#EXTINF:";
    private static final Pattern TVG_NAME_PATTERN = Pattern.compile("tvg-name=\"(.*?)\"");
    private static final Pattern GROUP_TITLE_PATTERN = Pattern.compile("group-title=\"([^\"]*)\",(.*?)$");
    private static final Pattern TVG_LOGO_PATTERN = Pattern.compile("tvg-logo=\"(.*?)\"");

    public LoadPlaylist(Context ctx, Boolean isFile, String filePath, LoadPlaylistListener listener) {
        this.ctx = ctx;
        this.listener = listener;
        this.isFile = isFile;
        this.filePath = filePath;
        this.dbHelper = new DBHelper(ctx); // Initialize DBHelper for SQLite
    }

    @Override
    protected void onPreExecute() {
        listener.onStart();
        // Clear existing playlist data before importing
        dbHelper.clearPlaylist();
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String strings) {
        try {
            ApplicationUtil.log("LoadPlaylist", "doInBackground started. isFile: " + isFile + ", path: " + filePath, null);
            if (Boolean.TRUE.equals(isFile)) {
                return processFileFromUri();
            } else {
                return processHttpRequest();
            }
        } catch (Exception e) {
            ApplicationUtil.log("LoadPlaylist", "doInBackground failed", e);
            msg = ctx.getString(R.string.err_server_not_connected);
            return "0";
        }
    }

    @NonNull
    private String processFileFromUri() {
        try (InputStream inputStream = ctx.getContentResolver().openInputStream(Uri.parse(filePath));
             BufferedReader reader = new BufferedReader(new InputStreamReader(inputStream))) {

            if (inputStream == null) {
                msg = "File not found or unable to open";
                return "0";
            }

            return processFile(reader);
        } catch (IOException e) {
            msg = "Error reading file";
            Log.e("LoadPlaylist", "Error loading playlist data", e);
            return "0";
        }
    }

    @NonNull
    private String processHttpRequest() throws IOException {
        ApplicationUtil.log("LoadPlaylist", "processHttpRequest started for URL: " + filePath, null);
        // HttpsTrustManager.allowAllSSL(); // Removed: Using shared unsafe client instead

        // Use a client with a longer timeout for playlist downloads
        OkHttpClient client = createHttpClient();
        Request request = new Request.Builder().url(filePath).build();

        ApplicationUtil.log("LoadPlaylist", "Executing HTTP request...", null);
        try (Response response = client.newCall(request).execute()) {
            ApplicationUtil.log("LoadPlaylist", "HTTP response received. Code: " + response.code(), null);
            
            if (!response.isSuccessful()) {
                msg = "HTTP request failed: " + response.code();
                ApplicationUtil.log("LoadPlaylist", msg, null);
                return "0";
            }

            if (response.body() == null) {
                msg = "Response body is empty";
                ApplicationUtil.log("LoadPlaylist", msg, null);
                return "0";
            }

            ApplicationUtil.log("LoadPlaylist", "Response body size: " + response.body().contentLength() + " bytes", null);
            ApplicationUtil.log("LoadPlaylist", "Starting to process file content...", null);
            
            try (BufferedReader reader = new BufferedReader(
                    new InputStreamReader(response.body().byteStream()))
            ) {
                return processFile(reader);
            }
        }
    }

    @NonNull
    private OkHttpClient createHttpClient() {
        // Use shared client as base to inherit "Trust All" SSL configuration
        return ApplicationUtil.getSharedClientInstance().newBuilder()
                .connectTimeout(30, TimeUnit.SECONDS)
                .writeTimeout(30, TimeUnit.SECONDS)
                .readTimeout(300, TimeUnit.SECONDS) // 5 minutes for large files
                .cache(null)
                .build();
    }

    @NonNull
    private String processFile(BufferedReader reader) {
        ApplicationUtil.log("LoadPlaylist", "processFile started with streaming", null);
        String line;
        String name = null, logo = "", group = "";
        int lineCount = 0;
        int channelCount = 0;
        int lastReportedCount = 0;
        
        try {
            while ((line = reader.readLine()) != null) {
                lineCount++;
                
                // Check for cancellation
                if (isCancelled()) {
                    ApplicationUtil.log("LoadPlaylist", "Processing cancelled by user", null);
                    msg = "Processing cancelled";
                    return "0";
                }
    
                if (line.startsWith(EXTINF_PREFIX)) {
                    String data = line.substring(EXTINF_PREFIX.length()).trim();
                    // Remove potential duration (e.g., -1 or 0) and the comma if it exists immediately
                    if (data.startsWith("-1")) data = data.substring(2).trim();           
                    else if (data.startsWith("0")) data = data.substring(1).trim();
                    
                    name = extractData(data, TVG_NAME_PATTERN);
                    if (name.isEmpty()) {
                        String[] parts = data.split(",", 2);
                        name = parts.length > 1 ? parts[1].trim() : "Unknown";
                    }
                    if (!name.isEmpty()) {
                        name = name.trim();
                    }
                    logo = extractData(data, TVG_LOGO_PATTERN).trim();
                    group = extractData(data, GROUP_TITLE_PATTERN).trim();
                } else if ((line.startsWith("http") || line.startsWith("https")) && name != null) {
                    channelCount++;
                    addPlaylistItem(line, name, logo, group);
                    name = null;
                    logo = "";
                    group = "";
                    
                    // Progressive reporting: frequent updates early, less frequent later
                    int threshold;
                    if (channelCount < 1000) {
                        threshold = 100;  // Every 100 channels for first 1000
                    } else if (channelCount < 10000) {
                        threshold = 1000; // Every 1000 for next 9000
                    } else {
                        threshold = 5000; // Every 5000 thereafter
                    }
                    
                    if (channelCount - lastReportedCount >= threshold) {
                        String progressMsg = "Loading " + channelCount + " channels...";
                        publishProgress(progressMsg);
                        lastReportedCount = channelCount;
                    }
                }
            }

            // Write remaining items in the batch
            if (!liveBatch.isEmpty()) {
                dbHelper.insertPlaylistBulk(liveBatch, true);
                liveBatch.clear();
            }
            if (!movieBatch.isEmpty()) {
                dbHelper.insertPlaylistBulk(movieBatch, false);
                movieBatch.clear();
            }
    
            ApplicationUtil.log("LoadPlaylist", 
                "processFile completed successfully. Total lines: " + lineCount + 
                ", Total channels: " + channelCount, 
                null);
            msg = "Successfully loaded " + channelCount + " channels";
            return "1";
            
        } catch (OutOfMemoryError e) {
            ApplicationUtil.log("LoadPlaylist", "OutOfMemoryError: Playlist too large (" + channelCount + " channels) - " + e.getMessage());
            msg = "Playlist too large (" + channelCount + "+ channels). Please use a smaller playlist.";
            return "0";
        } catch (IOException e) {
            ApplicationUtil.log("LoadPlaylist", "IOException while reading playlist at channel " + channelCount, e);
            msg = "Error reading playlist file";
            return "0";
        } catch (Exception e) {
            ApplicationUtil.log("LoadPlaylist", "Unexpected error at channel " + channelCount, e);
            msg = "Unexpected error: " + e.getMessage();
            return "0";
        }
    }

    private void addPlaylistItem(String line, String name, String logo, String group) {
        boolean isLive = detectIsLive(line);
        ItemPlaylist item = new ItemPlaylist(
                name,
                logo != null ? logo : "null", // Use empty string if logo is null
                group != null ? group : "Uncategorized", // Default group if missing
                line
        );

        if (isLive) {
            liveBatch.add(item);
            if (liveBatch.size() >= BATCH_SIZE) {
                dbHelper.insertPlaylistBulk(liveBatch, true);
                liveBatch.clear();
            }
        } else {
            movieBatch.add(item);
            if (movieBatch.size() >= BATCH_SIZE) {
                dbHelper.insertPlaylistBulk(movieBatch, false);
                movieBatch.clear();
            }
        }
    }

    private boolean detectIsLive(String url) {
        if (url == null) return true;
        String lowerUrl = url.toLowerCase();
        // Common video extensions usually imply VOD/Movie
        return !lowerUrl.endsWith(".mp4") && 
               !lowerUrl.endsWith(".mkv") && 
               !lowerUrl.endsWith(".avi") && 
               !lowerUrl.endsWith(".mov") && 
               !lowerUrl.endsWith(".flv") && 
               !lowerUrl.endsWith(".wmv");
    }

    private String extractData(String data, Pattern pattern) {
        if (data == null || data.isEmpty()) return "";
        try {
            Matcher matcher = pattern.matcher(data);
            if (matcher.find()) {
                return matcher.group(1);
            }
        } catch (Exception e) {
            return "";
        }
        return "";
    }

    @Override
    protected void onProgressUpdate(@NonNull String value) {
        listener.onProgress(value);
    }

    @Override
    protected void onPostExecute(String s) {
        // Close the DBHelper to release resources
        dbHelper.close();
        // Pass an empty list because data is already in SQLite
        listener.onEnd(s, msg, new ArrayList<>());
    }
}