package nemosofts.streambox.executor;

import android.content.Context;

import java.util.ArrayList;
import java.util.List;

import nemosofts.streambox.interfaces.GetMovieListener;
import nemosofts.streambox.item.ItemMovies;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.AsyncTaskExecutor;
import nemosofts.streambox.utils.helper.DBHelper;

/**
 * Fetches movies from playlist using SQL-level pagination.
 * This avoids loading 600k+ items into memory.
 */
public class GetMoviesPlaylist extends AsyncTaskExecutor<String, String, String> {

    private final DBHelper dbHelper;
    private final GetMovieListener listener;
    private final ArrayList<ItemMovies> itemMovies = new ArrayList<>();
    private final String catName;
    private final int page;
    private static final int ITEMS_PER_PAGE = 20; // Increased for better UX

    public GetMoviesPlaylist(Context ctx, int page, String catName, GetMovieListener listener) {
        this.listener = listener;
        this.catName = catName;
        this.page = page;
        dbHelper = new DBHelper(ctx);
    }

    @Override
    protected void onPreExecute() {
        listener.onStart();
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String strings) {
        try {
            String targetCat = catName != null ? catName.trim() : "";
            
            // Calculate offset for SQL pagination
            int offset = (page - 1) * ITEMS_PER_PAGE;
            
            // Use SQL LIMIT/OFFSET - no memory issues!
            List<ItemMovies> items = dbHelper.getPlaylistMovieItems(targetCat, ITEMS_PER_PAGE, offset);
            
            if (items.isEmpty()) {
                return "0";
            }
            
            itemMovies.addAll(items);
            
            ApplicationUtil.log("GetMoviesPlaylist", 
                "Loaded " + items.size() + " movies for category '" + targetCat + "' (page " + page + ")", null);
            
            return "1";
        } catch (Exception e) {
            ApplicationUtil.log("GetMoviesPlaylist", "Error fetching movies playlist", e);
            return "0";
        }
    }

    @Override
    protected void onPostExecute(String s) {
        dbHelper.close();
        listener.onEnd(s, itemMovies);
    }
}