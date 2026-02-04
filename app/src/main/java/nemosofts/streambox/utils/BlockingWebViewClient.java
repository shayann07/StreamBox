package nemosofts.streambox.utils;

import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import java.io.ByteArrayInputStream;

/**
 * Custom WebViewClient that blocks requests to nemosofts.com.
 * Use this to replace the default WebViewClient in any WebView that might load
 * the nemosofts license verification dialog.
 */
public class BlockingWebViewClient extends WebViewClient {

    private static final String BLOCKED_DOMAIN = "nemosofts.com";

    @Override
    public WebResourceResponse shouldInterceptRequest(WebView view, WebResourceRequest request) {
        String url = request.getUrl().toString();
        
        if (url.contains(BLOCKED_DOMAIN)) {
            ApplicationUtil.log("BlockingWebViewClient", "Blocked request to: " + url, null);
            // Return an empty response to block the request
            return new WebResourceResponse(
                    "text/html",
                    "UTF-8",
                    new ByteArrayInputStream("".getBytes())
            );
        }
        
        return super.shouldInterceptRequest(view, request);
    }

    @Override
    public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
        String url = request.getUrl().toString();
        
        if (url.contains(BLOCKED_DOMAIN)) {
            ApplicationUtil.log("BlockingWebViewClient", "Blocked URL loading: " + url, null);
            return true; // Block the navigation
        }
        
        return super.shouldOverrideUrlLoading(view, request);
    }
}
