package nemosofts.streambox.utils.helper;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.net.Uri;

import androidx.annotation.NonNull;
import androidx.nemosofts.BuildConfig;

import java.io.File;
import java.util.ArrayList;
import java.util.List;
import java.util.Objects;

import nemosofts.streambox.item.ItemCat;
import nemosofts.streambox.item.ItemChannel;
import nemosofts.streambox.item.ItemDns;
import nemosofts.streambox.item.ItemMovies;
import nemosofts.streambox.item.ItemPlaylist;
import nemosofts.streambox.item.ItemSelectPage;
import nemosofts.streambox.item.ItemSeries;
import nemosofts.streambox.item.ItemSingleURL;
import nemosofts.streambox.item.ItemUsersDB;
import nemosofts.streambox.item.ItemVideoDownload;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.encrypter.EncryptData;

public class DBHelper extends SQLiteOpenHelper {

    private static final String TAG = "DBHelper";
    private static final int DATABASE_VERSION = 3;
    private static final String DATABASE_NAME = BuildConfig.APPLICATION_ID + "_" + "nemosofts.db";
    private SQLiteDatabase db;
    private final EncryptData encryptData;
    final Context context;
    private final SPHelper spHelper;

    public DBHelper(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
        this.context = context;
        this.encryptData = new EncryptData(context);
        this.spHelper = new SPHelper(context);
        setWriteAheadLoggingEnabled(true); // Fix for "database is locked" errors
    }

    private static final String TAG_ID = "id";
    private static final String TAG_USER_ID = "user_id";

    private static final String TAG_AND = " AND ";
    private static final String TAG_AND_Q = "=? AND ";

    // Table ---------------------------------------------------------------------------------------
    private static final String TABLE_USERS = "users";
    private static final String TABLE_SINGLE = "single";
    public static final String TABLE_DNS_XUI = "tbl_dns_xui";
    public static final String TABLE_DNS_STREAM = "tbl_dns_stream";
    public static final String TABLE_FAV_LIVE = "fav_live";
    public static final String TABLE_FAV_MOVIE = "fav_movie";
    public static final String TABLE_FAV_SERIES = "fav_series";
    public static final String TABLE_RECENT_LIVE = "recent_live";
    public static final String TABLE_RECENT_MOVIE = "recent_movie";
    public static final String TABLE_RECENT_SERIES = "recent_series";
    public static final String TABLE_DOWNLOAD_MOVIES = "download_movie";
    public static final String TABLE_DOWNLOAD_EPISODES = "download_episode";
    public static final String TABLE_SEEK_MOVIE = "movie_seek";
    public static final String TABLE_SEEK_EPISODES = "epi_seek";
    public static final String TABLE_FILTER_LIVE = "filter_live";
    public static final String TABLE_FILTER_MOVIE = "filter_movie";
    public static final String TABLE_FILTER_SERIES = "filter_series";
    public static final String TABLE_SELECT_PAGE = "select_page";
    public static final String TABLE_PLAYLIST = "playlist";



    // TAG -----------------------------------------------------------------------------------------
    private static final String TAG_DNS_TITLE = "dns_title";
    private static final String TAG_DNS_BASE = "dns_base";

    private static final String TAG_SINGLE_ANY_NAME = "any_name";
    private static final String TAG_SINGLE_URL = "single_url";

    private static final String TAG_USERS_ANY_NAME = "any_name";
    private static final String TAG_USERS_NAME = "user_name";
    private static final String TAG_USERS_PASSWORD = "user_pass";
    private static final String TAG_USERS_URL = "user_url";
    private static final String TAG_USERS_TYPE = "user_type";

    private static final String TAG_MOVIE_STREAM_ID = "stream_id";
    private static final String TAG_MOVIE_TITLE = "title";
    private static final String TAG_MOVIE_SEEK = "seek";
    private static final String TAG_MOVIE_SEEK_FULL = "seek_full";

    private static final String TAG_SELECT_PAGE_ID = "select_page_id";
    private static final String TAG_SELECT_PAGE_TITLE = "title";
    private static final String TAG_SELECT_PAGE_TYPE = "redirect_type";
    private static final String TAG_SELECT_PAGE_DATA = "page_data";

    // FAV AND RECENT ------------------------------------------------------------------------------
    private static final String TAG_LIVE_NAME = "name";
    private static final String TAG_LIVE_ID = "stream_id";
    private static final String TAG_LIVE_ICON = "stream_icon";

    private static final String TAG_MOVIE_NAME = "name";
    private static final String TAG_MOVIE_ID = "stream_id";
    private static final String TAG_MOVIE_ICON = "stream_icon";
    private static final String TAG_MOVIE_RATING = "rating";

    private static final String TAG_SERIES_NAME = "name";
    private static final String TAG_SERIES_ID = "series_id";
    private static final String TAG_SERIES_COVER = "cover";
    private static final String TAG_SERIES_RATING = "rating";
    // DOWNLOAD ------------------------------------------------------------------------------------
    private static final String TAG_DOWNLOAD_NAME = "name";
    private static final String TAG_DOWNLOAD_ID = "stream_id";
    private static final String TAG_DOWNLOAD_ICON = "stream_icon";
    private static final String TAG_DOWNLOAD_URL = "video_url";
    private static final String TAG_DOWNLOAD_CONTAINER = "container";
    private static final String TAG_DOWNLOAD_TEMP_NAME = "temp_name";

    // FILTER --------------------------------------------------------------------------------------
    private static final String TAG_FILTER_ID = "filter_id";

    // PLAYLIST ------------------------------------------------------------------------------------
    private static final String TAG_PLAYLIST_NAME = "name";
    private static final String TAG_PLAYLIST_LOGO = "logo";
    private static final String TAG_PLAYLIST_GROUP = "group_name";
    private static final String TAG_PLAYLIST_URL = "url";
    private static final String TAG_PLAYLIST_IS_LIVE = "is_live";

    // Columns -------------------------------------------------------------------------------------
    private final String[] columnsFilter = new String[]{
            TAG_ID, TAG_USER_ID, TAG_FILTER_ID
    };
    private final String[] columnsLive = new String[]{
            TAG_ID, TAG_USER_ID, TAG_LIVE_NAME, TAG_LIVE_ID, TAG_LIVE_ICON
    };
    private final String[] columnsMovie = new String[]{
            TAG_ID, TAG_USER_ID, TAG_MOVIE_NAME, TAG_MOVIE_ID, TAG_MOVIE_ICON, TAG_MOVIE_RATING
    };
    private final String[] columnsSeries = new String[]{
            TAG_ID, TAG_USER_ID, TAG_SERIES_NAME, TAG_SERIES_ID, TAG_SERIES_COVER, TAG_SERIES_RATING
    };
    private final String[] columnsSingle = new String[]{
            TAG_ID, TAG_SINGLE_ANY_NAME, TAG_SINGLE_URL
    };
    private final String[] columnsSeek = new String[]{
            TAG_ID, TAG_USER_ID, TAG_MOVIE_STREAM_ID, TAG_MOVIE_TITLE, TAG_MOVIE_SEEK, TAG_MOVIE_SEEK_FULL
    };
    private final String[] columnsDns = new String[]{
            TAG_ID, TAG_DNS_TITLE, TAG_DNS_BASE
    };
    private final String[] columnsDownload = new String[]{
            TAG_ID, TAG_USER_ID, TAG_DOWNLOAD_NAME, TAG_DOWNLOAD_ID, TAG_DOWNLOAD_ICON,
            TAG_DOWNLOAD_URL, TAG_DOWNLOAD_CONTAINER, TAG_DOWNLOAD_TEMP_NAME
    };
    private final String[] columnsUsers = new String[]{
            TAG_USER_ID, TAG_USERS_ANY_NAME, TAG_USERS_NAME,
            TAG_USERS_PASSWORD, TAG_USERS_URL, TAG_USERS_TYPE
    };
    private final String[] columnsSelectPage = new String[]{
            TAG_ID, TAG_SELECT_PAGE_ID, TAG_SELECT_PAGE_TITLE,
            TAG_SELECT_PAGE_TYPE, TAG_SELECT_PAGE_DATA
    };

