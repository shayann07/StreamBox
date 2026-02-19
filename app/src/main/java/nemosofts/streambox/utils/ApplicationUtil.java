package nemosofts.streambox.utils;

import android.annotation.SuppressLint;
import android.content.Context;
import android.database.Cursor;
import android.graphics.Color;
import android.net.Uri;
import android.os.BatteryManager;
import android.os.Build;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.SpannableStringBuilder;
import android.text.Spanned;
import android.text.TextWatcher;
import android.text.style.ForegroundColorSpan;
import android.util.Log;
import android.util.Rational;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.annotation.OptIn;
import androidx.media3.common.Format;
import androidx.media3.common.MimeTypes;
import androidx.media3.common.util.UnstableApi;
import androidx.media3.exoplayer.ExoPlayer;
import androidx.nemosofts.utils.FormatUtils;
import androidx.nemosofts.utils.NetworkUtils;
import androidx.nemosofts.utils.ThemoviedbAPI;

import org.jetbrains.annotations.Contract;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Random;
import java.util.concurrent.TimeUnit;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import nemosofts.streambox.BuildConfig;
import nemosofts.streambox.R;
import nemosofts.streambox.adapter.AdapterCategory;
import nemosofts.streambox.item.ItemCat;
import okhttp3.ConnectionPool;
import okhttp3.Dispatcher;
import okhttp3.FormBody;
import okhttp3.Headers;
import okhttp3.HttpUrl;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import okhttp3.logging.HttpLoggingInterceptor;
import okio.Buffer;

public class ApplicationUtil {

    private static final String TAG = "ApplicationUtil";
    private static final String USERNAME_KEY = "username";
    private static final String PASSWORD_KEY = "password";
    private static final String ACTION_KEY = "action";
    private static final Random RANDOM = new Random();
    private static final int DEFAULT_TIMEOUT_SECONDS = 60;
    private static final int DEFAULT_READ_TIMEOUT_SECONDS = 60;
    private static final int DEFAULT_WRITE_TIMEOUT_SECONDS = 20;
    private static final int MAX_REQUESTS = 64;
    private static final int MAX_REQUESTS_PER_HOST = 16;
    
    public static final java.net.CookieManager DEFAULT_COOKIE_MANAGER;

    static {
        DEFAULT_COOKIE_MANAGER = new java.net.CookieManager();
        DEFAULT_COOKIE_MANAGER.setCookiePolicy(java.net.CookiePolicy.ACCEPT_ALL);
        if (java.net.CookieHandler.getDefault() != DEFAULT_COOKIE_MANAGER) {
            java.net.CookieHandler.setDefault(DEFAULT_COOKIE_MANAGER);
        }
    }

    private static final ConnectionPool CONNECTION_POOL =
            new ConnectionPool(8, 5, TimeUnit.MINUTES);
    private static final OkHttpClient HTTP_CLIENT = buildSharedClient();

    private ApplicationUtil() {
        throw new IllegalStateException("Utility class");
    }

    public static int getRandomValue(int bound) {
        return RANDOM.nextInt(bound);
    }

