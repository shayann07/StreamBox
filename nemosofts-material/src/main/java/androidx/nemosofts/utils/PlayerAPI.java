package androidx.nemosofts.utils;

public class PlayerAPI {
    public static final String FORMAT_M3U8 = "m3u8";
    public static final String FORMAT_TS = "ts";

    public static String getMovieURL(String server, String user, String pass, String id, String format) {
        String cleanServer = server.endsWith("/") ? server.substring(0, server.length() - 1) : server;
        String cleanFormat = format.startsWith(".") ? format : "." + format;
        return cleanServer + "/movie/" + user + "/" + pass + "/" + id + cleanFormat;
    }

    public static String getEpisodeURL(String server, String user, String pass, String id, String format) {
        String cleanServer = server.endsWith("/") ? server.substring(0, server.length() - 1) : server;
        String cleanFormat = format.startsWith(".") ? format : "." + format;
        return cleanServer + "/series/" + user + "/" + pass + "/" + id + cleanFormat;
    }

    public static String getLiveURL(Boolean isLive, String server, String user, String pass, String id, String format) {
        String cleanServer = server.endsWith("/") ? server.substring(0, server.length() - 1) : server;
        String cleanFormat = format.startsWith(".") ? format : "." + format;
        if (Boolean.TRUE.equals(isLive)) {
             return cleanServer + "/live/" + user + "/" + pass + "/" + id + cleanFormat;
         }
         return cleanServer + "/" + user + "/" + pass + "/" + id + cleanFormat;
    }

    public static String getData(String server, String user, String pass, String action) {
        String cleanServer = server.endsWith("/") ? server.substring(0, server.length() - 1) : server;
        return cleanServer + "/player_api.php?username=" + user + "&password=" + pass + "&action=" + action;
    }
}
