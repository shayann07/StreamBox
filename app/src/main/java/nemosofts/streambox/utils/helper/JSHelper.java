package nemosofts.streambox.utils.helper;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.SharedPreferences;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.nemosofts.BuildConfig;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import nemosofts.streambox.item.ItemCat;
import nemosofts.streambox.item.ItemChannel;
import nemosofts.streambox.item.ItemMovies;
import nemosofts.streambox.item.ItemPlaylist;
import nemosofts.streambox.item.ItemSeries;
import nemosofts.streambox.utils.ApplicationUtil;

public class JSHelper {

    private static final String TAG = "JSHelper";
    private final Context context;
    private final SharedPreferences sp;

    public static final String TAG_JSON_LIVE_CAT = "json_live_cat";
    public static final String TAG_JSON_MOVIE_CAT = "json_movie_cat";
    public static final String TAG_JSON_SERIES_CAT = "json_series_cat";

    private static final String TAG_JSON_LIVE = "json_live";
    private static final String TAG_JSON_MOVIE = "json_movie";
    private static final String TAG_JSON_SERIES = "json_series";
    private static final String TAG_ORDER_LIVE = "live_order";
    private static final String TAG_ORDER_MOVIE = "movie_order";
    private static final String TAG_ORDER_SERIES = "series_order";
    private static final String TAG_ORDER_EPISODES = "episodes_order";
    private static final String TAG_UPDATE_DATE = "update_date";
    private static final String TAG_JSON_PLAYLIST = "json_playlist";
    private static final String TAG_SIZE_LIVE = "live_size_all";
    private static final String TAG_SIZE_MOVIE = "movie_size_all";
    private static final String TAG_SIZE_SERIES = "series_size_all";
    private static final String TAG_ORDER_CAT = "is_categories_order";

    private static final String TAG_CAT_ID = "category_id";
    private static final String TAG_CAT_NAME = "category_name";
    private static final String TAG_NAME = "name";
    private static final String TAG_STREAM_ID = "stream_id";
    private static final String TAG_STREAM_ICON = "stream_icon";
    private static final String TAG_STREAM_TYPE = "stream_type";
    private static final String TAG_RATING = "rating";
    private static final String TAG_SERIES_ID = "series_id";
    private static final String TAG_COVER = "cover";
    private static final String TAG_EMPTY = "";

    private static final String TAG_LIVE = "live";
    private static final String TAG_CREATED_LIVE = "created_live";
    private static final String TAG_RADIO_STREAMS = "radio_streams";
    private static final String TAG_LOGO = "logo";
    private static final String TAG_URL = "url";
    private static final String TAG_GROUP = "group";
    private static final String TAG_PLAYLIST_NAME = "playlistName";

    public JSHelper(Context ctx) {
        this.context = ctx.getApplicationContext();
        sp = this.context.getSharedPreferences(BuildConfig.APPLICATION_ID + "_" + "js_nemosofts", Context.MODE_PRIVATE);
    }

    private android.util.JsonReader getJsonReader(String fileName) throws java.io.IOException {
        java.io.File file = new java.io.File(context.getFilesDir(), fileName);
        if (!file.exists()) {
            return null;
        }
        return new android.util.JsonReader(new java.io.InputStreamReader(context.openFileInput(fileName), java.nio.charset.StandardCharsets.UTF_8));
    }

    private String checkNull(android.util.JsonReader reader) throws java.io.IOException {
        if (reader.peek() == android.util.JsonToken.NULL) {
            reader.nextNull();
            return "";
        }
        return reader.nextString();
    }

    private void saveToFile(String fileName, String data) {
        try (java.io.FileOutputStream fos = context.openFileOutput(fileName, Context.MODE_PRIVATE)) {
            fos.write(data.getBytes());
        } catch (Exception e) {
             ApplicationUtil.log(TAG, "Error saving file: " + fileName, e);
        }
    }

    private String readFromFile(String fileName) {
        java.io.File file = new java.io.File(context.getFilesDir(), fileName);
        if (!file.exists()) {
            return null;
        }
        try (java.io.FileInputStream fis = context.openFileInput(fileName);
             java.io.InputStreamReader isr = new java.io.InputStreamReader(fis);
             java.io.BufferedReader bufferedReader = new java.io.BufferedReader(isr)) {
            StringBuilder sb = new StringBuilder();
            String line;
            while ((line = bufferedReader.readLine()) != null) {
                sb.append(line);
            }
            return sb.toString();
        } catch (Exception e) {
             ApplicationUtil.log(TAG, "Error reading file: " + fileName, e);
             return null;
        }
    }