    @NonNull
    public static OkHttpClient buildSharedClient() {
        Dispatcher dispatcher = new Dispatcher();
        dispatcher.setMaxRequests(MAX_REQUESTS);
        dispatcher.setMaxRequestsPerHost(MAX_REQUESTS_PER_HOST);

        OkHttpClient.Builder builder = new OkHttpClient.Builder()
                .connectTimeout(DEFAULT_TIMEOUT_SECONDS, TimeUnit.SECONDS)
                .writeTimeout(DEFAULT_WRITE_TIMEOUT_SECONDS, TimeUnit.SECONDS)
                .readTimeout(DEFAULT_READ_TIMEOUT_SECONDS, TimeUnit.SECONDS)
                .dispatcher(dispatcher)
                .retryOnConnectionFailure(true)
                .connectionPool(CONNECTION_POOL)
                .cache(null);

        if (BuildConfig.DEBUG) {
            HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
            // Use HEADERS instead of BODY to prevent OOM on large video streams
            logging.setLevel(HttpLoggingInterceptor.Level.HEADERS);
            builder.addInterceptor(logging);
        }

        // Trust All SSL Certificates (Fix for Expired/Win-Acme Certificates)
        try {
            final javax.net.ssl.TrustManager[] trustAllCerts = new javax.net.ssl.TrustManager[]{
                    new javax.net.ssl.X509TrustManager() {
                        @Override
                        public void checkClientTrusted(java.security.cert.X509Certificate[] chain, String authType) throws java.security.cert.CertificateException {
                        }

                        @Override
                        public void checkServerTrusted(java.security.cert.X509Certificate[] chain, String authType) throws java.security.cert.CertificateException {
                        }

                        @Override
                        public java.security.cert.X509Certificate[] getAcceptedIssuers() {
                            return new java.security.cert.X509Certificate[]{};
                        }
                    }
            };

            final javax.net.ssl.SSLContext sslContext = javax.net.ssl.SSLContext.getInstance("SSL");
            sslContext.init(null, trustAllCerts, new java.security.SecureRandom());
            final javax.net.ssl.SSLSocketFactory sslSocketFactory = sslContext.getSocketFactory();

            builder.sslSocketFactory(sslSocketFactory, (javax.net.ssl.X509TrustManager) trustAllCerts[0]);
            builder.hostnameVerifier((hostname, session) -> true);
        } catch (Exception e) {
            log(TAG, "Error configuring unsafe SSL", e);
        }

        builder.cookieJar(new okhttp3.CookieJar() {
            @Override
            public void saveFromResponse(@NonNull HttpUrl url, @NonNull List<okhttp3.Cookie> cookies) {
                if (cookies.isEmpty()) return;
                java.util.Map<String, java.util.List<String>> headers = new java.util.HashMap<>();
                java.util.List<String> cookieStrings = new java.util.ArrayList<>();
                for (okhttp3.Cookie cookie : cookies) {
                    cookieStrings.add(cookie.toString());
                }
                headers.put("Set-Cookie", cookieStrings);
                try {
                    DEFAULT_COOKIE_MANAGER.put(url.uri(), headers);
                } catch (java.io.IOException e) {
                    log(TAG, "Error saving cookies", e);
                }
            }

            @NonNull
            @Override
            public List<okhttp3.Cookie> loadForRequest(@NonNull HttpUrl url) {
                try {
                    java.util.Map<String, java.util.List<String>> headers = DEFAULT_COOKIE_MANAGER.get(url.uri(), java.util.Collections.emptyMap());
                    java.util.List<String> cookieHeaders = headers.get("Cookie");
                    if (cookieHeaders == null) return java.util.Collections.emptyList();

                    java.util.List<okhttp3.Cookie> cookies = new java.util.ArrayList<>();
                    for (String header : cookieHeaders) {
                        String[] pairs = header.split(";\\s*");
                        for (String pair : pairs) {
                            int equals = pair.indexOf('=');
                            if (equals > 0) {
                                String name = pair.substring(0, equals);
                                String value = pair.substring(equals + 1);
                                cookies.add(new okhttp3.Cookie.Builder()
                                        .name(name)
                                        .value(value)
                                        .domain(url.host())
                                        .build());
                            }
                        }
                    }
                    return cookies;
                } catch (java.io.IOException e) {
                    log(TAG, "Error loading cookies", e);
                    return java.util.Collections.emptyList();
                }
            }
        });

        return builder.build();
    }

    public static OkHttpClient getSharedClientInstance() {
        return HTTP_CLIENT;
    }

