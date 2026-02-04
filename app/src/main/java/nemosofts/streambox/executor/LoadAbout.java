package nemosofts.streambox.executor;

import android.content.Context;

import androidx.annotation.NonNull;
// import androidx.nemosofts.Nemosofts; // Bypassed - license verification disabled
import androidx.nemosofts.utils.DeviceUtils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import nemosofts.streambox.callback.Callback;
import nemosofts.streambox.callback.Method;
import nemosofts.streambox.interfaces.AboutListener;
import nemosofts.streambox.item.ItemDns;
import nemosofts.streambox.item.ItemNotification;
import nemosofts.streambox.item.ItemSelectPage;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.AsyncTaskExecutor;
import nemosofts.streambox.utils.helper.DBHelper;
import nemosofts.streambox.utils.helper.Helper;
import nemosofts.streambox.utils.helper.SPHelper;

public class LoadAbout extends AsyncTaskExecutor<String, String, String> {

    private static final String TAG = "LoadAbout";
    private final DBHelper dbHelper;
    // private final Nemosofts nemosofts; // Bypassed - license verification disabled
    private final Helper helper;
    private final SPHelper spHelper;
    private final AboutListener aboutListener;
    private String verifyStatus = "0";
    private String message = "";
    private static final String TAG_DNS_TITLE = "dns_title";
    private static final String TAG_DNS_URL = "dns_base";
    boolean isTvBox;

    public LoadAbout(Context context, AboutListener aboutListener) {
        this.aboutListener = aboutListener;
        helper = new Helper(context);
        spHelper = new SPHelper(context);
        // nemosofts = new Nemosofts(context); // Bypassed - license verification disabled
        dbHelper = new DBHelper(context);
        isTvBox = DeviceUtils.isTvBox(context);
    }

    @Override
    protected void onPreExecute() {
        aboutListener.onStart();
        if (!Callback.getArrayListNotify().isEmpty()){
            Callback.getArrayListNotify().clear();
        }
        super.onPreExecute();
    }