    private void deleteFile(String fileName) {
        context.deleteFile(fileName);
    }

    private SharedPreferences.Editor getEditor() {
        return sp.edit();
    }

    // Categories -------------------------------------------------------------------------------------
    public List<ItemCat> fetchAllCategories(String type) {
        ArrayList<ItemCat> allCategories = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(type)) {
            if (reader == null) {
                return allCategories;
            }
            reader.beginArray();
            while (reader.hasNext()) {
                allCategories.add(readCategory(reader));
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in fetchAllCategories", e);
        }
        return allCategories;
    }

    private ItemCat readCategory(android.util.JsonReader reader) throws java.io.IOException {
        String id = "";
        String name = "";
        reader.beginObject();
        while (reader.hasNext()) {
            String key = reader.nextName();
            if (key.equals(TAG_CAT_ID)) {
                id = checkNull(reader);
            } else if (key.equals(TAG_CAT_NAME)) {
                name = checkNull(reader);
            } else {
                reader.skipValue();
            }
        }
        reader.endObject();
        return new ItemCat(id, name, TAG_EMPTY);
    }

    public List<ItemCat> getCategory(String type, String filterIDs) {
        if (filterIDs == null || filterIDs.isEmpty()) {
            return fetchAllCategories(type);
        }

        List<Integer> filterIdList = parseFilterIDs(filterIDs);
        // Optimization: Use streaming to build the map only for needed items if possible,
        // but since we need random access for sorting, building a Map is still efficient IF the list isn't massive.
        // For now, let's stream into the map.
        Map<String, ItemCat> itemMap = new HashMap<>();

        try (android.util.JsonReader reader = getJsonReader(type)) {
            if (reader == null) return Collections.emptyList();
            reader.beginArray();
            while (reader.hasNext()) {
                ItemCat item = readCategory(reader);
                itemMap.put(item.getId(), item);
            }
            reader.endArray();
            return buildFinalCategoryList(filterIdList, itemMap);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in Categories", e);
            return Collections.emptyList();
        }
    }

    @NonNull
    private List<Integer> parseFilterIDs(String filterIDs) {
        List<Integer> filterIdList = new ArrayList<>();
        for (String idStr : filterIDs.split(",")) {
            try {
                filterIdList.add(Integer.parseInt(idStr.trim()));
            } catch (NumberFormatException e) {
                ApplicationUtil.log(TAG, "Invalid ID in filterIDs:" + idStr, e);
            }
        }
        return filterIdList;
    }

    // parseCategoriesFromJson removed as it is no longer used by getCategory

    @NonNull
    private List<ItemCat> buildFinalCategoryList(List<Integer> filterIdList, Map<String, ItemCat> itemMap) {
        List<ItemCat> finalList = new ArrayList<>();
        Set<String> processedIds  = new HashSet<>();

        // Add filtered items first
        for (Integer id : filterIdList) {
            String idStr = String.valueOf(id);
            ItemCat item = itemMap.get(idStr);
            if (item != null) {
                finalList.add(item);
                processedIds.add(idStr);
            }
        }

        // Add remaining items
        for (Map.Entry<String, ItemCat> entry : itemMap.entrySet()) {
            if (!processedIds.contains(entry.getKey())) {
                finalList.add(entry.getValue());
            }
        }

        return finalList;
    }