    /**
     * Creates an OkHttpClient for IPTV streaming with required headers.
     * IPTV servers often require Referer, Origin, and other headers for authentication.
     * 
     * @param serverUrl The base server URL for Referer/Origin headers
     * @return OkHttpClient configured for IPTV streaming
     */
    @NonNull
    public static OkHttpClient getStreamingClientInstance(String serverUrl) {
        String baseUrl = "";
        try {
            if (serverUrl != null && !serverUrl.isEmpty()) {
                java.net.URL url = new java.net.URL(serverUrl);
                baseUrl = url.getProtocol() + "://" + url.getHost();
                int port = url.getPort();
                // Only include port if it's not the default for the protocol
                // HTTP default = 80, HTTPS default = 443
                boolean isDefaultPort = (url.getProtocol().equals("http") && port == 80) ||
                                        (url.getProtocol().equals("https") && port == 443);
                if (port != -1 && !isDefaultPort) {
                    baseUrl += ":" + port;
                }
                log(TAG, "getStreamingClientInstance: Parsed baseUrl: " + baseUrl + " from serverUrl: " + serverUrl);
            } else {
                log(TAG, "getStreamingClientInstance: serverUrl is null or empty!");
            }
        } catch (Exception e) {
            log(TAG, "Error parsing server URL: " + e.getMessage());
        }
        
        final String finalBaseUrl = baseUrl;
        
        return HTTP_CLIENT.newBuilder()
                .protocols(java.util.Collections.singletonList(okhttp3.Protocol.HTTP_1_1))
                .addInterceptor(chain -> {
                    okhttp3.Request original = chain.request();
                    String requestUrl = original.url().toString();
                    
                    // Log the request before adding headers
                    log(TAG, "IPTV Request: " + requestUrl);
                    
                    okhttp3.Request.Builder requestBuilder = original.newBuilder()
                            .header("Accept", "*/*")
                            .header("Accept-Language", "en-US,en;q=0.9")
                            .header("Connection", "keep-alive");
                    
                    // Add Referer and Origin if we have a valid server URL
                    if (!finalBaseUrl.isEmpty()) {
                        requestBuilder.header("Referer", finalBaseUrl + "/");
                        // requestBuilder.header("Origin", finalBaseUrl); // Removed to prevent 403 on cross-domain redirects
                        log(TAG, "IPTV Headers added - Referer: " + finalBaseUrl + "/");
                    } else {
                        log(TAG, "WARNING: No Referer/Origin headers added - baseUrl is empty!");
                    }
                    
                    okhttp3.Request newRequest = requestBuilder.build();
                    okhttp3.Response response = chain.proceed(newRequest);
                    
                    // Log response status
                    log(TAG, "IPTV Response: " + response.code() + " for " + requestUrl);
                    
                    return response;
                })
                .build();
    }

    @NonNull
    public static String responsePost(String url, RequestBody requestBody) {
        OkHttpClient client = HTTP_CLIENT;

        // Build the POST request
        Request request = new Request.Builder()
                .url(url)
                .post(requestBody)
                .build();

        try (Response response = client.newCall(request).execute()) {
            if (response.isSuccessful()) {
                return response.body().string();
            } else {
                log(TAG, "Unsuccessful response: " + response.code() + " " + response.message() + " for URL: " + url, null);
            }
            return executeGetRequestFromPostBody(url, requestBody, client);
        } catch (IOException e) {
            log(TAG, "Error during POST request for URL: " + url, e);
            return executeGetRequestFromPostBody(url, requestBody, client);
        }
    }

    @NonNull
    private static String executeGetRequestFromPostBody(String url,
                                                        RequestBody requestBody,
                                                        OkHttpClient client) {
        HttpUrl.Builder urlBuilder = HttpUrl.parse(url).newBuilder();
        if (requestBody instanceof FormBody formBody) {
            for (int i = 0; i < formBody.size(); i++) {
                urlBuilder.addQueryParameter(formBody.name(i), formBody.value(i));
            }
        } else if (requestBody instanceof MultipartBody multipartBody) {
            extractMultipartParams(multipartBody, urlBuilder);
        }

        Request getRequest = new Request.Builder()
                .url(urlBuilder.build())
                .get()
                .build();

        try (Response response = client.newCall(getRequest).execute()) {
            return response.body().string();
        } catch (IOException e) {
            log(TAG, "Error during GET request retry.", e);
            return "";
        }
    }

