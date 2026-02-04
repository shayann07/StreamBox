package nemosofts.streambox.executor;

import android.content.Context;

import androidx.annotation.NonNull;
import androidx.nemosofts.utils.PlayerAPI;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

import nemosofts.streambox.interfaces.LoadSuccessListener;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.AsyncTaskExecutor;
import nemosofts.streambox.utils.HttpsTrustManager;
import nemosofts.streambox.utils.helper.JSHelper;
import nemosofts.streambox.utils.helper.SPHelper;

public class LoadMovies extends AsyncTaskExecutor<String, String, String> {

    private final JSHelper jsHelper;
    private final SPHelper spHelper;
    private final LoadSuccessListener listener;
    private String msg = "";

    public LoadMovies(Context ctx, LoadSuccessListener listener) {
        this.listener = listener;
        jsHelper = new JSHelper(ctx);
        spHelper = new SPHelper(ctx);
    }

    @Override
    protected void onPreExecute() {
        jsHelper.removeAllMovies();
        listener.onStart();
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String string) {
        try {
            android.util.Log.e("LoadMovies", "Starting doInBackground");
            // Fetch movie categories
            String jsonCategory = fetchDataFromApi("get_vod_categories");
            android.util.Log.e("LoadMovies", "Category Response: " + jsonCategory);
            
            if (jsonCategory.isEmpty()) {
                msg = "No movie categories found";
                return "3";
            }
            if (jsonCategory.trim().startsWith("{")) {
                android.util.Log.e("LoadMovies", "Category is error object");
                try {
                    JSONObject jsonObject = new JSONObject(jsonCategory);
                    if (jsonObject.has("NEMOSOFTS_APP")) {
                         JSONArray array = jsonObject.getJSONArray("NEMOSOFTS_APP");
                         msg = array.getJSONObject(0).getString("MSG");
                    } else if (jsonObject.has("message")){
                        msg = jsonObject.getString("message");
                    } else {
                        msg = "Error: Invalid Server Response";
                    }
                } catch (Exception e) {
                     msg = "Error: Invalid JSON Format";
                }
                return "0";
            }
            
            android.util.Log.e("LoadMovies", "Calling jsHelper.addToMovieCatData");
            jsHelper.addToMovieCatData(jsonCategory);
            android.util.Log.e("LoadMovies", "Finished jsHelper.addToMovieCatData");

            // Fetch movie streams
            android.util.Log.e("LoadMovies", "Fetching streams");
            String jsonMovies = fetchDataFromApi("get_vod_streams");
            android.util.Log.e("LoadMovies", "Streams Response Length: " + jsonMovies.length());
            
            if (jsonMovies.isEmpty()) {
                msg = "No movie found";
                return "3";
            }
            if (jsonMovies.trim().startsWith("{")) {
                 try { // Similar check for movies
                    JSONObject jsonObject = new JSONObject(jsonMovies);
                    if (jsonObject.has("NEMOSOFTS_APP")) {
                         JSONArray array = jsonObject.getJSONArray("NEMOSOFTS_APP");
                         msg = array.getJSONObject(0).getString("MSG");
                    } else if (jsonObject.has("message")){
                         msg = jsonObject.getString("message");
                    }
                } catch (Exception e) {
                     msg = "Error: Invalid JSON Format";
                }
                return "0";
            }
            
            jsHelper.setMovieSize(new JSONArray(jsonMovies).length());
            jsHelper.addToMovieData(jsonMovies);

            return "1";
        } catch (Exception e) {
            android.util.Log.e("LoadMovies", "Exception in doInBackground", e);
            msg = "Please try again";
            return "0";
        }
    }

    @NonNull
    private String fetchDataFromApi(String action) {
        try {
            // Try fetching data via ApplicationUtil
            String response = ApplicationUtil.responsePost(spHelper.getAPI(),
                    ApplicationUtil.getAPIRequest(action, spHelper.getUserName(), spHelper.getPassword())
            );
            if (!response.isEmpty()) {
                return response;
            }

            // Fallback: fetch data using HTTP connection
            return performHttpRequest(action);
        } catch (Exception e) {
            return "";
        }
    }

    @NonNull
    private String performHttpRequest(String action) throws IOException {
        HttpsTrustManager.allowAllSSL();

        // Construct the URL with query parameters
        URL url = new URL(PlayerAPI.getData(spHelper.getAPI(), spHelper.getUserName(), spHelper.getPassword(), action));
        HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
        urlConnection.setRequestMethod("GET");
        urlConnection.setConnectTimeout(30000); // 30 seconds timeout
        urlConnection.setReadTimeout(30000); // 30 seconds timeout
        urlConnection.connect();

        // Read the response from InputStream
        try (InputStream inputStream = urlConnection.getInputStream();
             BufferedReader reader = new BufferedReader(new InputStreamReader(inputStream))) {

            if (inputStream == null) {
                return "";
            }

            StringBuilder stringBuilder = new StringBuilder();
            String line;

            while ((line = reader.readLine()) != null) {
                stringBuilder.append(line);
            }

            return ApplicationUtil.isEmpty(stringBuilder, "");
        }
    }

    private boolean isJSONValid(String test) {
        try {
            new JSONObject(test);
        } catch (Exception ex) {
            try {
                new JSONArray(test);
            } catch (Exception ex1) {
                return false;
            }
        }
        return true;
    }

    @Override
    protected void onPostExecute(String result) {
        listener.onEnd(result, msg);
    }
}