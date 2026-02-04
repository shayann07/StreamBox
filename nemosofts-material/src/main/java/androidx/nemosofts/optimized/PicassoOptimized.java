package androidx.nemosofts.optimized;

import android.content.Context;

import com.squareup.picasso.OkHttp3Downloader;
import com.squareup.picasso.Picasso;

import java.io.File;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.util.Collections;
import java.util.List;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;

import okhttp3.Cache;
import okhttp3.ConnectionPool;
import okhttp3.Dispatcher;
import okhttp3.Dns;
import okhttp3.OkHttpClient;
import okhttp3.Protocol;

/**
 * Optimized Picasso configuration for fast image loading on all Android versions.
 * 
 * Performance optimizations for newer Android (10+):
 * - Custom DNS caching (avoids slow system DNS on Android 9+)
 * - HTTP/1.1 only (avoids HTTP/2 negotiation overhead)
 * - Connection pooling (10 idle connections, 5 min keep-alive)
 * - Parallel downloads (6 concurrent requests per host, 20 total)
 * - 10-second timeouts (fast-fail for slow images)
 * - 100MB disk cache
 */
public class PicassoOptimized {

    private static final int DISK_CACHE_SIZE = 100 * 1024 * 1024; // 100MB
    private static final int TIMEOUT_SECONDS = 10; // Fast timeout
    private static final int MAX_REQUESTS = 20; // Total concurrent requests
    private static final int MAX_REQUESTS_PER_HOST = 6; // Per-host parallel downloads
    private static final int THREAD_POOL_SIZE = 6; // Picasso decode threads
    private static final long DNS_CACHE_TTL_MS = 5 * 60 * 1000; // 5 minutes DNS cache

    /**
     * Custom DNS implementation that caches DNS lookups to avoid slow system DNS
     * on newer Android versions (9+) which use Private DNS.
     */
    private static class CachingDns implements Dns {
        private final Dns systemDns = Dns.SYSTEM;
        private final Map<String, CachedDnsEntry> cache = new ConcurrentHashMap<>();

        @Override
        public List<InetAddress> lookup(String hostname) throws UnknownHostException {
            CachedDnsEntry cached = cache.get(hostname);
            if (cached != null && !cached.isExpired()) {
                return cached.addresses;
            }
            
            // Perform actual DNS lookup
            List<InetAddress> addresses = systemDns.lookup(hostname);
            cache.put(hostname, new CachedDnsEntry(addresses));
            return addresses;
        }

        private static class CachedDnsEntry {
            final List<InetAddress> addresses;
            final long expiryTime;

            CachedDnsEntry(List<InetAddress> addresses) {
                this.addresses = addresses;
                this.expiryTime = System.currentTimeMillis() + DNS_CACHE_TTL_MS;
            }

            boolean isExpired() {
                return System.currentTimeMillis() > expiryTime;
            }
        }
    }

    /**
     * Create an optimized Picasso instance for fast image loading.
     * @param context Application context
     * @return Optimized Picasso instance
     */
    public static Picasso create(Context context) {
        try {
            // Disk cache
            File cacheDir = new File(context.getCacheDir(), "picasso-cache");
            Cache cache = new Cache(cacheDir, DISK_CACHE_SIZE);
            
            // Connection pool: reuse connections for speed
            ConnectionPool connectionPool = new ConnectionPool(
                    10, // Max idle connections
                    5, TimeUnit.MINUTES // Keep-alive duration
            );
            
            // Dispatcher: allow more parallel downloads
            Dispatcher dispatcher = new Dispatcher();
            dispatcher.setMaxRequests(MAX_REQUESTS);
            dispatcher.setMaxRequestsPerHost(MAX_REQUESTS_PER_HOST);
            
            // Build OkHttp client with speed optimizations
            OkHttpClient client = new OkHttpClient.Builder()
                    .cache(cache)
                    .connectionPool(connectionPool)
                    .dispatcher(dispatcher)
                    // Custom DNS caching to avoid slow system DNS on Android 9+
                    .dns(new CachingDns())
                    // Force HTTP/1.1 to avoid HTTP/2 negotiation overhead
                    .protocols(Collections.singletonList(Protocol.HTTP_1_1))
                    .connectTimeout(TIMEOUT_SECONDS, TimeUnit.SECONDS)
                    .readTimeout(TIMEOUT_SECONDS, TimeUnit.SECONDS)
                    .writeTimeout(TIMEOUT_SECONDS, TimeUnit.SECONDS)
                    .retryOnConnectionFailure(true)
                    .followRedirects(true)
                    .followSslRedirects(true)
                    .build();
            
            // Custom executor for faster image decoding
            ExecutorService executor = Executors.newFixedThreadPool(THREAD_POOL_SIZE);
            
            // Build Picasso with all optimizations
            return new Picasso.Builder(context)
                    .downloader(new OkHttp3Downloader(client))
                    .executor(executor)
                    .build();
        } catch (Exception e) {
            // Fallback to default Picasso if anything fails
            return new Picasso.Builder(context).build();
        }
    }
}