    private static void extractMultipartParams(MultipartBody multipartBody,
                                               HttpUrl.Builder urlBuilder) {
        for (MultipartBody.Part part : multipartBody.parts()) {
            RequestBody body = part.body();
            Headers headers = part.headers();
            if (headers != null) {
                String contentDisposition = headers.get("Content-Disposition");
                if (contentDisposition != null) {
                    Matcher matcher = Pattern.compile("name=\"([^\"]*)\"").matcher(contentDisposition);
                    if (matcher.find()) {
                        String name = matcher.group(1);
                        try (Buffer buffer = new Buffer()) {
                            body.writeTo(buffer);
                            urlBuilder.addQueryParameter(name, buffer.readUtf8());
                        } catch (IOException e) {
                            log(TAG, "Error reading MultipartBody part for GET parameters.", e);
                        }
                    }
                }
            }
        }
    }

    @NonNull
    public static String getMovieCredits(String movieID, String token) {
        if (movieID == null || token == null) {
            return "";
        }
        return executeRequest(ThemoviedbAPI.getMovieCredits(movieID), token);
    }

    @NonNull
    public static String getMovieImages(String movieID, String token) {
        if (movieID == null || token == null) {
            return "";
        }
        return executeRequest(ThemoviedbAPI.getMovieImages(movieID), token);
    }

    @NonNull
    private static String executeRequest(String url, String token) {
        OkHttpClient client = new OkHttpClient();
        Request request = new Request.Builder()
                .url(url)
                .get()
                .addHeader("accept", "application/json")
                .addHeader("Authorization", "Bearer "+token)
                .build();
        try {
            Response response = client.newCall(request).execute();
            return response.body().string();
        } catch (Exception e) {
            return "";
        }
    }

    @NonNull
    public static RequestBody createApiRequest(@NonNull Map<String, String> parameters) {
        MultipartBody.Builder builder = new MultipartBody.Builder().setType(MultipartBody.FORM);
        for (Map.Entry<String, String> entry : parameters.entrySet()) {
            if (entry.getValue() != null) {
                builder.addFormDataPart(entry.getKey(), entry.getValue());
            }
        }
        return builder.build();
    }

    @NonNull
    public static RequestBody getAPIRequestLogin(String username, String password) {
        Map<String, String> params = new HashMap<>();
        params.put(USERNAME_KEY, username);
        params.put(PASSWORD_KEY, password);
        return createApiRequest(params);
    }

    @NonNull
    public static RequestBody getAPIRequest(String action, String username, String password) {
        Map<String, String> params = new HashMap<>();
        params.put(USERNAME_KEY, username);
        params.put(PASSWORD_KEY, password);
        params.put(ACTION_KEY, action);
        return createApiRequest(params);
    }

    @NonNull
    public static RequestBody getAPIRequestID(String action, String type, String seriesID,
                                               String username, String password) {
        Map<String, String> params = new HashMap<>();
        params.put(USERNAME_KEY, username);
        params.put(PASSWORD_KEY, password);
        params.put(ACTION_KEY, action);
        params.put(type, seriesID);
        return createApiRequest(params);
    }

    public static Boolean calculateUpdateHours(@NonNull String inputDateStr, int updateHours){
        boolean isUpdate = false;
        try {
            if (!inputDateStr.isEmpty()){
                @SuppressLint("SimpleDateFormat") SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MM-yyyy HH:mm:ss");
                Date inputDate = dateFormat.parse(inputDateStr);
                Date currentDate = new Date();
                assert inputDate != null;
                long timeDifferenceInMillis = currentDate.getTime() - inputDate.getTime();
                long seconds = timeDifferenceInMillis / 1000;
                int hours = (int) (seconds / 3600);
                isUpdate = hours > updateHours;
            }
        } catch (Exception e) {
            return isUpdate;
        }
        return isUpdate;
    }