    private static final String CREATE_TABLE = "CREATE TABLE ";
    private static final String AUTOINCREMENT = " INTEGER PRIMARY KEY AUTOINCREMENT, ";
    private static final String TEXT_ONE = " TEXT,";
    private static final String TEXT_END = " TEXT";

    // Creating Table Query SELECT PAGE ------------------------------------------------------------
    private static final String CREATE_TABLE_SELECT_PAGE = CREATE_TABLE + TABLE_SELECT_PAGE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_SELECT_PAGE_ID + TEXT_ONE
            + TAG_SELECT_PAGE_TITLE + TEXT_ONE
            + TAG_SELECT_PAGE_TYPE + TEXT_ONE
            + TAG_SELECT_PAGE_DATA + TEXT_END
            + ")";

    // Creating Table Query PLAYLIST ---------------------------------------------------------------
    private static final String CREATE_TABLE_PLAYLIST = CREATE_TABLE + TABLE_PLAYLIST + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_PLAYLIST_NAME + TEXT_ONE
            + TAG_PLAYLIST_LOGO + TEXT_ONE
            + TAG_PLAYLIST_GROUP + TEXT_ONE
            + TAG_PLAYLIST_URL + TEXT_ONE
            + TAG_PLAYLIST_IS_LIVE + " INTEGER DEFAULT 0"
            + ")";
    private static final String CREATE_INDEX_PLAYLIST_GROUP = 
            "CREATE INDEX IF NOT EXISTS idx_playlist_group ON " + TABLE_PLAYLIST + "(" + TAG_PLAYLIST_GROUP + ")";
    private static final String CREATE_INDEX_PLAYLIST_IS_LIVE = 
            "CREATE INDEX IF NOT EXISTS idx_playlist_is_live ON " + TABLE_PLAYLIST + "(" + TAG_PLAYLIST_IS_LIVE + ")";

    // Creating Table Query FILTER ---------------------------------------------------------------
    private static final String CREATE_TABLE_FILTER_MOVIES = CREATE_TABLE + TABLE_FILTER_MOVIE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_FILTER_ID + TEXT_END
            + ")";
    private static final String CREATE_TABLE_FILTER_SERIES = CREATE_TABLE + TABLE_FILTER_SERIES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_FILTER_ID + TEXT_END
            + ")";
    private static final String CREATE_TABLE_FILTER_LIVE = CREATE_TABLE + TABLE_FILTER_LIVE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_FILTER_ID + TEXT_END
            + ")";

    // Creating Table Query DOWNLOAD ---------------------------------------------------------------
    private static final String CREATE_TABLE_DOWNLOAD_MOVIES = CREATE_TABLE + TABLE_DOWNLOAD_MOVIES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_DOWNLOAD_NAME + TEXT_ONE
            + TAG_DOWNLOAD_ID + TEXT_ONE
            + TAG_DOWNLOAD_ICON + TEXT_ONE
            + TAG_DOWNLOAD_URL + TEXT_ONE
            + TAG_DOWNLOAD_CONTAINER + TEXT_ONE
            + TAG_DOWNLOAD_TEMP_NAME + TEXT_END
            + ")";
    private static final String CREATE_TABLE_DOWNLOAD_EPISODES = CREATE_TABLE + TABLE_DOWNLOAD_EPISODES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_DOWNLOAD_NAME + TEXT_ONE
            + TAG_DOWNLOAD_ID + TEXT_ONE
            + TAG_DOWNLOAD_ICON + TEXT_ONE
            + TAG_DOWNLOAD_URL + TEXT_ONE
            + TAG_DOWNLOAD_CONTAINER + TEXT_ONE
            + TAG_DOWNLOAD_TEMP_NAME + TEXT_END
            + ")";

    // Creating Table Query FAV --------------------------------------------------------------------
    private static final String CREATE_TABLE_FAV_SERIES = CREATE_TABLE + TABLE_FAV_SERIES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_SERIES_NAME + TEXT_ONE
            + TAG_SERIES_ID + TEXT_ONE
            + TAG_SERIES_COVER + TEXT_ONE
            + TAG_SERIES_RATING + TEXT_END
            + ")";
    private static final String CREATE_TABLE_FAV_MOVIE = CREATE_TABLE + TABLE_FAV_MOVIE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_MOVIE_NAME + TEXT_ONE
            + TAG_MOVIE_ID + TEXT_ONE
            + TAG_MOVIE_ICON + TEXT_ONE
            + TAG_MOVIE_RATING + TEXT_END
            + ")";
    private static final String CREATE_TABLE_FAV_LIVE = CREATE_TABLE + TABLE_FAV_LIVE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_LIVE_NAME + TEXT_ONE
            + TAG_LIVE_ID + TEXT_ONE
            + TAG_LIVE_ICON + TEXT_END
            + ")";

    // Creating Table Query RECENT -----------------------------------------------------------------
    private static final String CREATE_TABLE_RECENT_SERIES = CREATE_TABLE + TABLE_RECENT_SERIES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_SERIES_NAME + TEXT_ONE
            + TAG_SERIES_ID + TEXT_ONE
            + TAG_SERIES_COVER + TEXT_ONE
            + TAG_SERIES_RATING + TEXT_END
            + ")";
    private static final String CREATE_TABLE_RECENT_MOVIE = CREATE_TABLE + TABLE_RECENT_MOVIE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_MOVIE_NAME + TEXT_ONE
            + TAG_MOVIE_ID + TEXT_ONE
            + TAG_MOVIE_ICON + TEXT_ONE
            + TAG_MOVIE_RATING + TEXT_END
            + ")";
    private static final String CREATE_TABLE_RECENT_LIVE = CREATE_TABLE + TABLE_RECENT_LIVE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_LIVE_NAME + TEXT_ONE
            + TAG_LIVE_ID + TEXT_ONE
            + TAG_LIVE_ICON + TEXT_END
            + ")";