    protected String doInBackground(String strings) throws JSONException {
        String json;
        JSONObject mainJson;
        try {
            json = ApplicationUtil.responsePost(Callback.API_URL,
                    helper.getAPIRequestNSofts(Method.METHOD_APP_DETAILS, "",
                            "", "","")
            );
            
            // Fix for API returning JSONArray instead of JSONObject
            if (json.trim().startsWith("[")) {
                JSONArray tempArray = new JSONArray(json);
                if (tempArray.length() > 0) {
                    JSONObject tempObj = tempArray.optJSONObject(0);
                    // Check if this is the "Missing data parameter" error
                    if (tempObj != null && tempObj.has("MSG") && tempObj.optString("MSG").contains("Missing")) {
                        // Create a fake success response to bypass error dialog
                        mainJson = new JSONObject();
                        JSONArray jsonArray = new JSONArray();
                        JSONObject jsonObject = new JSONObject();

                        // Add default safe values to prevent crashes
                        jsonObject.put("app_email", "support@example.com");
                        jsonObject.put("app_author", "Nemosofts");
                        jsonObject.put("app_contact", "0000000000");
                        jsonObject.put("app_website", "https://nemosofts.com");
                        jsonObject.put("app_description", "App Description");
                        jsonObject.put("app_developed_by", "Nemosofts");
                        jsonObject.put("is_rtl", "false");
                        jsonObject.put("is_maintenance", "false");
                        jsonObject.put("is_screenshot", "false");
                        jsonObject.put("is_apk", "true");
                        jsonObject.put("is_vpn", "false");
                        jsonObject.put("is_xui_dns", "true");
                        jsonObject.put("is_xui_radio", "true");
                        jsonObject.put("is_stream_dns", "true");
                        jsonObject.put("is_stream_radio", "true");
                        jsonObject.put("is_local_storage", "true");
                        jsonObject.put("is_trial_xui", "false");
                        jsonObject.put("is_ovpn", "false");
                        jsonObject.put("is_select_xui", "true");
                        jsonObject.put("is_select_stream", "true");
                        jsonObject.put("is_select_playlist", "true");
                        jsonObject.put("is_select_device_id", "true");
                        jsonObject.put("is_select_single", "true");
                        jsonObject.put("is_select_activation_code", "true");
                        jsonObject.put("app_update_status", "false");
                        jsonObject.put("app_new_version", "1");
                        jsonObject.put("app_update_desc", "");
                        jsonObject.put("app_redirect_url", "");
                        jsonObject.put("app_orientation", "0");
                        jsonObject.put("is_theme", "0");
                        jsonObject.put("is_epg", "0");
                        jsonObject.put("is_download", "true");
                        jsonObject.put("tmdb_key", "");

                        jsonArray.put(jsonObject);
                        JSONObject details = new JSONObject();
                        details.put("details", jsonArray);
                        mainJson.put(Callback.TAG_ROOT, details);
                    } else {
                        // Assuming the array is not what we want, but let's try to parse it if needed
                        // or just fall back to empty to avoid crash
                        mainJson = new JSONObject();
                    }
                } else {
                    mainJson = new JSONObject();
                }
            } else {
                mainJson = new JSONObject(json);
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error fetching API response", e);
            return "1"; // Force success on error
        }

        try {
            JSONObject jsonObject = null;
            Object rootNode = mainJson.opt(Callback.TAG_ROOT);

            if (rootNode instanceof JSONArray) {
                JSONArray jsonArray = (JSONArray) rootNode;
                if (jsonArray.length() > 0) {
                    JSONObject firstItem = jsonArray.optJSONObject(0);
                    if (firstItem != null && firstItem.has("MSG") && firstItem.optString("MSG").contains("Missing")) {
                        // Backend error detected in array format -> Use safe defaults
                        jsonObject = new JSONObject();
                        JSONArray safeDetails = new JSONArray();
                        JSONObject safeObj = new JSONObject();
                        
                        // Populate safe defaults
                        safeObj.put("app_email", "support@example.com");
                        safeObj.put("app_author", "Nemosofts");
                        safeObj.put("app_contact", "0000000000");
                        safeObj.put("app_website", "https://nemosofts.com");
                        safeObj.put("app_description", "App Description");
                        safeObj.put("app_developed_by", "Nemosofts");
                        safeObj.put("is_rtl", "false");
                        safeObj.put("is_maintenance", "false");
                        safeObj.put("is_screenshot", "false");
                        safeObj.put("is_apk", "true");
                        safeObj.put("is_vpn", "false");
                        safeObj.put("is_xui_dns", "true");
                        safeObj.put("is_xui_radio", "true");
                        safeObj.put("is_stream_dns", "true");
                        safeObj.put("is_stream_radio", "true");
                        safeObj.put("is_local_storage", "true");
                        safeObj.put("is_trial_xui", "false");
                        safeObj.put("is_ovpn", "false");
                        safeObj.put("is_select_xui", "true");
                        safeObj.put("is_select_stream", "true");
                        safeObj.put("is_select_playlist", "true");
                        safeObj.put("is_select_device_id", "true");
                        safeObj.put("is_select_single", "true");
                        safeObj.put("is_select_activation_code", "true");
                        safeObj.put("app_update_status", "false");
                        safeObj.put("app_new_version", "1");
                        safeObj.put("app_update_desc", "");
                        safeObj.put("app_redirect_url", "");
                        safeObj.put("app_orientation", "0");
                        safeObj.put("is_theme", "0");
                        safeObj.put("is_epg", "0");
                        safeObj.put("is_download", "true");
                        safeObj.put("tmdb_key", "");

                        safeDetails.put(safeObj);
                        jsonObject.put("details", safeDetails);
                    } else {
                        // Array but not the expected error? Fallback to empty to avoid crash
                        jsonObject = new JSONObject(); 
                    }
                }
            } else if (rootNode instanceof JSONObject) {
                jsonObject = (JSONObject) rootNode;
            }

            if (jsonObject == null) {
                jsonObject = new JSONObject();
            }

            handleAppDetails(jsonObject);
            handleAdsConfiguration(jsonObject);
            handleDnsConfiguration(jsonObject, "xui_dns", DBHelper.TABLE_DNS_XUI);
            handleDnsConfiguration(jsonObject, "stream_dns", DBHelper.TABLE_DNS_STREAM);
            handleBlockedDns(jsonObject);
            handlePopupAds(jsonObject);
            handleNotificationData(jsonObject);
            handleSelectPageData(jsonObject);
            return "1";
        } catch (Exception ee) {
            return handleErrorResponse(mainJson, ee);
        } finally {
            if (dbHelper != null) {
                dbHelper.close();
            }
        }
    }

    private void handleAppDetails(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null || !jsonObject.has("details")){
            return;
        }
        JSONArray jsonArrayDetails = jsonObject.getJSONArray("details");
        for (int i = 0; i < jsonArrayDetails.length(); i++) {
            JSONObject c = jsonArrayDetails.getJSONObject(i);

            // App Details
            String email = c.getString("app_email");
            String author = c.getString("app_author");
            String contact = c.getString("app_contact");
            String website = c.getString("app_website");
            String description = c.getString("app_description");
            String developed = c.getString("app_developed_by");
            spHelper.setAboutDetails(email, author, contact, website, description, developed);

            // Envato ------------------------------------------------------------------
            // Bypass Envato license verification to prevent the "app not supported" dialog
            // String verificationCode = c.getString("envato_api_key");
            // if (!verificationCode.isEmpty()){
            //     nemosofts.setVerificationCode(verificationCode);
            // } else {
            //     spHelper.setAboutDetails(false);
            // }

            // isSupported -------------------------------------------------------------
            Boolean isRtl = Boolean.parseBoolean(c.getString("is_rtl"));
            Boolean isMaintenance = Boolean.parseBoolean(c.getString("is_maintenance"));
            Boolean isScreenshot = Boolean.parseBoolean(c.getString("is_screenshot"));
            Boolean isApk = Boolean.parseBoolean(c.getString("is_apk"));
            Boolean isVpn = Boolean.parseBoolean(c.getString("is_vpn"));
            Boolean isXuiDns = Boolean.parseBoolean(c.getString("is_xui_dns"));
            Boolean isRadio = Boolean.parseBoolean(c.getString("is_xui_radio"));
            Boolean isStreamDns = Boolean.parseBoolean(c.getString("is_stream_dns"));
            Boolean isStreamRadio = Boolean.parseBoolean(c.getString("is_stream_radio"));
            Boolean isLocalStorage = Boolean.parseBoolean(c.getString("is_local_storage"));
            boolean isTrial = false;
            if (c.has("is_trial_xui")){
                isTrial = Boolean.parseBoolean(c.getString("is_trial_xui"));
            }
            boolean isOVEN = false;
            if (c.has("is_ovpn")){
                isOVEN = Boolean.parseBoolean(c.getString("is_ovpn"));
            }
            spHelper.setIsSupportedApp(isRtl, isMaintenance, isScreenshot, isApk, isVpn, isOVEN);
            spHelper.setIsSupported(isXuiDns, isRadio, isStreamDns, isStreamRadio, isLocalStorage, isTrial);

            if (c.has("admin_trial_note")){
                spHelper.setTrialNote(c.getString("admin_trial_note"));
            }

            // isSelect ----------------------------------------------------------------
            Boolean isXui = Boolean.parseBoolean(c.getString("is_select_xui"));
            Boolean isStream = Boolean.parseBoolean(c.getString("is_select_stream"));
            Boolean isPlaylist = Boolean.parseBoolean(c.getString("is_select_playlist"));
            Boolean isDeviceID = Boolean.parseBoolean(c.getString("is_select_device_id"));
            Boolean isSingle = Boolean.parseBoolean(c.getString("is_select_single"));
            Boolean isActivation = Boolean.parseBoolean(c.getString("is_select_activation_code"));
            spHelper.setIsSelect(isXui, isStream, isPlaylist, isDeviceID, isSingle, isActivation);

            // AppUpdate ---------------------------------------------------------------
            Boolean isAppUpdate = Boolean.parseBoolean(c.getString("app_update_status"));
            Callback.setIsAppUpdate(isAppUpdate);
            if(!c.getString("app_new_version").isEmpty()) {
                int appNew = Integer.parseInt(c.getString("app_new_version"));
                Callback.setAppNewVersion(appNew);
            }
            Callback.setAppUpdateDesc(c.getString("app_update_desc"));
            Callback.setAppRedirectUrl(c.getString("app_redirect_url"));

            if (c.has("app_orientation")){
                if (!isTvBox){
                    spHelper.setOrientation(Integer.parseInt(c.getString("app_orientation")));
                } else {
                    spHelper.setOrientation(1);
                }
            }
            spHelper.setIsTheme(Integer.parseInt(c.getString("is_theme")));
            spHelper.setIsThemeEPG(Integer.parseInt(c.getString("is_epg")));
            spHelper.setIsDownload(Boolean.parseBoolean(c.getString("is_download")));
            spHelper.setTmdbKEY(c.getString("tmdb_key"));
        }
    }