    @NonNull
    public static String averageRating(String rating) {
        if (rating == null || rating.isEmpty()) {
            return "0";
        }

        try {
            float floatRating = Float.parseFloat(rating);
            int roundedRating = Math.max(0, Math.min(5, (int) Math.floor(floatRating)));
            return String.valueOf(roundedRating);
        } catch (NumberFormatException e) {
            return "0";  // Return "0" for invalid numeric formats
        }
    }

    @NonNull
    @Contract("_ -> new")
    public static Rational getRational(final Format format) {
        if (format == null || format.width <= 0 || format.height <= 0) {
            // Default to 16:9 if format is null or has invalid dimensions
            return new Rational(16, 9);
        }
        if (isRotated(format)){
            return new Rational(format.height, format.width);
        } else {
            return new Rational(format.width, format.height);
        }
    }

    @Contract(pure = true)
    @OptIn(markerClass = UnstableApi.class)
    public static boolean isRotated(@NonNull final Format format) {
        return format.rotationDegrees == 90 || format.rotationDegrees == 270;
    }

    @NonNull
    @Contract(pure = true)
    public static String containerExtension(@Nullable String container) {
        if (container == null || container.isEmpty()) {
            return ".mp4";
        }
        return container.contains(".") ? container : "." + container;
    }

    @NonNull
    @Contract(pure = true)
    public static Boolean isAdultsCount(@NonNull String count) {
        String normalizedCount = count.toLowerCase();
        // List of keywords related to adult content
        String[] adultKeywords = {"18+", "+18", "[18+]", "adults", "adult", "xxx", "pron", "sex"};
        // Check if any of the keywords are present in the normalized string
        for (String keyword : adultKeywords) {
            if (normalizedCount.contains(keyword)) {
                return true;
            }
        }
        return false;
    }

    public static int getBatteryDrawable(int status, int level, int scale) {
        float batteryLevel = (level / (float) scale) * 100;
        boolean isCharging = (status == BatteryManager.BATTERY_STATUS_CHARGING);
        if (isCharging){
            return R.drawable.ic_battery_charging;
        }

        if (batteryLevel < 10){
            return R.drawable.ic_battery_disable;
        } else if (batteryLevel < 20){
            return R.drawable.ic_battery_empty;
        } else if (batteryLevel < 30){
            return R.drawable.ic_battery_one;
        } else if (batteryLevel < 50){
            return R.drawable.ic_battery_two;
        }
        return R.drawable.ic_battery_full;
    }

    public static void setRating(String rating, ImageView star1, ImageView star2,
                                 ImageView star3, ImageView star4, ImageView star5) {
        if (rating == null) {
            return;
        }

        try {
            String avg = averageRating(rating);
            int starCount = Integer.parseInt(avg);
            ImageView[] stars = {star1, star2, star3, star4, star5};
            for (int i = 0; i < stars.length; i++) {
                if (stars[i] != null) {
                    stars[i].setImageResource(i < starCount ? R.drawable.ic_star : R.drawable.ic_star_border);
                }
            }
        } catch (Exception e) {
            log(TAG, "Invalid rating format", e);
        }
    }

    @NonNull
    public static CharSequence setErrorMsg(String errorMsg) {
        try {
            SpannableStringBuilder builder = new SpannableStringBuilder(errorMsg);
            builder. setSpan(new ForegroundColorSpan(Color.RED), 12, 28, Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);
            return builder;
        } catch (Exception e) {
            return errorMsg;
        }
    }

    @OptIn(markerClass = UnstableApi.class)
    @NonNull
    public static String getInfoAudio( ExoPlayer exoPlayer) {
        String infoAudio = """
        Audio Sample Rate: N/A
    
        Audio Channels: N/A
    
        Audio Type: N/A
    
        Audio MIME Type: N/A
        """;

        if (exoPlayer == null){
            return infoAudio;
        }
        if (exoPlayer.getAudioFormat() != null){
            int audioSampleRate = exoPlayer.getAudioFormat().sampleRate;
            int audioChannels = exoPlayer.getAudioFormat().channelCount;
            String audioMimeType = exoPlayer.getAudioFormat().sampleMimeType;

            infoAudio = "Audio Sample Rate: " + audioSampleRate + "\n\n"
                    + "Audio Channels: " + audioChannels + "\n\n"
                    + "Audio Type: "+ formatAudioFromMime(audioMimeType) +"\n\n"
                    + "Audio MIME Type: " + audioMimeType +"\n";

        }
        return infoAudio;
    }

