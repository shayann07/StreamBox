package androidx.nemosofts.utils;

public class FormatUtils {
    public static String readableFileSize(long size) {
        if (size <= 0) return "0";
        final String[] units = new String[]{"B", "kB", "MB", "GB", "TB"};
        int digitGroups = (int) (Math.log10(size) / Math.log10(1024));
        return new java.text.DecimalFormat("#,##0.#").format(size / Math.pow(1024, digitGroups)) + " " + units[digitGroups];
    }

    public static String formatTime(String time) {
        return time; // Stub
    }

    public static String formatTimeDuration(String duration) {
        return duration; // Stub
    }

    public static String getTimestamp(String timestamp, boolean is12Format) {
        return timestamp; // Stub
    }

    public static String getMimeType(String url) {
        return "video/mp4"; // Stub
    }

    public static String convertIntToDate(String date, String format) {
        if (date == null || date.isEmpty()) return "";
        try {
            long timestamp = Long.parseLong(date);
            // Some APIs return seconds, some milliseconds. 
            // If it's less than 10^11 it's likely seconds.
            if (timestamp < 100000000000L) {
                timestamp *= 1000;
            }
            java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat(format, java.util.Locale.getDefault());
            return sdf.format(new java.util.Date(timestamp));
        } catch (Exception e) {
            return date;
        }
    }

    public static String formatFileSize(long size) {
        return readableFileSize(size);
    }

    public static String calculateTimeSpan(String date) {
        if (date == null || date.isEmpty()) return "";
        try {
            java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("dd-MM-yyyy HH:mm:ss", java.util.Locale.getDefault());
            java.util.Date past = sdf.parse(date);
            java.util.Date now = new java.util.Date();
            long seconds = java.util.concurrent.TimeUnit.MILLISECONDS.toSeconds(now.getTime() - past.getTime());
            long minutes = java.util.concurrent.TimeUnit.MILLISECONDS.toMinutes(now.getTime() - past.getTime());
            long hours = java.util.concurrent.TimeUnit.MILLISECONDS.toHours(now.getTime() - past.getTime());
            long days = java.util.concurrent.TimeUnit.MILLISECONDS.toDays(now.getTime() - past.getTime());

            if (seconds < 60) {
                return seconds + " secs ago";
            } else if (minutes < 60) {
                return minutes + " mins ago";
            } else if (hours < 24) {
                return hours + " hours ago";
            } else {
                return days + " days ago";
            }
        } catch (Exception e) {
            return "";
        }
    }

    public static String format(int value) {
        if (value < 1000) return String.valueOf(value);
        if (value < 1000000) {
            return String.format(java.util.Locale.getDefault(), "%.1fk", value / 1000.0);
        }
        return String.format(java.util.Locale.getDefault(), "%.1fm", value / 1000000.0);
    }

    public static String formatFrameRate(float rate) {
        return String.valueOf(rate);
    }

    public static String formatVideoResolution(int height) {
        return height + "p";
    }
}
