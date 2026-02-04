package nemosofts.streambox.adapter;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.nemosofts.material.IconTextView;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import nemosofts.streambox.R;
import nemosofts.streambox.activity.DetailsMovieActivity;
import nemosofts.streambox.activity.DetailsSeriesActivity;
import nemosofts.streambox.activity.MovieActivity;
import nemosofts.streambox.activity.SeriesActivity;
import nemosofts.streambox.callback.Callback;
import nemosofts.streambox.item.ItemMovies;
import nemosofts.streambox.item.ItemPostHome;
import nemosofts.streambox.item.ItemSeries;
import nemosofts.streambox.utils.helper.SPHelper;
import androidx.nemosofts.utils.DeviceUtils;

public class AdapterHome extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private final Context context;
    private final List<ItemPostHome> arrayList;
    private final String type;
    private final RecyclerView.RecycledViewPool viewPool = new RecyclerView.RecycledViewPool();

    private static final int ITEMS_LIMIT = 7;

    private static final int VIEW_PROG = 0;
    private static final int VIEW_BANNER = 1;
    private static final int VIEW_POST = 2;


    private static final String TAG_CAT_ID = "cat_id";
    private static final String TAG_PAGE_TYPE = "pageType";

    private final Boolean isTitle;
    private final int columnWidth;
    private final int columnHeight;

    public AdapterHome(Context context, List<ItemPostHome> arrayList, String type) {
        this.context = context;
        this.arrayList = arrayList;
        this.type= type;
        this.isTitle = new SPHelper(context).getUICardTitle();
        this.columnWidth = DeviceUtils.getColumnWidth(context, 8, 0);
        this.columnHeight = (int) (columnWidth * 1.50);
        // Optimize: Increase pool size for horizontal items to reduce creation/inflation
        // Assuming default viewType is 0. 25 is enough for ~3 visible rows * 7 items
        viewPool.setMaxRecycledViews(0, 25);
    }

    private class BannerHolder extends RecyclerView.ViewHolder {

        RecyclerView rv;
        AdapterBannerMovies adapterBannerMovies;
        AdapterBannerSeries adapterBannerSeries;

        BannerHolder(View view) {
            super(view);
            rv = view.findViewById(R.id.rv_banner);
            GridLayoutManager grid = new GridLayoutManager(context, 1);
            grid.setSpanCount(2);
            rv.setLayoutManager(grid);
            rv.setItemAnimator(new DefaultItemAnimator());
            rv.setHasFixedSize(true);
            rv.setItemViewCacheSize(10);
            // Don't share pool with Banner - layout mismatch (Collision on viewType 0)
            // rv.setRecycledViewPool(viewPool);
        }
    }

    private class PostHolder extends RecyclerView.ViewHolder {

        RecyclerView rv;
        AdapterHomeMovies adapterHomeMovies;
        AdapterHomeSeries adapterHomeSeries;
        TextView title;
        IconTextView viewAll;

        PostHolder(View view) {
            super(view);
            rv = view.findViewById(R.id.rv_home_cat);
            title = view.findViewById(R.id.tv_home_title);
            viewAll = view.findViewById(R.id.btn_view_all);
            GridLayoutManager grid = new GridLayoutManager(context, 1);
            grid.setSpanCount(7);
            // Optimization: Prefetch inner items while scrolling outer list
            grid.setInitialPrefetchItemCount(7); 
            rv.setLayoutManager(grid);
            rv.setItemAnimator(new DefaultItemAnimator());
            rv.setHasFixedSize(true);
            rv.setItemViewCacheSize(4);
            rv.setRecycledViewPool(viewPool);
        }
    }

    private static class ProgressViewHolder extends RecyclerView.ViewHolder {
        private ProgressViewHolder(View v) {
            super(v);
        }
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        if (viewType == VIEW_BANNER) {
            View itemView = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.layout_home_ui_banner, parent, false);
            return new BannerHolder(itemView);
        } else if (viewType == VIEW_POST) {
            View itemView = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.layout_home_ui_list, parent, false);
            return new PostHolder(itemView);
        } else {
            View itemView = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.row_progressbar, parent, false);
            return new ProgressViewHolder(itemView);
        }
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerView.ViewHolder holder, int position) {
        if (holder instanceof BannerHolder bannerHolder) {
            bindBannerHolder(bannerHolder, position);
        } else if (holder instanceof PostHolder postHolder) {

            if (type.equals(Callback.TAG_MOVIE)){
                postHolder.title.setText(arrayList.get(holder.getAbsoluteAdapterPosition()).getTitle());

                if (postHolder.rv.getAdapter() instanceof AdapterHomeMovies) {
                    ((AdapterHomeMovies) postHolder.rv.getAdapter()).updateData(
                            arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListMovies()
                    );
                } else {
                    postHolder.adapterHomeMovies = new AdapterHomeMovies(
                            context,
                            arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListMovies(),
                            (itemMovies, position2) ->
                                    launchMovieDetails(holder.getAbsoluteAdapterPosition(), position2),
                            isTitle, columnWidth, columnHeight
                    );
                    postHolder.rv.setAdapter(postHolder.adapterHomeMovies);
                }

                postHolder.viewAll.setOnClickListener(v -> {
                    Intent intent = new Intent(context, MovieActivity.class);
                    intent.putExtra(TAG_PAGE_TYPE, MovieActivity.TAG_TYPE_ONLINE);
                    intent.putExtra(TAG_CAT_ID, arrayList.get(holder.getAbsoluteAdapterPosition()).getCatID());
                    context.startActivity(intent);
                });

                if (arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListMovies().size() >= ITEMS_LIMIT){
                    postHolder.viewAll.setVisibility(View.VISIBLE);
                } else {
                    postHolder.viewAll.setVisibility(View.GONE);
                }
            } else if (type.equals(Callback.TAG_SERIES)){
                postHolder.title.setText(arrayList.get(holder.getAbsoluteAdapterPosition()).getTitle());

                if (postHolder.rv.getAdapter() instanceof AdapterHomeSeries) {
                    ((AdapterHomeSeries) postHolder.rv.getAdapter()).updateData(
                            arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListSeries()
                    );
                } else {
                    postHolder.adapterHomeSeries = new AdapterHomeSeries(
                            context,
                            arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListSeries(),
                            (itemSeries, position4) ->
                                    launchSeriesDetails(holder.getAbsoluteAdapterPosition(), position4),
                            isTitle, columnWidth, columnHeight
                    );
                    postHolder.rv.setAdapter(postHolder.adapterHomeSeries);
                }

                postHolder.viewAll.setOnClickListener(v -> {
                    Intent intent = new Intent(context, SeriesActivity.class);
                    intent.putExtra(TAG_CAT_ID, arrayList.get(holder.getAbsoluteAdapterPosition()).getCatID());
                    context.startActivity(intent);
                });

                if (arrayList.get(holder.getAbsoluteAdapterPosition()).getArrayListSeries().size() >= ITEMS_LIMIT){
                    postHolder.viewAll.setVisibility(View.VISIBLE);
                } else {
                    postHolder.viewAll.setVisibility(View.GONE);
                }
            }
        }
    }

    private void bindBannerHolder(BannerHolder holder, int position) {
        if (type.equals(Callback.TAG_MOVIE)) {
            setupMovieBanner(holder, position);
        } else if (type.equals(Callback.TAG_SERIES)) {
            setupSeriesBanner(holder, position);
        }
    }

    private void setupMovieBanner(BannerHolder holder, int position) {
        if (holder == null){
            return;
        }
        if (holder.rv.getAdapter() instanceof AdapterBannerMovies) {
             // Assuming AdapterBannerMovies also needs updateData, but for now just recreate if simpler OR fix it too.
             // Actually, the user didn't complain about banner lag, but for consistency I should.
             // But I haven't added updateData to AdapterBannerMovies.
             // I'll leave Banner as is for now to minimize risk, or just re-create (it's only 1 row).
             // Wait, if I use ViewPool, I SHOULD reuse adapter to avoid pool thrashing?
             // No, ViewPool works across adapters if they share view types.
             // But Recreating adapter clears the RV's cache.
             // I'll stick to recreating Banner adapter for now as I haven't seen AdapterBannerMovies file.
             // The main lag is likely the many rows of Home Movies.
        }
        
        holder.adapterBannerMovies = new AdapterBannerMovies(
                context,
                arrayList.get(position).getArrayListMovies(),
                (itemMovies, pos) -> launchMovieDetails(position, pos)
        );
        holder.rv.setAdapter(holder.adapterBannerMovies);
    }

    private void setupSeriesBanner(BannerHolder holder, int position) {
        if (holder == null){
            return;
        }
        holder.adapterBannerSeries = new AdapterBannerSeries(
                context,
                arrayList.get(position).getArrayListSeries(),
                (itemSeries, pos) -> launchSeriesDetails(position, pos)
        );
        holder.rv.setAdapter(holder.adapterBannerSeries);
    }

    private void launchMovieDetails(int holderPosition, int itemPosition) {
        ItemMovies item = arrayList.get(holderPosition).getArrayListMovies().get(itemPosition);
        Intent intent = new Intent(context, DetailsMovieActivity.class);
        intent.putExtra("stream_id", item.getStreamID());
        intent.putExtra("stream_name", item.getName());
        intent.putExtra("stream_icon", item.getStreamIcon());
        intent.putExtra("stream_rating", item.getRating());
        context.startActivity(intent);
    }

    private void launchSeriesDetails(int holderPosition, int itemPosition) {
        ItemSeries item = arrayList.get(holderPosition).getArrayListSeries().get(itemPosition);
        Intent intent = new Intent(context, DetailsSeriesActivity.class);
        intent.putExtra("series_id", item.getSeriesID());
        intent.putExtra("series_name", item.getName());
        intent.putExtra("series_rating", item.getRating());
        intent.putExtra("series_cover", item.getCover());
        context.startActivity(intent);
    }

    @Override
    public long getItemId(int id) {
        return id;
    }

    @Override
    public int getItemCount() {
        return arrayList.size();
    }

    @Override
    public int getItemViewType(int position) {
        return switch (arrayList.get(position).getType()) {
            case "slider" -> VIEW_BANNER;
            case "data" -> VIEW_POST;
            default -> VIEW_PROG;
        };
    }
}