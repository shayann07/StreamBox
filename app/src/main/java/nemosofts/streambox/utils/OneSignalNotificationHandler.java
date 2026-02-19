package nemosofts.streambox.utils;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.content.Context;
import android.os.Build;

import androidx.core.app.NotificationCompat;

import com.onesignal.notifications.INotificationReceivedEvent;
import com.onesignal.notifications.INotificationServiceExtension;

/**
 * OneSignal Notification Service Extension.
 * Ensures push notifications display as heads-up (popup) notifications
 * on Android 8.0+ by setting high priority and creating a high-importance channel.
 */
public class OneSignalNotificationHandler implements INotificationServiceExtension {

    private static final String CHANNEL_ID = "streamflux_push";
    private static final String CHANNEL_NAME = "Push Notifications";

    @Override
    public void onNotificationReceived(INotificationReceivedEvent event) {
        event.getNotification().setExtender(builder -> {
            builder.setChannelId(CHANNEL_ID);
            builder.setPriority(NotificationCompat.PRIORITY_HIGH);
            builder.setDefaults(NotificationCompat.DEFAULT_ALL);
            return builder;
        });
    }

    /**
     * Call this from MyApplication.onCreate() to create the notification channel early.
     */
    public static void createNotificationChannel(Context context) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationManager manager = (NotificationManager) context.getSystemService(Context.NOTIFICATION_SERVICE);
            if (manager != null) {
                NotificationChannel channel = new NotificationChannel(
                        CHANNEL_ID,
                        CHANNEL_NAME,
                        NotificationManager.IMPORTANCE_HIGH
                );
                channel.setDescription("StreamFlux push notifications");
                channel.enableLights(true);
                channel.enableVibration(true);
                channel.setShowBadge(true);
                channel.setLockscreenVisibility(android.app.Notification.VISIBILITY_PUBLIC);
                manager.createNotificationChannel(channel);
            }
        }
    }
}