    public void addToSeriesCatData(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_SERIES_CAT, json);
    }

    public void addToCatLiveList(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_LIVE_CAT, json);
    }

    public void addToMovieCatData(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_MOVIE_CAT, json);
    }

    // CatchUp -------------------------------------------------------------------------------------
    public List<ItemCat> getCatchUpCategoryLive() {
        ArrayList<ItemCat> arrayList = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) {
                return arrayList;
            }
            Set<String> seenDates = new HashSet<>();
            reader.beginArray();
            while (reader.hasNext()) {
                reader.beginObject();
                String catId = "";
                int tvArchive = 0;
                while(reader.hasNext()) {
                    String name = reader.nextName();
                    if (name.equals("tv_archive")) {
                        tvArchive = reader.nextInt();
                    } else if (name.equals(TAG_CAT_ID)) {
                        catId = checkNull(reader);
                    } else {
                        reader.skipValue();
                    }
                }
                reader.endObject();

                if (tvArchive == 1 && !seenDates.contains(catId) && !catId.isEmpty()) {
                    seenDates.add(catId);
                    // This creates a recursive open/close of the cat file.
                    // It's not ideal for performance but functional for now.
                    // To optimize, we should cache categoriesMap once.
                    ItemCat cat = categoryIdList(catId);
                    if (cat != null) {
                        arrayList.add(cat);
                    }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in CatchUpCategoryLive", e);
        }
        return arrayList;
    }

    @Nullable
    private ItemCat categoryIdList(String catID) {
        if (catID == null || catID.isEmpty()){
            return null;
        }
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE_CAT)) {
             if (reader == null) return null;
             reader.beginArray();
             while (reader.hasNext()) {
                 ItemCat item = readCategory(reader);
                 if (item.getId().equals(catID)) {
                     return item;
                 }
             }
             reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in categoryIdList", e);
        }
        return null;
    }

    public List<ItemChannel> getLiveCatchUpLive(String catId) {
        ArrayList<ItemChannel> arrayList = new ArrayList<>();
        if (catId == null || catId.isEmpty()){
            return arrayList;
        }
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) return arrayList;
            
            reader.beginArray();
            while (reader.hasNext()) {
                String name = "";
                String streamID = "";
                String streamIcon = "";
                String currentCatId = "";
                String streamType = "";
                int tvArchive = 0;

                reader.beginObject();
                while (reader.hasNext()) {
                    String key = reader.nextName();
                    if (key.equals(TAG_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ID)) streamID = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
                    else if (key.equals(TAG_CAT_ID)) currentCatId = checkNull(reader);
                    else if (key.equals(TAG_STREAM_TYPE)) streamType = checkNull(reader);
                    else if (key.equals("tv_archive")) tvArchive = reader.nextInt();
                    else reader.skipValue();
                }
                reader.endObject();

                if (tvArchive == 1 && currentCatId.equals(catId) && streamType.equals(TAG_LIVE)){
                     arrayList.add(new ItemChannel(name, streamID, streamIcon, TAG_EMPTY));
                }
            }
            reader.endArray();

        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLiveCatchUpLive", e);
        }
        return arrayList;
    }

    // Live ----------------------------------------------------------------------------------------
    public List<ItemChannel> getLive(String catId) {
        ArrayList<ItemChannel> arrayList = new ArrayList<>();
        if (catId == null || catId.isEmpty()){
            return arrayList;
        }
        String targetCat = catId.trim();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                // Read basic fields
                String name = "";
                String streamID = "";
                String streamIcon = "";
                String currentCatId = "";
                String streamType = "";

                boolean isMismatch = false;

                reader.beginObject();
                while (reader.hasNext()) {
                    if (isMismatch) {
                        reader.skipValue();
                        continue;
                    }
                    String key = reader.nextName();
                    if (key.equals(TAG_CAT_ID)) {
                        currentCatId = checkNull(reader).trim(); // Trimming
                        if (!currentCatId.equalsIgnoreCase(targetCat)) {
                            isMismatch = true;
                        }
                    }
                    else if (key.equals(TAG_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ID)) streamID = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
                    else if (key.equals(TAG_STREAM_TYPE)) streamType = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (!isMismatch && currentCatId.equalsIgnoreCase(targetCat)) { // Defensive
                    if (streamType.equals(TAG_LIVE) || streamType.equals(TAG_CREATED_LIVE)) {
                        arrayList.add(new ItemChannel(name, streamID, streamIcon, TAG_EMPTY));
                    }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLive", e);
        }
        return arrayList;
    }


    public List<ItemChannel> getLiveRadio() {
         ArrayList<ItemChannel> arrayList = new ArrayList<>();
         try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                String name = "";
                String streamID = "";
                String streamIcon = "";
                String streamType = "";

                reader.beginObject();
                while(reader.hasNext()) {
                    String key = reader.nextName();
                    if (key.equals(TAG_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ID)) streamID = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
                    else if (key.equals(TAG_STREAM_TYPE)) streamType = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (streamType.equals(TAG_RADIO_STREAMS)){
                    arrayList.add(new ItemChannel(name, streamID, streamIcon, TAG_EMPTY));
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLiveRadio", e);
        }
        return arrayList;
    }

    public List<ItemChannel> getLiveRe() {
        ArrayList<ItemChannel> arrayList = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                String name = "";
                String streamID = "";
                String streamIcon = "";
                String streamType = "";

                reader.beginObject();
                while(reader.hasNext()) {
                    String key = reader.nextName();
                    if (key.equals(TAG_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ID)) streamID = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
                    else if (key.equals(TAG_STREAM_TYPE)) streamType = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (!ApplicationUtil.isAdultsCount(name)) {
                    if (streamType.equals(TAG_LIVE) || streamType.equals(TAG_CREATED_LIVE)) {
                        arrayList.add(new ItemChannel(name, streamID, streamIcon, TAG_EMPTY));
                    }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLiveRe", e);
        }
        return arrayList;
    }

    public List<ItemChannel> getLivesSearch(String searchText) {
        ArrayList<ItemChannel> arrayList = new ArrayList<>();
        if (searchText == null || searchText.isEmpty() || searchText.equals(" ") || searchText.length() == 1){
            return arrayList;
        }
        String lowerQuery = searchText.trim().toLowerCase();
        
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_LIVE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while(reader.hasNext()) {
                String name = "";
                String streamID = "";
                String streamIcon = "";
                String streamType = "";

                reader.beginObject();
                while(reader.hasNext()) {
                    String key = reader.nextName();
                    if (key.equals(TAG_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ID)) streamID = checkNull(reader);
                    else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
                    else if (key.equals(TAG_STREAM_TYPE)) streamType = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (name.toLowerCase().contains(lowerQuery) && streamType.equals(TAG_LIVE)){
                    arrayList.add(new ItemChannel(name,streamID,streamIcon,TAG_EMPTY));
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLivesSearch", e);
        }
        return arrayList;
    }

    public void addToLiveData(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_LIVE, json);
    }

    // Movies --------------------------------------------------------------------------------------
    private ItemMovies readMovie(android.util.JsonReader reader, String targetCatId) throws java.io.IOException {
        String name = "";
        String streamId = "";
        String streamIcon = "";
        String rating = "";
        String catID = "";
        boolean isMismatch = false;

        reader.beginObject();
        while (reader.hasNext()) {
            if (isMismatch) {
                reader.skipValue(); // Fast skip remaining fields
                continue;
            }
            String key = reader.nextName();
            if (key.equals(TAG_CAT_ID)) {
                catID = checkNull(reader).trim();
                if (targetCatId != null && !targetCatId.isEmpty() && !catID.equalsIgnoreCase(targetCatId)) {
                    isMismatch = true; 
                }
            }
            else if (key.equals(TAG_NAME)) name = checkNull(reader);
            else if (key.equals(TAG_STREAM_ID)) streamId = checkNull(reader);
            else if (key.equals(TAG_STREAM_ICON)) streamIcon = checkNull(reader);
            else if (key.equals(TAG_RATING)) rating = checkNull(reader);
            else reader.skipValue();
        }
        reader.endObject();
        
        if (isMismatch) return null;
        return new ItemMovies(name, streamId, streamIcon, rating, TAG_EMPTY, catID);
    }

    public List<ItemMovies> getMovies(String catId) {
        ArrayList<ItemMovies> arrayList = new ArrayList<>();
        if (catId == null || catId.isEmpty()){
            return arrayList;
        }
        String targetCat = catId.trim();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_MOVIE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                ItemMovies movie = readMovie(reader, targetCat);
                if (movie != null) {
                    arrayList.add(movie);
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getMovies", e);
        }
        return arrayList;
    }

    public List<ItemMovies> getMoviesRe() {
        ArrayList<ItemMovies> arrayList = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_MOVIE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                ItemMovies movie = readMovie(reader, "");
                if (movie != null && !ApplicationUtil.isAdultsCount(movie.getName())){
                    arrayList.add(movie);
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getMoviesRe", e);
        }
        return arrayList;
    }

    public List<ItemMovies> getMoviesSearch(String searchText) {
        ArrayList<ItemMovies> arrayList = new ArrayList<>();
        if (searchText == null || searchText.isEmpty() || searchText.equals(" ") || searchText.length() == 1){
            return arrayList;
        }
        String lowerQuery = searchText.trim().toLowerCase();

        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_MOVIE)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                ItemMovies movie = readMovie(reader, "");
                if (movie != null && movie.getName().toLowerCase().contains(lowerQuery)) {
                    arrayList.add(movie);
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getMoviesSearch", e);
        }
        return arrayList;
    }

    public void addToMovieData(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_MOVIE, json);
    }


    // Series --------------------------------------------------------------------------------------
    private ItemSeries readSeries(android.util.JsonReader reader, String targetCatId) throws java.io.IOException {
        String name = "";
        String seriesId = "";
        String cover = "";
        String rating = "";
        String categoryId = "";
        boolean isMismatch = false;

        reader.beginObject();
        while(reader.hasNext()){
            if (isMismatch) {
                reader.skipValue();
                continue;
            }
            String key = reader.nextName();
            if (key.equals(TAG_CAT_ID)) {
                categoryId = checkNull(reader).trim();
                if (targetCatId != null && !targetCatId.isEmpty() && !categoryId.equalsIgnoreCase(targetCatId)) {
                    isMismatch = true;
                }
            }
            else if (key.equals(TAG_NAME)) name = checkNull(reader);
            else if (key.equals(TAG_SERIES_ID)) seriesId = checkNull(reader);
            else if (key.equals(TAG_COVER)) cover = checkNull(reader);
            else if (key.equals(TAG_RATING)) rating = checkNull(reader);
            else reader.skipValue();
        }
        reader.endObject();
        
        if (isMismatch) return null;
        return new ItemSeries(name, seriesId, cover, rating, categoryId);
    }

    public List<ItemSeries> getSeries(String catId) {
        ArrayList<ItemSeries> arrayList = new ArrayList<>();
        if (catId == null || catId.isEmpty()){
            return arrayList;
        }
        String targetCat = catId.trim();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_SERIES)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                ItemSeries series = readSeries(reader, targetCat);
                if (series != null) {
                    arrayList.add(series);
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getSeries", e);
        }
        return arrayList;
    }

    public List<ItemSeries> getSeriesRe() {
        ArrayList<ItemSeries> arrayList = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_SERIES)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while (reader.hasNext()) {
                ItemSeries series = readSeries(reader, "");
                if (series != null && !ApplicationUtil.isAdultsCount(series.getName())){
                    arrayList.add(series);
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getSeriesRe", e);
        }
        return arrayList;
    }

    public List<ItemSeries> getSeriesSearch(String searchText) {
        ArrayList<ItemSeries> arrayList = new ArrayList<>();
        if (searchText == null || searchText.isEmpty() || searchText.equals(" ") || searchText.length() == 1){
            return arrayList;
        }
        String lowerQuery = searchText.toLowerCase();

        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_SERIES)) {
             if (reader == null) return arrayList;
             reader.beginArray();
             while (reader.hasNext()) {
                 ItemSeries series = readSeries(reader, "");
                 if (series != null && series.getName().toLowerCase().contains(lowerQuery)) {
                     arrayList.add(series);
                 }
             }
             reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getSeriesSearch", e);
        }
        return arrayList;
    }

    public void addToSeriesData(String json) {
        if (json == null) {
            return;
        }
        saveToFile(TAG_JSON_SERIES, json);
    }

    // Playlist ------------------------------------------------------------------------------------
    public void addToPlaylistData(List<ItemPlaylist> arrayListPlaylist) {
        if (arrayListPlaylist == null || arrayListPlaylist.isEmpty()) {
            ApplicationUtil.log(TAG, "addToPlaylistData: Empty or null playlist, skipping", null);
            return;
        }
        
        long startTime = System.currentTimeMillis();
        ApplicationUtil.log(TAG, "Starting to save " + arrayListPlaylist.size() + " channels to file...", null);
        
        try (java.io.FileOutputStream fos = context.openFileOutput(TAG_JSON_PLAYLIST, Context.MODE_PRIVATE);
             java.io.BufferedWriter bufferedWriter = new java.io.BufferedWriter(
                 new java.io.OutputStreamWriter(fos, java.nio.charset.StandardCharsets.UTF_8), 
                 8192); // 8KB buffer for better write performance
             android.util.JsonWriter jsonWriter = new android.util.JsonWriter(bufferedWriter)) {

            jsonWriter.setIndent("");  // No indentation for smaller file size
            jsonWriter.beginArray();
            
            int count = 0;
            for (ItemPlaylist item : arrayListPlaylist) {
                jsonWriter.beginObject();
                jsonWriter.name(TAG_PLAYLIST_NAME).value(item.getName());
                jsonWriter.name(TAG_LOGO).value(item.getLogo());
                jsonWriter.name(TAG_GROUP).value(item.getGroup());
                jsonWriter.name(TAG_URL).value(item.getUrl());
                jsonWriter.endObject();
                
                count++;
                
                // Flush buffer every 1000 items to prevent memory buildup
                if (count % 1000 == 0) {
                    jsonWriter.flush();
                    ApplicationUtil.log(TAG, "Progress: Saved " + count + "/" + arrayListPlaylist.size() + " channels", null);
                }
            }
            
            jsonWriter.endArray();
            jsonWriter.flush(); // Final flush
            
            long duration = System.currentTimeMillis() - startTime;
            ApplicationUtil.log(TAG, 
                "Successfully saved " + count + " channels to file in " + duration + "ms (" + 
                (duration / 1000.0) + " seconds)", 
                null);
                
        } catch (OutOfMemoryError e) {
            ApplicationUtil.log(TAG, "OutOfMemoryError: Playlist too large to save (" + arrayListPlaylist.size() + " channels) - " + e.getMessage());
            // Try to recover by clearing the ArrayList
            arrayListPlaylist.clear();
            System.gc(); // Suggest garbage collection
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error saving playlist data (" + arrayListPlaylist.size() + " channels)", e);
        }
    }

    public List<ItemCat> getCategoryPlaylist(int pageType) {
        ArrayList<ItemCat> arrayList = new ArrayList<>();
        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_PLAYLIST)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while(reader.hasNext()){
                String url = "";
                String group = "";

                reader.beginObject();
                while(reader.hasNext()){
                    String key = reader.nextName();
                    if (key.equals(TAG_URL)) url = checkNull(reader);
                    else if (key.equals(TAG_GROUP)) group = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (pageType == 4){
                    if (isStreamingExtension(url)){
                        arrayList.add(new ItemCat(TAG_EMPTY ,group, TAG_EMPTY));
                    }
                } else {
                    if (!isStreamingExtension(url)){
                        arrayList.add(new ItemCat(TAG_EMPTY ,group, TAG_EMPTY));
                    }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getCategoryPlaylist", e);
        }
        return arrayList;
    }

    private boolean isStreamingExtension(@NonNull String url) {
        return url.toLowerCase().contains(".ts") || url.toLowerCase().contains("/ts") || url.toLowerCase().contains(".ts?token") ||
                url.toLowerCase().contains(".m3u8") || url.toLowerCase().contains("/m3u8") ||
                url.toLowerCase().contains(".m3u8?") || url.toLowerCase().contains(".m3u8?token") ||
                url.toLowerCase().contains(".mpd") || url.toLowerCase().contains("/mpd");
    }

    public List<ItemChannel> getLivePlaylist(String targetGroup) {
        ArrayList<ItemChannel> arrayList = new ArrayList<>();
        String targetCat = targetGroup != null ? targetGroup.trim() : "";

        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_PLAYLIST)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while(reader.hasNext()){
                String name = "";
                String logo = "";
                String group = "";
                String url = "";
                boolean isMismatch = false;

                reader.beginObject();
                while(reader.hasNext()){
                    if (isMismatch) {
                        reader.skipValue();
                        continue;
                    }
                    String key = reader.nextName();
                    if (key.equals(TAG_GROUP)) {
                        group = checkNull(reader); // Group check first
                        if (!targetCat.isEmpty() && !group.equalsIgnoreCase(targetCat)) {
                            isMismatch = true;
                        }
                    }
                    else if (key.equals(TAG_PLAYLIST_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_LOGO)) logo = checkNull(reader);
                    else if (key.equals(TAG_URL)) url = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (!isMismatch && isStreamingExtension(url)){
                     // Double check group ID if it wasn't the first field (backward compatibility)
                     if (targetCat.isEmpty() || group.equalsIgnoreCase(targetCat)) {
                         arrayList.add(new ItemChannel(name, url, logo, group));
                     }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getLivePlaylist", e);
        }
        return arrayList;
    }

    public List<ItemMovies> getMoviesPlaylist(String targetGroup) {
        ArrayList<ItemMovies> arrayList = new ArrayList<>();
        String targetCat = targetGroup != null ? targetGroup.trim() : "";

        try (android.util.JsonReader reader = getJsonReader(TAG_JSON_PLAYLIST)) {
            if (reader == null) return arrayList;
            reader.beginArray();
            while(reader.hasNext()) {
                String name = "";
                String logo = "";
                String group = "";
                String url = "";
                boolean isMismatch = false;

                reader.beginObject();
                while(reader.hasNext()){
                    if (isMismatch) {
                        reader.skipValue();
                        continue;
                    }
                    String key = reader.nextName();
                    if (key.equals(TAG_GROUP)) {
                        group = checkNull(reader);
                        if (!targetCat.isEmpty() && !group.equalsIgnoreCase(targetCat)) {
                            isMismatch = true;
                        }
                    }
                    else if (key.equals(TAG_PLAYLIST_NAME)) name = checkNull(reader);
                    else if (key.equals(TAG_LOGO)) logo = checkNull(reader);
                    else if (key.equals(TAG_URL)) url = checkNull(reader);
                    else reader.skipValue();
                }
                reader.endObject();

                if (!isMismatch && !isStreamingExtension(url)){
                     // Double check group ID (backward compatibility)
                     if (targetCat.isEmpty() || group.equalsIgnoreCase(targetCat)) {
                        arrayList.add(new ItemMovies(name, url, logo, TAG_EMPTY, group, TAG_EMPTY));
                     }
                }
            }
            reader.endArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error parsing JSON in getMoviesPlaylist", e);
        }
        return arrayList;
    }

    // Remove --------------------------------------------------------------------------------------
    public void removeAllPlaylist() {
        try {
            deleteFile(TAG_JSON_PLAYLIST);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing removeAllPlaylist", e);
        }
    }
    
    public void removeAllData() {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putInt(TAG_SIZE_LIVE, 0);
            deleteFile(TAG_JSON_LIVE);
            deleteFile(TAG_JSON_LIVE_CAT);

            editor.putInt(TAG_SIZE_MOVIE, 0);
            deleteFile(TAG_JSON_MOVIE);
            deleteFile(TAG_JSON_MOVIE_CAT);

            editor.putInt(TAG_SIZE_SERIES, 0);
            deleteFile(TAG_JSON_SERIES);
            deleteFile(TAG_JSON_SERIES_CAT);

            editor.apply();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing removeAllData", e);
        }
    }

    public void removeAllSeries() {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putInt(TAG_SIZE_SERIES, 0).apply();
            deleteFile(TAG_JSON_SERIES);
            deleteFile(TAG_JSON_SERIES_CAT);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing removeAllSeries", e);
        }
    }

    public void removeAllMovies() {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putInt(TAG_SIZE_MOVIE, 0).apply();
            deleteFile(TAG_JSON_MOVIE);
            deleteFile(TAG_JSON_MOVIE_CAT);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing removeAllMovies", e);
        }
    }

    public void removeAllLive() {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putInt(TAG_SIZE_LIVE, 0).apply();
            deleteFile(TAG_JSON_LIVE);
            deleteFile(TAG_JSON_LIVE_CAT);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing removeAllLive", e);
        }
    }

    // ---------------------------------------------------------------------------------------------
    public Boolean getIsLiveOrder() {
        return getBoolean(TAG_ORDER_LIVE, false);
    }
    public void setIsLiveOrder(Boolean flag) {
        putBoolean(TAG_ORDER_LIVE, flag);
    }

    public Boolean getIsMovieOrder() {
        return getBoolean(TAG_ORDER_MOVIE, false);
    }
    public void setIsMovieOrder(Boolean flag) {
        putBoolean(TAG_ORDER_MOVIE, flag);
    }

    public Boolean getIsSeriesOrder() {
        return getBoolean(TAG_ORDER_SERIES, false);
    }
    public void setIsSeriesOrder(Boolean flag) {
        putBoolean(TAG_ORDER_SERIES, flag);
    }

    public boolean getIsCategoriesOrder() {
        return getBoolean(TAG_ORDER_CAT, false);
    }
    public void setIsCategoriesOrder(Boolean flag){
        putBoolean(TAG_ORDER_CAT, flag);
    }

    public Boolean getIsEpisodesOrder() {
        return getBoolean(TAG_ORDER_EPISODES, false);
    }
    public void setIsEpisodesOrder(Boolean flag) {
        putBoolean(TAG_ORDER_EPISODES, flag);
    }


    //Size------------------------------------------------------------------------------------------
    public int getLiveSize() {
        return getInt(TAG_SIZE_LIVE, 0);
    }
    public void setLiveSize(int size) {
        putInt(TAG_SIZE_LIVE, size);
    }

    public int getMoviesSize() {
        return getInt(TAG_SIZE_MOVIE, 0);
    }
    public void setMovieSize(int size) {
        putInt(TAG_SIZE_MOVIE, size);
    }

    public int getSeriesSize() {
        return getInt(TAG_SIZE_SERIES, 0);
    }
    public void setSeriesSize(int size) {
        putInt(TAG_SIZE_SERIES, size);
    }

    //----------------------------------------------------------------------------------------------
    @SuppressLint("SimpleDateFormat")
    public void setUpdateDate(){
        Calendar calendar = Calendar.getInstance();
        SimpleDateFormat simpleDateFormat = new SimpleDateFormat("dd-MM-yyyy HH:mm:ss");
        String currentDateTime = simpleDateFormat.format(calendar.getTime());
        putString(TAG_UPDATE_DATE, currentDateTime);
    }

    public String getUpdateDate() {
        return getString(TAG_UPDATE_DATE, TAG_EMPTY);
    }

    // Example method to put a value ---------------------------------------------------------------
    public void putString(String key, String value) {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putString(key, value).apply();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error putString "+key, e);
        }
    }

    public void putBoolean(String key, Boolean value) {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putBoolean(key, value).apply();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error putBoolean "+key, e);
        }
    }

    public void putInt(String key, int value) {
        try {
            SharedPreferences.Editor editor = getEditor();
            editor.putInt(key, value).apply();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error putInt "+key, e);
        }
    }

    // Example method to get a value ---------------------------------------------------------------
    public String getString(String key, String defaultValue) {
        try {
            return sp.getString(key, defaultValue);
        } catch (Exception e) {
            if (key.equals(TAG_UPDATE_DATE)){
                return TAG_EMPTY;
            } else {
                return null;
            }
        }
    }

    public Boolean getBoolean(String key, Boolean defaultValue) {
        try {
            return sp.getBoolean(key, defaultValue);
        } catch (Exception e) {
            return false;
        }
    }

    public int getInt(String key, int defaultValue) {
        try {
            return sp.getInt(key, defaultValue);
        } catch (Exception e) {
            return 0;
        }
    }

        // Streaming Transaction Support -------------------------------------------------------------
    private android.util.JsonWriter streamingJsonWriter;
    private java.io.BufferedWriter streamingBufferedWriter;
    private java.io.FileOutputStream streamingFos;

    public void startPlaylistTransaction() {
        try {
            streamingFos = context.openFileOutput(TAG_JSON_PLAYLIST, Context.MODE_PRIVATE);
            streamingBufferedWriter = new java.io.BufferedWriter(
                    new java.io.OutputStreamWriter(streamingFos, java.nio.charset.StandardCharsets.UTF_8),
                    8192); // 8KB
            streamingJsonWriter = new android.util.JsonWriter(streamingBufferedWriter);
            streamingJsonWriter.setIndent(""); // Minimize size
            streamingJsonWriter.beginArray();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error starting playlist transaction", e);
        }
    }

    public void writePlaylistBatch(List<ItemPlaylist> batch) {
        if (streamingJsonWriter == null) return;
        try {
            for (ItemPlaylist item : batch) {
                streamingJsonWriter.beginObject();
                // CRITICAL: Trim strings to fix "No Data Found" issues
                streamingJsonWriter.name(TAG_GROUP).value(item.getGroup().trim());
                streamingJsonWriter.name(TAG_PLAYLIST_NAME).value(item.getName().trim());
                streamingJsonWriter.name(TAG_LOGO).value(item.getLogo().trim());
                streamingJsonWriter.name(TAG_URL).value(item.getUrl().trim());
                streamingJsonWriter.endObject();
            }
            streamingJsonWriter.flush();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error writing playlist batch", e);
        }
    }

    public void endPlaylistTransaction() {
        if (streamingJsonWriter == null) return;
        try {
            streamingJsonWriter.endArray();
            streamingJsonWriter.flush();
            streamingJsonWriter.close();
            streamingBufferedWriter.close();
            streamingFos.close();
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error ending playlist transaction", e);
        } finally {
            streamingJsonWriter = null;
            streamingBufferedWriter = null;
            streamingFos = null;
        }
    }
}