    // Creating Table Query ------------------------------------------------------------------------
    private static final String CREATE_TABLE_DNS_XUI = CREATE_TABLE + TABLE_DNS_XUI + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_DNS_TITLE + TEXT_ONE
            + TAG_DNS_BASE + TEXT_END
            + ")";
    private static final String CREATE_TABLE_DNS_STREAM = CREATE_TABLE + TABLE_DNS_STREAM + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_DNS_TITLE + TEXT_ONE
            + TAG_DNS_BASE + TEXT_END
            + ")";

    // Creating table query ------------------------------------------------------------------------
    private static final String CREATE_TABLE_SINGLE = CREATE_TABLE + TABLE_SINGLE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_SINGLE_ANY_NAME + TEXT_ONE
            + TAG_SINGLE_URL + TEXT_END
            + ")";

    // Creating table query ------------------------------------------------------------------------
    private static final String CREATE_TABLE_USERS = CREATE_TABLE + TABLE_USERS + " ("
            + TAG_USER_ID + AUTOINCREMENT
            + TAG_USERS_ANY_NAME + TEXT_ONE
            + TAG_USERS_NAME + TEXT_ONE
            + TAG_USERS_PASSWORD + TEXT_ONE
            + TAG_USERS_URL + TEXT_ONE
            + TAG_USERS_TYPE + TEXT_END
            + ")";

    // Creating table query ------------------------------------------------------------------------
    private static final String CREATE_TABLE_MOVIE_SEEK = CREATE_TABLE + TABLE_SEEK_MOVIE + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_MOVIE_STREAM_ID + TEXT_ONE
            + TAG_MOVIE_TITLE + TEXT_ONE
            + TAG_MOVIE_SEEK + TEXT_ONE
            + TAG_MOVIE_SEEK_FULL + TEXT_END
            + ")";

    // Creating table query ------------------------------------------------------------------------
    private static final String CREATE_TABLE_EPISODES_SEEK = CREATE_TABLE + TABLE_SEEK_EPISODES + " ("
            + TAG_ID + AUTOINCREMENT
            + TAG_USER_ID + TEXT_ONE
            + TAG_MOVIE_STREAM_ID + TEXT_ONE
            + TAG_MOVIE_TITLE + TEXT_ONE
            + TAG_MOVIE_SEEK + TEXT_ONE
            + TAG_MOVIE_SEEK_FULL + TEXT_END
            + ")";


    
    private SQLiteDatabase getDb() {
        if (db == null || !db.isOpen()) {
            db = getWritableDatabase();
        }
        return db;
    }

    @Override
    public void onConfigure(SQLiteDatabase database) {
        super.onConfigure(database);
        try {
            database.enableWriteAheadLogging();
        } catch (Exception e) {
            // Ignore
        }
    }

    // Create --------------------------------------------------------------------------------------
    @Override
    public void onCreate(SQLiteDatabase database) {
        try {
            database.execSQL(CREATE_TABLE_SINGLE);
            database.execSQL(CREATE_TABLE_FAV_LIVE);
            database.execSQL(CREATE_TABLE_RECENT_LIVE);
            database.execSQL(CREATE_TABLE_MOVIE_SEEK);
            database.execSQL(CREATE_TABLE_FAV_MOVIE);
            database.execSQL(CREATE_TABLE_RECENT_MOVIE);
            database.execSQL(CREATE_TABLE_FAV_SERIES);
            database.execSQL(CREATE_TABLE_RECENT_SERIES);
            database.execSQL(CREATE_TABLE_EPISODES_SEEK);
            database.execSQL(CREATE_TABLE_DNS_XUI);
            database.execSQL(CREATE_TABLE_DNS_STREAM);
            database.execSQL(CREATE_TABLE_USERS);
            database.execSQL(CREATE_TABLE_DOWNLOAD_MOVIES);
            database.execSQL(CREATE_TABLE_DOWNLOAD_EPISODES);

            database.execSQL(CREATE_TABLE_FILTER_LIVE);
            database.execSQL(CREATE_TABLE_FILTER_MOVIES);
            database.execSQL(CREATE_TABLE_FILTER_SERIES);

            database.execSQL(CREATE_TABLE_SELECT_PAGE);

            // Playlist table with indexes for fast queries
            database.execSQL(CREATE_TABLE_PLAYLIST);
            database.execSQL(CREATE_INDEX_PLAYLIST_GROUP);
            database.execSQL(CREATE_INDEX_PLAYLIST_IS_LIVE);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error creating table", e);
        }
    }