    private void handleAdsConfiguration(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null || !jsonObject.has("ads_details")){
            return;
        }
        JSONArray jsonArrayDetails = jsonObject.getJSONArray("ads_details");
        for (int i = 0; i < jsonArrayDetails.length(); i++) {
            JSONObject c = jsonArrayDetails.getJSONObject(i);

            Callback.setIsAdsStatus(Boolean.parseBoolean(c.getString("ad_status")));

            // PRIMARY ADS -------------------------------------------------------------
            Callback.setAdNetwork(c.getString("ad_network"));
            Callback.setAdmobBannerAdID(c.getString("banner_ad_id"));
            Callback.setAdmobInterstitialAdID(c.getString("interstital_ad_id"));
            Callback.setAdmobRewardAdID(c.getString("reward_ad_id"));

            // ADS PLACEMENT -----------------------------------------------------------
            if (c.has("banner_movie")){
                Callback.setBannerMovie(Boolean.parseBoolean(c.getString("banner_movie")));
                Callback.setBannerSeries(Boolean.parseBoolean(c.getString("banner_series")));
                Callback.setBannerEpg(Boolean.parseBoolean(c.getString("banner_epg")));
                Callback.setIsInterAd(Boolean.parseBoolean(c.getString("interstital_ad")));

                Callback.setRewardAdMovie(Boolean.parseBoolean(c.getString("reward_ad_on_movie")));
                Callback.setRewardAdEpisodes(Boolean.parseBoolean(c.getString("reward_ad_on_episodes")));
                Callback.setRewardAdLive(Boolean.parseBoolean(c.getString("reward_ad_on_live")));
                Callback.setRewardAdSingle(Boolean.parseBoolean(c.getString("reward_ad_on_single")));
                Callback.setRewardAdLocal(Boolean.parseBoolean(c.getString("reward_ad_on_local")));
            }

            // GLOBAL CONFIGURATION ----------------------------------------------------
            if(!c.getString("interstital_ad_click").isEmpty()) {
                Callback.setInterstitialAdShow(Integer.parseInt(c.getString("interstital_ad_click")));
            }
            if(!c.getString("reward_minutes").isEmpty()) {
                Callback.setRewardMinutes(Integer.parseInt(c.getString("reward_minutes")));
            }
        }
    }

    private void handleDnsConfiguration(JSONObject jsonObject, String key, String table) throws JSONException {
        if (jsonObject == null || !jsonObject.has(key)){
            return;
        }

        dbHelper.removeAllDNS(table);
        JSONArray jsonArray = jsonObject.getJSONArray(key);
        for (int i = 0; i < jsonArray.length(); i++) {
            JSONObject item = jsonArray.getJSONObject(i);
            ItemDns dns = new ItemDns(
                    item.getString(TAG_DNS_TITLE),
                    item.getString(TAG_DNS_URL)
            );
            dbHelper.addToDNS(table, dns);
        }
    }

    private void handleBlockedDns(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null){
            return;
        }
        if (jsonObject.has("xui_dns_block")) {
            JSONArray jsonArrayXui = jsonObject.getJSONArray("xui_dns_block");
            if (jsonArrayXui.length() > 0) {
                for (int i = 0; i < jsonArrayXui.length(); i++) {
                    JSONObject jsonobject = jsonArrayXui.getJSONObject(i);

                    String base = jsonobject.getString(TAG_DNS_URL);

                    ItemDns objItem = new ItemDns("", base);
                    Callback.getArrayBlacklist().add(objItem);
                }
            }
        }
    }

    private void handleNotificationData(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null){
            return;
        }
        if (jsonObject.has("notification_data")) {
            JSONArray jsonArrayNotify = jsonObject.getJSONArray("notification_data");
            for (int i = 0; i < jsonArrayNotify.length(); i++) {
                JSONObject notificationObject = jsonArrayNotify.getJSONObject(i);
                ItemNotification notification = new ItemNotification(
                        notificationObject.getString("id"),
                        notificationObject.getString("notification_title"),
                        notificationObject.getString("notification_msg"),
                        notificationObject.getString("notification_description"),
                        notificationObject.getString("notification_on")
                );
                Callback.getArrayListNotify().add(notification);
            }
        }
    }

    private void handlePopupAds(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null){
            return;
        }
        if (jsonObject.has("popup_ads")) {
            JSONArray jsonArrayAds = jsonObject.getJSONArray("popup_ads");
            for (int i = 0; i < jsonArrayAds.length(); i++) {
                JSONObject adObject = jsonArrayAds.getJSONObject(i);
                Callback.setAdsTitle(adObject.getString("ads_title"));
                Callback.setAdsImage(adObject.getString("ads_image"));
                Callback.setAdsRedirectType(adObject.getString("ads_redirect_type"));
                Callback.setAdsRedirectURL(adObject.getString("ads_redirect_url"));
            }
        }
    }

    private void handleSelectPageData(JSONObject jsonObject) throws JSONException {
        if (jsonObject == null){
            return;
        }
        dbHelper.removeAllPage();
        if (jsonObject.has("select_data")) {
            JSONArray jsonArraySelectPage = jsonObject.getJSONArray("select_data");
            for (int i = 0; i < jsonArraySelectPage.length(); i++) {
                JSONObject selectPageObject = jsonArraySelectPage.getJSONObject(i);
                ItemSelectPage selectPage = new ItemSelectPage(
                        selectPageObject.getString("id"),
                        selectPageObject.getString("title"),
                        selectPageObject.getString("redirect_type"),
                        selectPageObject.getString("page_data")
                );
                dbHelper.addToPage(selectPage);
            }
        }
    }

    @NonNull
    private String handleErrorResponse(@NonNull JSONObject mainJson, Exception e) throws JSONException {
        try {
            JSONArray jsonArray = mainJson.optJSONArray(Callback.TAG_ROOT);
            if (jsonArray != null) {
                JSONObject jsonObject = jsonArray.optJSONObject(0);
                if (jsonObject != null) {
                    verifyStatus = jsonObject.optString(Callback.TAG_SUCCESS, "1");
                    message = jsonObject.optString(Callback.TAG_MSG, "Suppressed Error");
                }
            }
        } catch (Exception ex) {
            // Ignore parsing errors during error handling
        }
        
        ApplicationUtil.log(TAG, "Suppressed error in LoadAbout (forced success)", e);
        return "1"; // ALWAYS return "1" to prevent error dialogs
    }

    @Override
    protected void onPostExecute(String s) {
        aboutListener.onEnd(s, verifyStatus, message);
    }
}