package nemosofts.streambox.executor;

import android.content.Context;
import java.util.ArrayList;
import java.util.List;
import nemosofts.streambox.interfaces.GetChannelListener;
import nemosofts.streambox.item.ItemChannel;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.helper.DBHelper;
import nemosofts.streambox.utils.AsyncTaskExecutor;

public class GetChannelPlaylist extends AsyncTaskExecutor<String, String, String> {

    private final DBHelper dbHelper;
    private final GetChannelListener listener;
    private final ArrayList<ItemChannel> itemChannels = new ArrayList<>();
    private final String catName;
    private final int page;
    private static final int ITEMS_PER_PAGE = 20;

    public GetChannelPlaylist(Context ctx, int page, String catName, GetChannelListener listener) {
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
            
            // Use SQL LIMIT/OFFSET
            List<ItemChannel> items = dbHelper.getPlaylistLiveItems(targetCat, ITEMS_PER_PAGE, offset);
            
            if (items.isEmpty()){
                return "0";
            }
            
            itemChannels.addAll(items);
            
            ApplicationUtil.log("GetChannelPlaylist", 
                "Loaded " + items.size() + " channels for category '" + targetCat + "' (page " + page + ")", null);
            
            return "1";
        } catch (Exception e) {
            ApplicationUtil.log("GetChannelPlaylist", "Error fetching channel playlist", e);
            return "0";
        }
    }

    @Override
    protected void onPostExecute(String s) {
        dbHelper.close();
        listener.onEnd(s, itemChannels);
    }
}