    @OptIn(markerClass = UnstableApi.class)
    @NonNull
    public static String getInfoVideo(ExoPlayer exoPlayer, boolean isLive) {
        String infoVideo = """
        Video Quality : Unknown resolution
    
        Video Width: N/A
    
        Video Height: N/A
        """;

        if (exoPlayer == null){
            return infoVideo;
        }
        if (exoPlayer.getVideoFormat() != null){
            int videoWidth = exoPlayer.getVideoFormat().width;
            int videoHeight = exoPlayer.getVideoFormat().height;
            int videoBitrate = exoPlayer.getVideoFormat().bitrate;
            float frameRate = exoPlayer.getVideoFormat().frameRate;
            String finalRate = FormatUtils.formatFrameRate(frameRate);

            if (isLive){
                infoVideo = "Video Quality: " + FormatUtils.formatVideoResolution(videoHeight)+ "\n\n"
                        + "Video Width: " + videoWidth + "\n\n"
                        + "Video Height: " + videoHeight + "\n";
            } else {
                infoVideo = "Video Quality: " + FormatUtils.formatVideoResolution(videoHeight)+ "\n\n"
                        + "Video Width: " + videoWidth + "\n\n"
                        + "Video Height: " + videoHeight + "\n\n"
                        + "Video Bitrate: " + videoBitrate + "\n\n"
                        + "Video Frame Rate: " + finalRate + "\n";
            }
        }
        return infoVideo;
    }

    @OptIn(markerClass = UnstableApi.class)
    public static String formatAudioFromMime(final String mimeType) {
        if (mimeType == null || mimeType.isEmpty()) {
            return "N/A";
        }
        return switch (mimeType) {
            case MimeTypes.AUDIO_DTS -> "DTS";
            case MimeTypes.AUDIO_DTS_HD -> "DTS-HD";
            case MimeTypes.AUDIO_DTS_EXPRESS -> "DTS Express";
            case MimeTypes.AUDIO_TRUEHD -> "TrueHD";
            case MimeTypes.AUDIO_AC3 -> "AC-3";
            case MimeTypes.AUDIO_E_AC3 -> "E-AC-3";
            case MimeTypes.AUDIO_E_AC3_JOC -> "E-AC-3-JOC";
            case MimeTypes.AUDIO_AC4 -> "AC-4";
            case MimeTypes.AUDIO_AAC -> "AAC";
            case MimeTypes.AUDIO_MPEG -> "MP3";
            case MimeTypes.AUDIO_MPEG_L2 -> "MP2";
            case MimeTypes.AUDIO_VORBIS -> "Vorbis";
            case MimeTypes.AUDIO_OPUS -> "Opus";
            case MimeTypes.AUDIO_FLAC -> "FLAC";
            case MimeTypes.AUDIO_ALAC -> "ALAC";
            case MimeTypes.AUDIO_WAV -> "WAV";
            case MimeTypes.AUDIO_AMR -> "AMR";
            case MimeTypes.AUDIO_AMR_NB -> "AMR-NB";
            case MimeTypes.AUDIO_AMR_WB -> "AMR-WB";
            case MimeTypes.AUDIO_MPEGH_MHA1, MimeTypes.AUDIO_MPEGH_MHM1 -> "MPEG-H";
            case MimeTypes.APPLICATION_PGS -> "PGS";
            case MimeTypes.APPLICATION_SUBRIP -> "SRT";
            case MimeTypes.TEXT_SSA -> "SSA";
            case MimeTypes.TEXT_VTT -> "VTT";
            case MimeTypes.APPLICATION_TTML -> "TTML";
            case MimeTypes.APPLICATION_TX3G -> "TX3G";
            case MimeTypes.APPLICATION_DVBSUBS -> "DVB";
            default -> mimeType;
        };
    }