    @Override
    public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion) {
        if (database == null){
            return;
        }
        
        // Handle version-specific upgrades (preserve data where possible)
        if (oldVersion < 3) {
            // Add new playlist table without dropping existing data
            try {
                database.execSQL(CREATE_TABLE_PLAYLIST);
                database.execSQL(CREATE_INDEX_PLAYLIST_GROUP);
                database.execSQL(CREATE_INDEX_PLAYLIST_IS_LIVE);
            } catch (Exception e) {
                // Table may already exist
                ApplicationUtil.log(TAG, "Upgrade to v3: Playlist table creation", e);
            }
        }
        
        // For major upgrades, still do full recreation
        if (newVersion > DATABASE_VERSION) {
            final String dropTable = "DROP TABLE IF EXISTS ";
            database.execSQL(dropTable + TABLE_DOWNLOAD_MOVIES);
            database.execSQL(dropTable + TABLE_DOWNLOAD_EPISODES);
            database.execSQL(dropTable + TABLE_FAV_SERIES);
            database.execSQL(dropTable + TABLE_FAV_MOVIE);
            database.execSQL(dropTable + TABLE_FAV_LIVE);
            database.execSQL(dropTable + TABLE_RECENT_SERIES);
            database.execSQL(dropTable + TABLE_RECENT_MOVIE);
            database.execSQL(dropTable + TABLE_RECENT_LIVE);
            database.execSQL(dropTable + TABLE_DNS_XUI);
            database.execSQL(dropTable + TABLE_DNS_STREAM);
            database.execSQL(dropTable + TABLE_SINGLE);
            database.execSQL(dropTable + TABLE_USERS);
            database.execSQL(dropTable + TABLE_SEEK_MOVIE);
            database.execSQL(dropTable + TABLE_SEEK_EPISODES);
            database.execSQL(dropTable + TABLE_FILTER_LIVE);
            database.execSQL(dropTable + TABLE_FILTER_MOVIE);
            database.execSQL(dropTable + TABLE_FILTER_SERIES);
            database.execSQL(dropTable + TABLE_SELECT_PAGE);
            database.execSQL(dropTable + TABLE_PLAYLIST);
            onCreate(database);
        }
    }

    // SELECT PAGE ---------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemSelectPage> getSelectPage() {
        List<ItemSelectPage> arrayList = new ArrayList<>();
        try (Cursor cursor = getDb().query(TABLE_SELECT_PAGE, columnsSelectPage, null, null,
                null, null, TAG_ID + " ASC")) {
            if (cursor.moveToFirst()) {
                do {
                    String id = cursor.getString(cursor.getColumnIndex(TAG_SELECT_PAGE_ID));
                    String name = cursor.getString(cursor.getColumnIndex(TAG_SELECT_PAGE_TITLE));
                    String type = cursor.getString(cursor.getColumnIndex(TAG_SELECT_PAGE_TYPE));
                    String url = cursor.getString(cursor.getColumnIndex(TAG_SELECT_PAGE_DATA));
                    ItemSelectPage objItem = new ItemSelectPage(id, name, type, url);
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading Page", e);
        }
        return arrayList;
    }

    public void addToPage(ItemSelectPage itemSelect) {
        if (itemSelect == null) {
            return;  // Early return if itemSelect is null, simplifying the logic
        }

        try {
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_SELECT_PAGE_ID, itemSelect.getId());
            contentValues.put(TAG_SELECT_PAGE_TITLE, itemSelect.getTitle());
            contentValues.put(TAG_SELECT_PAGE_TYPE, itemSelect.getType());
            contentValues.put(TAG_SELECT_PAGE_DATA, itemSelect.getBase().replace(" ", "%20"));
            getDb().insert(TABLE_SELECT_PAGE, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to Page", e);
        }
    }

    public void removeAllPage() {
        try {
            getDb().delete(TABLE_SELECT_PAGE, null, null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove Page", e);
        }
    }

    // FILTER --------------------------------------------------------------------------------------
    public void addToFilter(String table, String id) {
        if (table == null || id == null){
            return;
        }
        ContentValues contentValues = new ContentValues();
        contentValues.put(TAG_FILTER_ID, id);
        contentValues.put(TAG_USER_ID, spHelper.getUserId());
        getDb().insert(table, null, contentValues);
    }

    @NonNull
    private Boolean checkFilter(String table, String id) {
        if (table == null || id == null){
            return false;
        }
        try (Cursor cursor = getDb().query(table, columnsFilter, TAG_FILTER_ID + "=?" + TAG_AND
                + TAG_USER_ID
                + "=" + spHelper.getUserId(), new String[]{id}, null, null, null)) {
            return cursor.getCount() > 0;
        }
    }

    public void removeFilter(String table, String id) {
        if (table == null || id == null){
            return;
        }
        try {
            if (checkFilter(table, id)){
                getDb().delete(table, TAG_FILTER_ID + "=" + id + TAG_AND + TAG_USER_ID
                        + "=" + spHelper.getUserId(), null
                );
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove live", e);
        }
    }

    @SuppressLint("Range")
    public String getFilterIDs(String table) {
        StringBuilder filterIDs = new StringBuilder();
        if (table == null){
            return filterIDs.toString();
        }
        try (Cursor cursor = getDb().query(table, columnsFilter, TAG_USER_ID + "=" + spHelper.getUserId(),
                null, null, null, TAG_ID + " DESC", null)) {
            if (cursor.moveToFirst()) {
                do {
                    if (filterIDs.length() > 0) {
                        filterIDs.append(",");
                    }
                    filterIDs.append(cursor.getString(cursor.getColumnIndex(TAG_FILTER_ID)));
                } while (cursor.moveToNext());
            }
        }
        return filterIDs.toString();
    }

    // FAV AND RECENT ------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemChannel> getLive(String table, boolean isOrder) {
        List<ItemChannel> arrayList = new ArrayList<>();
        String orderClause = isOrder ? " ASC" : "";
        try (Cursor cursor = getDb().query(table, columnsLive, TAG_USER_ID + "=" + spHelper.getUserId(),
                null, null, null, TAG_ID + orderClause)) {
            if (cursor.moveToFirst()) {
                do {
                    String streamIcon = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_LIVE_ICON)));
                    String name = cursor.getString(cursor.getColumnIndex(TAG_LIVE_NAME));
                    String streamID = cursor.getString(cursor.getColumnIndex(TAG_LIVE_ID));

                    ItemChannel objItem = new ItemChannel(name, streamID, streamIcon, "");
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading live", e);
        }
        return arrayList;
    }

    // Live TV -------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public void addToLive(String table, ItemChannel itemChannel, int limit) {
        if (table == null){
            return;
        }
        try {
            if (TABLE_RECENT_LIVE.equals(table)) {
                // Delete excess records if needed
                try (Cursor cursorDelete = getDb().query(TABLE_RECENT_LIVE, columnsLive,
                        TAG_USER_ID + "=" + spHelper.getUserId(), null,
                        null, null, null)) {
                    if (cursorDelete.getCount() > limit) {
                        cursorDelete.moveToFirst();
                        String deleteId = cursorDelete.getString(cursorDelete.getColumnIndex(TAG_ID));
                        getDb().delete(TABLE_RECENT_LIVE, TAG_ID + "=?", new String[]{deleteId});
                    }
                }

                // Remove existing entry if the stream ID is already present
                if (checkLive(TABLE_RECENT_LIVE, itemChannel.getStreamID())) {
                    getDb().delete(TABLE_RECENT_LIVE, TAG_USER_ID + "=" + spHelper.getUserId()
                            + TAG_AND + TAG_LIVE_ID + "=?", new String[]{itemChannel.getStreamID()}
                    );
                }
            }

            // Prepare ContentValues and insert the new item
            String image = encryptData.encrypt(itemChannel.getStreamIcon().replace(" ", "%20"));
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USER_ID, spHelper.getUserId());
            contentValues.put(TAG_LIVE_NAME, itemChannel.getName());
            contentValues.put(TAG_LIVE_ID, itemChannel.getStreamID());
            contentValues.put(TAG_LIVE_ICON, image);
            db.insert(table, null, contentValues);

        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to live", e);
        }
    }

    public void removeLive(String table, String streamID) {
        if (table == null || streamID == null){
            return;
        }
        try {
            if (checkLive(table, streamID)){
                getDb().delete(table, TAG_LIVE_ID + "=" + streamID + TAG_AND + TAG_USER_ID
                        + "=" + spHelper.getUserId(), null
                );
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove live", e);
        }
    }

    public boolean checkLive(String table, String streamID) {
        if (table == null || streamID == null){
            return false;
        }
        String where = TAG_USER_ID + "=" + spHelper.getUserId() + TAG_AND + TAG_LIVE_ID + "=?";
        String[] args = {streamID};
        try (Cursor cursor = getDb().query(table, columnsLive, where, args, null, null, null)) {
            return cursor.moveToFirst();
        } catch (Exception e) {
            return false;
        }
    }

    // Movies --------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemMovies> getMovies(String table, boolean isOrder) {
        List<ItemMovies> arrayList = new ArrayList<>();
        if (table == null){
            return arrayList;
        }
        String orderClause = isOrder ? " ASC" : "";
        try (Cursor cursor = getDb().query(table, columnsMovie, TAG_USER_ID + "=" + spHelper.getUserId(),
                null, null, null, TAG_ID + orderClause)) {
            if (cursor.moveToFirst()) {
                do {
                    String streamIcon = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_MOVIE_ICON)));
                    String name = cursor.getString(cursor.getColumnIndex(TAG_MOVIE_NAME));
                    String streamID = cursor.getString(cursor.getColumnIndex(TAG_MOVIE_ID));
                    String rating = cursor.getString(cursor.getColumnIndex(TAG_MOVIE_RATING));

                    ItemMovies objItem = new ItemMovies(name, streamID, streamIcon, rating, "","");
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading movies", e);
        }
        return arrayList;
    }

    @SuppressLint("Range")
    public void addToMovie(String table, ItemMovies itemMovies, int limit) {
        if (itemMovies == null || table == null){
            return;
        }

        try {
            if (TABLE_RECENT_MOVIE.equals(table)) {
                // Delete excess records if needed
                try (Cursor cursorDelete = getDb().query(TABLE_RECENT_MOVIE, columnsMovie,
                        TAG_USER_ID + "=" + spHelper.getUserId(), null,
                        null, null, null)) {
                    if (cursorDelete.getCount() > limit) {
                        cursorDelete.moveToFirst();
                        String deleteId = cursorDelete.getString(cursorDelete.getColumnIndex(TAG_ID));
                        getDb().delete(TABLE_RECENT_MOVIE, TAG_ID + "=?", new String[]{deleteId});
                    }
                }

                // Remove existing entry if the movie is already present
                if (checkMovie(TABLE_RECENT_MOVIE, itemMovies.getStreamID())) {
                    getDb().delete(TABLE_RECENT_MOVIE, TAG_USER_ID + "=" + spHelper.getUserId()
                            + TAG_AND + TAG_MOVIE_ID + "=?", new String[]{itemMovies.getStreamID()}
                    );
                }
            }

            // Prepare ContentValues and insert the new movie item
            String image = encryptData.encrypt(itemMovies.getStreamIcon().replace(" ", "%20"));
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USER_ID, spHelper.getUserId());
            contentValues.put(TAG_MOVIE_NAME, itemMovies.getName());
            contentValues.put(TAG_MOVIE_ID, itemMovies.getStreamID());
            contentValues.put(TAG_MOVIE_ICON, image);
            contentValues.put(TAG_MOVIE_RATING, itemMovies.getRating());

            getDb().insert(table, null, contentValues);

        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to movie", e);
        }
    }

    public void removeMovie(String table, String streamID) {
        if (table == null || streamID == null){
            return;
        }
        try {
            if (checkMovie(table, streamID)){
                getDb().delete(table, TAG_USER_ID + "=" + spHelper.getUserId()
                        + TAG_AND + TAG_MOVIE_ID + "=" + streamID, null
                );
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove movie", e);
        }
    }

    public boolean checkMovie(String table, String streamID) {
        if (table == null || streamID == null){
            return false;
        }
        try (Cursor cursor = getDb().query(table, columnsMovie, TAG_USER_ID + "=" + spHelper.getUserId()
                + TAG_AND + TAG_MOVIE_ID + "=?", new String[]{streamID}, null, null, null)) {
            return cursor.getCount() > 0;
        } catch (Exception e) {
            return false;
        }
    }

    // Series --------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemSeries> getSeries(String table, boolean isOrder) {
        List<ItemSeries> arrayList = new ArrayList<>();
        if (table == null) {
            return arrayList;
        }
        String orderClause = isOrder ? " ASC" : "";
        try (Cursor cursor = getDb().query(table, columnsSeries, TAG_USER_ID + "=" + spHelper.getUserId(),
                null, null, null, TAG_ID + orderClause)) {
            if (cursor.moveToFirst()) {
                do {
                    String cover = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_SERIES_COVER)));
                    String name = cursor.getString(cursor.getColumnIndex(TAG_SERIES_NAME));
                    String seriesID = cursor.getString(cursor.getColumnIndex(TAG_SERIES_ID));
                    String rating = cursor.getString(cursor.getColumnIndex(TAG_SERIES_RATING));

                    ItemSeries objItem = new ItemSeries(name, seriesID, cover, rating,"");
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading series", e);
        }
        return arrayList;
    }

    @SuppressLint("Range")
    public void addToSeries(String table, ItemSeries itemSeries, int limit) {
        if (itemSeries == null || table == null){
            return;
        }

        try {
            if (TABLE_RECENT_SERIES.equals(table)) {
                // Delete excess records if needed
                try (Cursor cursorDelete = getDb().query(TABLE_RECENT_SERIES, columnsSeries,
                        TAG_USER_ID + "=" + spHelper.getUserId(), null,
                        null, null, null)) {
                    if (cursorDelete.getCount() > limit) {
                        cursorDelete.moveToFirst();
                        String deleteId = cursorDelete.getString(cursorDelete.getColumnIndex(TAG_ID));
                        getDb().delete(TABLE_RECENT_SERIES, TAG_ID + "=?", new String[]{deleteId});
                    }
                }

                // Remove existing entry if the series is already present
                if (checkSeries(TABLE_RECENT_SERIES, itemSeries.getSeriesID())) {
                    getDb().delete(TABLE_RECENT_SERIES,
                            TAG_SERIES_ID + "=?", new String[]{itemSeries.getSeriesID()}
                    );
                }
            }

            // Prepare ContentValues and insert the new series item
            String cover = encryptData.encrypt(itemSeries.getCover().replace(" ", "%20"));
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USER_ID, spHelper.getUserId());
            contentValues.put(TAG_SERIES_NAME, itemSeries.getName());
            contentValues.put(TAG_SERIES_ID, itemSeries.getSeriesID());
            contentValues.put(TAG_SERIES_COVER, cover);
            contentValues.put(TAG_SERIES_RATING, itemSeries.getRating());

            getDb().insert(table, null, contentValues);

        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to series", e);
        }
    }

    public void removeFavSeries(String table, String seriesID) {
        if (table == null || seriesID == null){
            return;
        }
        try {
            if (checkSeries(table, seriesID)){
                getDb().delete(table, TAG_USER_ID + "=" + spHelper.getUserId()
                        + TAG_AND + TAG_SERIES_ID + "=" + seriesID, null
                );
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove series", e);
        }
    }

    public boolean checkSeries(String table, String seriesID) {
        if (table == null || seriesID == null){
            return false;
        }
        try (Cursor cursor = getDb().query(table, columnsSeries, TAG_USER_ID + "=" + spHelper.getUserId()
                + TAG_AND + TAG_SERIES_ID + "=?", new String[]{seriesID}, null, null, null)) {
            return cursor.getCount() > 0;
        } catch (Exception e) {
            return false;
        }
    }

    // DNS -----------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemDns> loadDNS(String table) {
        List<ItemDns> arrayList = new ArrayList<>();
        if (table == null){
            return arrayList;
        }
        try (Cursor cursor = getDb().query(table, columnsDns, null, null,
                null, null, TAG_ID + " ASC")) {
            if (cursor.moveToFirst()) {
                do {
                    String name = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_DNS_TITLE)));
                    String url = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_DNS_BASE)));
                    ItemDns objItem = new ItemDns(name, url);
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading Dns", e);
        }
        return arrayList;
    }

    public void addToDNS(String table, ItemDns itemDns) {
        if (itemDns == null) {
            return;  // Early return if itemDns is null, simplifying the logic
        }

        try {
            String name = encryptData.encrypt(itemDns.getTitle());
            String url = encryptData.encrypt(itemDns.getBase().replace(" ", "%20"));

            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_DNS_TITLE, name);
            contentValues.put(TAG_DNS_BASE, url);

            getDb().insert(table, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to DNS", e);
        }
    }

    public void removeAllDNS(String table) {
        if (table == null){
            return;
        }
        try {
            getDb().delete(table, null, null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove Dns", e);
        }
    }

    // Single --------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemSingleURL> loadSingleURL() {
        List<ItemSingleURL> arrayList = new ArrayList<>();
        try (Cursor cursor = getDb().query(TABLE_SINGLE, columnsSingle, null, null,
                null, null, TAG_ID + " ASC")) {
            if (cursor.moveToFirst()) {
                do {
                    String id = cursor.getString(cursor.getColumnIndex(TAG_ID));
                    String anyName = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_SINGLE_ANY_NAME)));
                    String url = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_SINGLE_URL)));

                    arrayList.add(new ItemSingleURL(id, anyName, url));
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading Single URLs", e);
        }
        return arrayList;
    }

    public void addToSingleURL(ItemSingleURL itemSingle) {
        if (itemSingle == null){
            return;
        }

        try {
            String anyName = encryptData.encrypt(itemSingle.getAnyName());
            String url = encryptData.encrypt(itemSingle.getSingleURL().replace(" ", "%20"));

            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_SINGLE_ANY_NAME, anyName);
            contentValues.put(TAG_SINGLE_URL, url);

            getDb().insert(TABLE_SINGLE, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to Single URL", e);
        }
    }

    public void removeFromSingleURL(String singleID) {
        if (singleID == null){
            return;
        }
        try {
            getDb().delete(TABLE_SINGLE, TAG_ID + "=" + singleID, null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove Single URL", e);
        }
    }

    // Users ---------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemUsersDB> loadUsersDB() {
        List<ItemUsersDB> arrayList = new ArrayList<>();
        try (Cursor cursor = getDb().query(TABLE_USERS, columnsUsers, null, null,
                null, null, TAG_USER_ID + " ASC")) {
            if (cursor.moveToFirst()) {
                do {
                    String id = cursor.getString(cursor.getColumnIndex(TAG_USER_ID));
                    String anyName = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_USERS_ANY_NAME)));
                    String userName = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_USERS_NAME)));
                    String userPass = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_USERS_PASSWORD)));
                    String userUrl = encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_USERS_URL)));
                    String userType = cursor.getString(cursor.getColumnIndex(TAG_USERS_TYPE));

                    ItemUsersDB objItem = new ItemUsersDB(id, anyName, userName, userPass, userUrl, userType);
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading users db", e);
        }
        return arrayList;
    }

    @SuppressLint("Range")
    public String addToUserDB(ItemUsersDB itemUsersDB) {
        if (itemUsersDB == null) {
            return "0";  // Return 0 if the item is null
        }
        try {
            String anyName = encryptData.encrypt(itemUsersDB.getAnyName());
            String userName = encryptData.encrypt(itemUsersDB.getUseName());
            String userPass = encryptData.encrypt(itemUsersDB.getUserPass());
            String userUrl = encryptData.encrypt(itemUsersDB.getUserURL().replace(" ", "%20"));

            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USERS_ANY_NAME, anyName);
            contentValues.put(TAG_USERS_NAME, userName);
            contentValues.put(TAG_USERS_PASSWORD, userPass);
            contentValues.put(TAG_USERS_URL, userUrl);
            contentValues.put(TAG_USERS_TYPE, itemUsersDB.getUserType());

            // Insert the data into the database
            getDb().insert(TABLE_USERS, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to user db", e);
        }

        try (Cursor cursor = getDb().query(TABLE_USERS, new String[]{TAG_USER_ID}, null,
                null, null, null, TAG_USER_ID + " DESC", "1")) {
            if (cursor.moveToFirst()) {
                return cursor.getString(cursor.getColumnIndex(TAG_USER_ID));
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error retrieving last inserted ID", e);
        }
        return "0"; // Return the row ID or 0 if an error occurred
    }

    public void removeFromUser(String userID) {
        if (userID == null){
            return;
        }
        try {
            getDb().delete(TABLE_RECENT_LIVE, TAG_USER_ID + "=" + userID, null);
            getDb().delete(TABLE_FAV_LIVE, TAG_USER_ID + "=" + userID, null);

            getDb().delete(TABLE_RECENT_MOVIE, TAG_USER_ID + "=" + userID, null);
            getDb().delete(TABLE_FAV_MOVIE, TAG_USER_ID + "=" + userID, null);

            getDb().delete(TABLE_RECENT_SERIES, TAG_USER_ID + "=" + userID, null);
            getDb().delete(TABLE_FAV_SERIES, TAG_USER_ID + "=" + userID, null);

            getDb().delete(TABLE_SEEK_EPISODES, TAG_USER_ID + "=" + userID, null);

            getDb().delete(TABLE_SEEK_MOVIE, TAG_USER_ID + "=" + userID, null);

            getDb().delete(TABLE_USERS, TAG_USER_ID + "=" + userID, null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove user and remove all data for this user", e);
        }
    }

    // Seek Movie ----------------------------------------------------------------------------------
    public int getSeek(String table, String streamID, String streamName) {
        if (table == null || streamID == null || streamName == null){
            return 0;
        }
        String seekTo = "0";

        String whereClause = TAG_USER_ID + TAG_AND_Q + TAG_MOVIE_STREAM_ID + TAG_AND_Q + TAG_MOVIE_TITLE + "=?";
        String[] args = {
                spHelper.getUserId(),
                streamID,
                streamName.replace("'", "%27")
        };

        try (Cursor cursor = getDb().query(table, columnsSeek, whereClause, args, null, null, null)) {
            if (cursor.moveToFirst()) {
                int columnIndex = cursor.getColumnIndex(TAG_MOVIE_SEEK);
                if (columnIndex != -1) {
                    String seekValue = cursor.getString(columnIndex);
                    if (seekValue != null && !seekValue.isEmpty()) {
                        seekTo = seekValue;
                    }
                }
            }
        } catch (Exception e) {
            return 0;
        }

        try {
            return Integer.parseInt(seekTo);
        } catch (NumberFormatException e) {
            return 0;
        }
    }

    public int getSeekFull(String table, String streamID, String streamName) {
        if (table == null || streamID == null || streamName == null){
            return 0;
        }
        String seekTo = "0";

        String whereClause = TAG_USER_ID + TAG_AND_Q + TAG_MOVIE_STREAM_ID + TAG_AND_Q + TAG_MOVIE_TITLE + "=?";
        String[] args = {
                spHelper.getUserId(),
                streamID,
                streamName.replace("'", "%27")
        };
        try (Cursor cursor = getDb().query(table, columnsSeek, whereClause, args, null, null, null)) {
            if (cursor.moveToFirst()) {
                int columnIndex = cursor.getColumnIndex(TAG_MOVIE_SEEK_FULL);
                if (columnIndex != -1) {
                    String seekValue = cursor.getString(columnIndex);
                    if (seekValue != null && !seekValue.isEmpty()) {
                        seekTo = seekValue;
                    }
                }
            }
        } catch (Exception e) {
            return 0;
        }

        try {
            return Integer.parseInt(seekTo);
        } catch (NumberFormatException e) {
            return 0;
        }
    }

    public void addToSeek(String table, String currentPosition, String positionFull,
                          String streamID, String streamName) {
        if (table == null || streamID == null || streamName == null){
            return;
        }
        String whereClause = TAG_USER_ID + TAG_AND_Q + TAG_MOVIE_STREAM_ID + TAG_AND_Q + TAG_MOVIE_TITLE + "=?";
        String[] args = {
                spHelper.getUserId(),
                streamID,
                streamName.replace("'", "%27")
        };

        try {
            // Check if the seek position already exists for the stream and delete it if it does
            if (checkSeek(table, streamID, streamName)) {
                getDb().delete(table, whereClause, args);
            }

            // Prepare content values for the new seek entry
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USER_ID, spHelper.getUserId());
            contentValues.put(TAG_MOVIE_STREAM_ID, streamID);
            contentValues.put(TAG_MOVIE_TITLE, streamName);
            contentValues.put(TAG_MOVIE_SEEK, currentPosition);
            contentValues.put(TAG_MOVIE_SEEK_FULL, positionFull);

            // Insert the new entry into the table
            getDb().insert(table, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to seek", e);
        }
    }

    public boolean checkSeek(String table, String streamID, String streamName) {
        if (table == null || streamID == null || streamName == null){
            return false;
        }

        String whereClause = TAG_USER_ID + TAG_AND_Q + TAG_MOVIE_STREAM_ID + TAG_AND_Q + TAG_MOVIE_TITLE + "=?";
        String[] args = {
                spHelper.getUserId(),
                streamID,
                streamName.replace("'", "%27")
        };
        try (Cursor cursor = getDb().query(table, columnsSeek, whereClause, args, null, null, null)) {
            return cursor.moveToFirst();
        } catch (Exception e) {
            return false;
        }
    }

    public void removeSeekID(String table, String streamID, String streamName) {
        if (table == null || streamID == null || streamName == null){
            return;
        }
        try {
            if (checkSeek(table, streamID, streamName)) {
                String whereClause = TAG_USER_ID + TAG_AND_Q + TAG_MOVIE_STREAM_ID + TAG_AND_Q + TAG_MOVIE_TITLE + "=?";
                String[] args = {
                        spHelper.getUserId(),
                        streamID,
                        streamName.replace("'", "%27")
                };
                getDb().delete(table, whereClause, args);
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove seek id", e);
        }
    }

    // Download ------------------------------------------------------------------------------------
    @SuppressLint("Range")
    public List<ItemVideoDownload> loadDataDownload(String table) {
        ArrayList<ItemVideoDownload> arrayList = new ArrayList<>();
        if (table == null){
            return arrayList;
        }
        try (Cursor cursor = getDb().query(table, columnsDownload, null, null,
                null, null, "")) {
            if (cursor.moveToFirst()) {
                do {
                    String id = cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_ID));
                    String name = cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_NAME)).replace("%27", "'");
                    String imageBig = Uri.fromFile(new File(encryptData.decrypt(cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_ICON))))).toString();
                    String container = cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_CONTAINER));
                    String tempName = cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_TEMP_NAME));
                    String url = Objects.requireNonNull(context.getExternalFilesDir("")).getAbsolutePath() + File.separator + "temp/" + tempName;

                    ItemVideoDownload objItem = new ItemVideoDownload(name, id, imageBig, url, container);
                    objItem.setTempName(tempName);
                    objItem.setDownloadTable(table);
                    arrayList.add(objItem);
                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error loading download", e);
        }
        return arrayList;
    }

    public void addToDownloads(String table, ItemVideoDownload itemDownload) {
        if (itemDownload == null || table == null) {
            return; // Exit early if itemDownload is null.
        }

        try {
            // Perform necessary manipulations and encryption.
            String name = itemDownload.getName().replace("'", "%27"); // Replace single quotes with %27.
            String imageBig = encryptData.encrypt(itemDownload.getStreamIcon());
            String url = encryptData.encrypt(itemDownload.getVideoURL());

            // Create ContentValues to insert data into the database.
            ContentValues contentValues = new ContentValues();
            contentValues.put(TAG_USER_ID, spHelper.getUserId());
            contentValues.put(TAG_DOWNLOAD_ID, itemDownload.getStreamID());
            contentValues.put(TAG_DOWNLOAD_NAME, name);
            contentValues.put(TAG_DOWNLOAD_ICON, imageBig);
            contentValues.put(TAG_DOWNLOAD_URL, url);
            contentValues.put(TAG_DOWNLOAD_CONTAINER, itemDownload.getContainerExtension());
            contentValues.put(TAG_DOWNLOAD_TEMP_NAME, itemDownload.getTempName());

            // Insert the record into the database.
            getDb().insert(table, null, contentValues);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error adding to downloads", e);
        }
    }

    public Boolean checkDownload(String table, String id, String container) {
        if (table == null || id == null || container == null){
            return false;
        }
        try {
            File root = new File(Objects.requireNonNull(context.getExternalFilesDir("")).getAbsolutePath() + "/temp");
            try (Cursor cursor = getDb().query(table, columnsDownload, TAG_USER_ID + "=" + spHelper.getUserId() + TAG_AND + TAG_DOWNLOAD_ID + "=?",
                    new String[]{id}, null, null, null)) {
                if (cursor != null && cursor.moveToFirst()) {
                    @SuppressLint("Range") String filename = cursor.getString(cursor.getColumnIndex(TAG_DOWNLOAD_TEMP_NAME));
                    File file = new File(root, filename + container);
                    return file.exists();
                }
            }
        } catch (Exception e) {
            // Ignore
        }
        return false;
    }

    public void removeFromDownload(String table, String streamID) {
        if (table == null || streamID == null){
            return;
        }
        try {
            getDb().delete(table, TAG_DOWNLOAD_ID + "=" + streamID, null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error remove download", e);
        }
    }

    // clear Data ----------------------------------------------------------------------------------
    public void clearData(String table) {
        if (table == null){
            return;
        }
        try {
            getDb().delete(table, TAG_USER_ID + "=" + spHelper.getUserId(), null);
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearPlayback", e);
        }
    }

    // PLAYLIST ------------------------------------------------------------------------------------
    /**
     * Bulk insert playlist items using a transaction for optimal performance.
     * This is critical for handling 600k+ channel playlists efficiently.
     * 
     * @param items List of ItemPlaylist to insert
     * @param isLive true for live TV channels, false for movies/VOD
     */
    public void insertPlaylistBulk(List<ItemPlaylist> items, boolean isLive) {
        if (items == null || items.isEmpty()) {
            return;
        }
        
        long startTime = System.currentTimeMillis();
        SQLiteDatabase database = getDb();
        
        try {
            database.beginTransaction();
            
            // Use compiled statement for faster inserts
            String sql = "INSERT INTO " + TABLE_PLAYLIST + " (" +
                    TAG_PLAYLIST_NAME + "," +
                    TAG_PLAYLIST_LOGO + "," +
                    TAG_PLAYLIST_GROUP + "," +
                    TAG_PLAYLIST_URL + "," +
                    TAG_PLAYLIST_IS_LIVE + ") VALUES (?,?,?,?,?)";
            
            android.database.sqlite.SQLiteStatement statement = database.compileStatement(sql);
            int isLiveInt = isLive ? 1 : 0;
            
            int count = 0;
            for (ItemPlaylist item : items) {
                statement.clearBindings();
                statement.bindString(1, item.getName() != null ? item.getName() : "");
                statement.bindString(2, item.getLogo() != null ? item.getLogo() : "");
                statement.bindString(3, item.getGroup() != null ? item.getGroup() : "Uncategorized");
                statement.bindString(4, item.getUrl() != null ? item.getUrl() : "");
                statement.bindLong(5, isLiveInt);
                statement.executeInsert();
                count++;
            }
            
            database.setTransactionSuccessful();
            
            long duration = System.currentTimeMillis() - startTime;
            ApplicationUtil.log(TAG, "insertPlaylistBulk: Inserted " + count + " items in " + duration + "ms");
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error in insertPlaylistBulk", e);
        } finally {
            try {
                database.endTransaction();
            } catch (Exception e) {
                // Ignore
            }
        }
    }

    /**
     * Get distinct playlist categories (groups) with count.
     * 
     * @param isLive true for live TV categories, false for movies
     * @return List of ItemCat representing each category
     */
    @SuppressLint("Range")
    public List<ItemCat> getPlaylistCategories(boolean isLive) {
        List<ItemCat> categories = new ArrayList<>();
        
        int isLiveInt = isLive ? 1 : 0;
        String query = "SELECT " + TAG_PLAYLIST_GROUP + ", COUNT(*) as count FROM " + TABLE_PLAYLIST +
                " WHERE " + TAG_PLAYLIST_IS_LIVE + "=" + isLiveInt +
                " GROUP BY " + TAG_PLAYLIST_GROUP +
                " ORDER BY " + TAG_PLAYLIST_GROUP + " ASC";
        
        try (Cursor cursor = getDb().rawQuery(query, null)) {
            int id = 1;
            while (cursor.moveToNext()) {
                String groupName = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_GROUP));
                int count = cursor.getInt(cursor.getColumnIndex("count"));
                // Use group name as both ID and name for playlist categories
                categories.add(new ItemCat(String.valueOf(id++), groupName, String.valueOf(count)));
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error in getPlaylistCategories", e);
        }
        
        return categories;
    }

    /**
     * Get playlist items (live channels) with SQL-level pagination.
     * This avoids loading 600k items into memory.
     * 
     * @param groupName Category/group to filter by (null for all)
     * @param limit Number of items per page
     * @param offset Starting offset for pagination
     * @return List of ItemChannel for live TV
     */
    @SuppressLint("Range")
    public List<ItemChannel> getPlaylistLiveItems(String groupName, int limit, int offset) {
        List<ItemChannel> items = new ArrayList<>();
        
        String selection = TAG_PLAYLIST_IS_LIVE + "=1";
        String[] selectionArgs = null;
        
        if (groupName != null && !groupName.isEmpty()) {
            selection += " AND " + TAG_PLAYLIST_GROUP + "=?";
            selectionArgs = new String[]{groupName};
        }
        
        String orderBy = TAG_ID + " ASC LIMIT " + limit + " OFFSET " + offset;
        
        try (Cursor cursor = getDb().query(TABLE_PLAYLIST, 
                new String[]{TAG_PLAYLIST_NAME, TAG_PLAYLIST_URL, TAG_PLAYLIST_LOGO, TAG_PLAYLIST_GROUP},
                selection, selectionArgs, null, null, orderBy)) {
            
            while (cursor.moveToNext()) {
                String name = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_NAME));
                String url = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_URL));
                String logo = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_LOGO));
                String group = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_GROUP));
                items.add(new ItemChannel(name, url, logo, group));
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error in getPlaylistLiveItems", e);
        }
        
        return items;
    }

    /**
     * Get playlist items (movies/VOD) with SQL-level pagination.
     * 
     * @param groupName Category/group to filter by (null for all)
     * @param limit Number of items per page
     * @param offset Starting offset for pagination
     * @return List of ItemMovies for VOD
     */
    @SuppressLint("Range")
    public List<ItemMovies> getPlaylistMovieItems(String groupName, int limit, int offset) {
        List<ItemMovies> items = new ArrayList<>();
        
        String selection = TAG_PLAYLIST_IS_LIVE + "=0";
        String[] selectionArgs = null;
        
        if (groupName != null && !groupName.isEmpty()) {
            selection += " AND " + TAG_PLAYLIST_GROUP + "=?";
            selectionArgs = new String[]{groupName};
        }
        
        String orderBy = TAG_ID + " ASC LIMIT " + limit + " OFFSET " + offset;
        
        try (Cursor cursor = getDb().query(TABLE_PLAYLIST, 
                new String[]{TAG_PLAYLIST_NAME, TAG_PLAYLIST_URL, TAG_PLAYLIST_LOGO, TAG_PLAYLIST_GROUP},
                selection, selectionArgs, null, null, orderBy)) {
            
            while (cursor.moveToNext()) {
                String name = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_NAME));
                String url = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_URL));  // URL is stored in streamID for playlist movies
                String logo = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_LOGO));
                String group = cursor.getString(cursor.getColumnIndex(TAG_PLAYLIST_GROUP));
                // For playlist movies: streamID = URL, no rating/catID
                items.add(new ItemMovies(name, url, logo, "", group, ""));
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error in getPlaylistMovieItems", e);
        }
        
        return items;
    }

    /**
     * Clear all playlist data. Called before importing a new playlist.
     */
    public void clearPlaylist() {
        try {
            getDb().delete(TABLE_PLAYLIST, null, null);
            ApplicationUtil.log(TAG, "Playlist data cleared");
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error clearing playlist", e);
        }
    }

    /**
     * Get total count of playlist items for a category.
     */
    public int getPlaylistItemCount(String groupName, boolean isLive) {
        int isLiveInt = isLive ? 1 : 0;
        String selection = TAG_PLAYLIST_IS_LIVE + "=" + isLiveInt;
        
        if (groupName != null && !groupName.isEmpty()) {
            selection += " AND " + TAG_PLAYLIST_GROUP + "='" + groupName.replace("'", "''") + "'";
        }
        
        try (Cursor cursor = getDb().rawQuery(
                "SELECT COUNT(*) FROM " + TABLE_PLAYLIST + " WHERE " + selection, null)) {
            if (cursor.moveToFirst()) {
                return cursor.getInt(0);
            }
        } catch (Exception e) {
            ApplicationUtil.log(TAG, "Error in getPlaylistItemCount", e);
        }
        return 0;
    }

    @Override
    public synchronized void close () {
        if (db != null && db.isOpen()) {
            db.close();
            super.close();
        }
    }
}
