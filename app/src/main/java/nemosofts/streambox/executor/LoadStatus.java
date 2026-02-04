package nemosofts.streambox.executor;

import org.json.JSONArray;
import org.json.JSONObject;

import nemosofts.streambox.callback.Callback;
import nemosofts.streambox.interfaces.StatusListener;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.AsyncTaskExecutor;
import okhttp3.RequestBody;

public class LoadStatus extends AsyncTaskExecutor<String, String, String> {

    private final RequestBody requestBody;
    private final StatusListener listener;
    private String success = "0";
    private String message = "";

    public LoadStatus(StatusListener listener, RequestBody requestBody) {
        this.listener = listener;
        this.requestBody = requestBody;
    }

    @Override
    protected void onPreExecute() {
        listener.onStart();
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String strings) {
        String json = "";
        try {
            json = ApplicationUtil.responsePost(Callback.API_URL, requestBody);
            if (json == null || json.trim().isEmpty()) {
                ApplicationUtil.log("LoadStatus", "Empty response from server", null);
                return "0";
            }

            // Check if looks like a CAPTCHA or HTML error page instead of JSON
            if (json.trim().startsWith("<!doctype") || json.trim().startsWith("<html")) {
                if (json.toLowerCase().contains("captcha")) {
                    message = "CAPTCHA Required (Blocked by Hosting)";
                } else {
                    message = "Invalid API Response (HTML)";
                }
                ApplicationUtil.log("LoadStatus", "Received HTML instead of JSON: " + message, null);
                return "0";
            }

            JSONObject mainJson = new JSONObject(json);
            if (mainJson.has(Callback.TAG_ROOT)) {
                Object root = mainJson.get(Callback.TAG_ROOT);
                if (root instanceof JSONArray jsonArray) {
                    for (int i = 0; i < jsonArray.length(); i++) {
                        JSONObject c = jsonArray.getJSONObject(i);
                        success = c.optString(Callback.TAG_SUCCESS, "0");
                        message = c.optString(Callback.TAG_MSG, "");
                    }
                } else if (root instanceof JSONObject jsonObject) {
                    success = jsonObject.optString(Callback.TAG_SUCCESS, "0");
                    message = jsonObject.optString(Callback.TAG_MSG, "");
                }
            }
            return "1";
        } catch (Exception e) {
            ApplicationUtil.log("LoadStatus", "Error parsing status response. Raw: " + json, e);
            return "0";
        }
    }

    @Override
    protected void onPostExecute(String s) {
        listener.onEnd(s, success, message);
    }
}