    @Nullable
    public static String getRealPathFromURI(Context ctx, Uri contentUri) {
        try {
            @SuppressLint("Recycle") Cursor cursor = ctx.getContentResolver().query(contentUri,
                    null, null, null, null);
            if (cursor == null) {
                return contentUri.getPath();
            }
            cursor.moveToFirst();
            int idx = cursor.getColumnIndex(MediaStore.MediaColumns.DATA);
            return cursor.getString(idx);
        } catch (Exception e) {
            return null;
        }
    }

    @NonNull
    public static String getTimeOfTheDay(Context ctx) {
        if (ctx == null){
            return "";
        }
        String message;
        int timeOfDay = Calendar.getInstance().get(Calendar.HOUR_OF_DAY);
        if (timeOfDay < 6) {
            message = ctx.getString(R.string.title_good_night);
        } else if (timeOfDay < 12) {
            message = ctx.getString(R.string.title_good_morning);
        } else if (timeOfDay < 16) {
            message = ctx.getString(R.string.title_good_afternoon);
        } else if (timeOfDay < 20) {
            message = ctx.getString(R.string.title_good_evening);
        } else {
            message = ctx.getString(R.string.title_good_night);
        }
        return message;
    }

    public static void log(String tag, String msg) {
        // if (BuildConfig.DEBUG){
            Log.e(tag, msg);
        // }
    }

    public static void log(String tag, String msg, Exception e) {
        // if (BuildConfig.DEBUG){
            Log.e(tag, msg, e);
        // }
    }

    public static void deleteFiles(Context context, String path) {
        if (path == null) {
            return;
        }
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                Path filePath = Paths.get(path);
                Files.delete(filePath);
                Toast.makeText(context, context.getString(R.string.file_deleted), Toast.LENGTH_SHORT).show();
            } else {
                File file = new File(path);
                if (file.exists() && file.delete()) {
                    Toast.makeText(context, context.getString(R.string.file_deleted), Toast.LENGTH_SHORT).show();
                }
            }
        } catch (IOException e) {
            log("AdapterDownload", "Error deleting file", e);
        }
    }

    public static void setWifiIcon(ImageView imageView, Context context) {
        if (imageView == null || context == null){
            return;
        }
        if (!NetworkUtils.isConnected(context)) {
            imageView.setImageResource(R.drawable.ic_wifi_off);
            return;
        }
        if (NetworkUtils.isConnectedMobile(context)) {
            imageView.setImageResource(R.drawable.selector_none);
        } else if (NetworkUtils.isConnectedWifi(context)) {
            imageView.setImageResource(R.drawable.ic_wifi);
        } else if (NetworkUtils.isConnectedEthernet(context)) {
            imageView.setImageResource(R.drawable.ic_ethernet);
        }
    }

    public static int determinePageType(List<ItemCat> arrayList, int position) {
        if (arrayList == null){
            return 0;
        }
        return switch (arrayList.get(position).getId()) {
            case "01" -> 1; // Favorites
            case "02" -> 2; // Recently watched
            case "03" -> 3; // Recently added
            default -> 0; // Default category
        };
    }

    @NonNull
    @Contract("_ -> new")
    public static TextWatcher createSearchWatcher(AdapterCategory adapter) {
        return new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                // this method is empty
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                if (adapter != null) {
                    adapter.getFilter().filter(s);
                }
            }

            @Override
            public void afterTextChanged(Editable s) {
                // this method is empty
            }
        };
    }

    public static String isEmpty(StringBuilder builder, String defaultString) {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.VANILLA_ICE_CREAM) {
                return !builder.isEmpty() ? builder.toString() : defaultString;
            } else {
                return builder.toString().isEmpty() ? defaultString : builder.toString();
            }
        } catch (Exception e) {
            return defaultString;
        }
    }
}