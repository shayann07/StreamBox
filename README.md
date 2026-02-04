# StreamFlux 📺

> A professional-grade IPTV streaming application for Android with a full-featured PHP admin panel for content and user management.

[![Android](https://img.shields.io/badge/Platform-Android-green.svg)](https://android.com)
[![Min SDK](https://img.shields.io/badge/Min%20SDK-24-blue.svg)](https://developer.android.com)
[![Java](https://img.shields.io/badge/Language-Java-orange.svg)](https://java.com)
[![CodeIgniter](https://img.shields.io/badge/Backend-CodeIgniter%204-red.svg)](https://codeigniter.com)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Screenshots](#screenshots)
- [Architecture](#architecture)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Android App Setup](#android-app-setup)
- [Admin Panel Setup](#admin-panel-setup)
- [Build & Release](#build--release)
- [Configuration](#configuration)
- [Key Components](#key-components)
- [API Integration](#api-integration)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

---

## Overview

StreamFlux is a complete IPTV ecosystem consisting of:

1. **Android Application** - A feature-rich streaming client supporting live TV, movies, series, and radio with multiple UI themes
2. **Admin Panel** - A CodeIgniter 4 web application for managing users, devices, content, and analytics
3. **Database Schema** - MySQL database structure for the backend

The application supports multiple IPTV protocols including **Xtream Codes API**, **M3U/M3U8 playlists**, and custom **1Stream** integration.

---

## Features

### 📺 Streaming Capabilities

| Feature | Description |
|---------|-------------|
| **Live TV** | Stream live channels with full EPG (Electronic Program Guide) support |
| **Video on Demand** | Browse and watch movies with detailed info (cast, plot, ratings) |
| **TV Series** | Full series support with seasons and episodes |
| **Radio** | Audio-only streaming for radio channels |
| **Catch-Up TV** | Time-shifted viewing - watch previously aired content |
| **Multi-Screen** | Watch up to 4 streams simultaneously in split-screen mode |

### 🎮 Player Features

| Feature | Description |
|---------|-------------|
| **ExoPlayer Integration** | Hardware-accelerated playback via Media3 ExoPlayer |
| **Multiple Formats** | Support for TS, M3U8 (HLS), and direct streams |
| **Gesture Controls** | Swipe for brightness/volume, double-tap to seek |
| **Track Selection** | Audio track and subtitle selection |
| **Picture-in-Picture** | Continue watching while using other apps |
| **External Players** | MX Player, VLC, and other external player support |

### 🔐 Authentication Methods

| Method | Description |
|--------|-------------|
| **Xtream Codes** | Standard Xtream Codes API login (server/username/password) |
| **M3U Playlist** | Direct M3U/M3U8 playlist URL support |
| **Device Activation** | MAC-based device activation via admin panel |
| **Token Code** | One-time token codes for quick activation |
| **1Stream** | Custom 1Stream portal integration |

### 🎨 UI Themes

The app includes **9 unique themes** for different aesthetics:

1. **Glossy** - Modern glossy dark theme
2. **One UI** - Samsung-inspired clean interface
3. **Black Panther** - Sleek all-black theme
4. **Movie** - Cinema-inspired design
5. **Sports** - Dynamic sports-focused layout
6. **VUI** - Voice UI inspired design
7. **Christmas** - Festive holiday theme
8. **Halloween** - Spooky dark theme
9. **Ramadan** - Islamic calendar themed

### ⬇️ Offline Features

- **Download Manager** - Download movies and episodes for offline viewing
- **Encrypted Storage** - Downloaded content is encrypted for protection
- **Resume Support** - Continue downloads after interruption
- **Storage Management** - Choose internal or external storage

### 🔒 Security & Privacy

- **OpenVPN Integration** - Built-in VPN client with profile management
- **Parental Controls** - PIN protection for sensitive content
- **Secure Connections** - HTTPS/TLS support for all API calls
- **Device Blocklist** - Admin can block specific devices

---

## Architecture

### Android App Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      Presentation Layer                       │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │  Activities │  │  Adapters   │  │   Dialogs   │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                       Business Layer                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │  Executors  │  │   Helpers   │  │   Callback  │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                        Data Layer                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │    Room DB  │  │ SharedPrefs │  │  Network    │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

### Tech Stack

| Component | Technology |
|-----------|------------|
| **Language** | Java 17 |
| **Min SDK** | 24 (Android 7.0 Nougat) |
| **Target SDK** | 34 (Android 14) |
| **Video Player** | ExoPlayer (Media3) |
| **Networking** | OkHttp 4.x, Volley |
| **Image Loading** | Picasso (with custom optimizations) |
| **Local Database** | Room Persistence Library |
| **Preferences** | SharedPreferences (via SPHelper) |
| **Background Work** | ExecutorService |
| **Ads** | AdMob, Unity Ads, Facebook Audience Network |

---

## Project Structure

```
StreamFlux/
│
├── app/                                    # Main Android application module
│   ├── src/main/
│   │   ├── java/nemosofts/streambox/
│   │   │   ├── activity/                   # All 68 Activity classes
│   │   │   │   ├── ui/                     # Theme-specific activities
│   │   │   │   ├── ExoPlayerActivity.java  # VOD player
│   │   │   │   ├── ExoPlayerLiveActivity.java # Live TV player
│   │   │   │   ├── MultipleScreenActivity.java # Multi-screen player
│   │   │   │   └── ...
│   │   │   ├── adapter/                    # RecyclerView adapters (38 adapters)
│   │   │   ├── callback/                   # API response callbacks
│   │   │   ├── dialog/                     # Custom dialog implementations
│   │   │   ├── executor/                   # Background task executors (25 executors)
│   │   │   ├── interfaces/                 # Interface definitions (19 interfaces)
│   │   │   ├── item/                       # Data model classes (31 models)
│   │   │   ├── parser/                     # M3U and EPG parsers
│   │   │   └── utils/                      # Utility classes
│   │   │       ├── helper/                 # Database & preference helpers
│   │   │       ├── player/                 # Custom player components
│   │   │       └── ...
│   │   └── res/                            # Android resources
│   │       ├── layout/                     # XML layouts
│   │       ├── drawable/                   # Icons and shapes
│   │       ├── values/                     # Strings, colors, dimensions
│   │       └── ...
│   ├── libs/                               # AAR libraries
│   └── build.gradle                        # App-level Gradle config
│
├── nemosofts-material/                     # Custom Material Design library
│   └── src/main/java/androidx/nemosofts/  # Material components
│
├── nemosofts-library-mod/                  # Utility library
│   └── src/main/java/                      # Networking, helpers
│
├── adminpanelcode/                         # CodeIgniter 4 Admin Panel
│   ├── app/
│   │   ├── Controllers/                    # 21 Controllers
│   │   │   ├── AuthController.php          # Authentication
│   │   │   ├── DeviceController.php        # Device management
│   │   │   ├── ExtreamController.php       # Xtream server management
│   │   │   ├── NotificationController.php  # Push notifications
│   │   │   └── ...
│   │   ├── Models/                         # 19 Database models
│   │   ├── Views/                          # 56 View templates
│   │   └── Libraries/                      # Custom libraries
│   ├── public/                             # Web root
│   │   ├── sbox_api.php                    # API endpoint
│   │   └── assets/                         # CSS, JS, images
│   └── .env                                # Environment configuration
│
├── db/                                     # Database
│   └── mubashir_mubashir.sql              # MySQL schema
│
├── documentation/                          # HTML documentation
│
├── Key/                                    # Signing
│   └── streamdo.jks                        # Release keystore
│
├── build.gradle                            # Project-level Gradle
├── settings.gradle                         # Module settings
└── .gitignore                              # Git ignore rules
```

---

## Prerequisites

### For Android Development

| Requirement | Version |
|-------------|---------|
| Android Studio | Hedgehog (2023.1.1) or newer |
| JDK | 17 or higher |
| Gradle | 8.2+ (bundled with Android Studio) |
| Android SDK | API 24-34 |

### For Admin Panel

| Requirement | Version |
|-------------|---------|
| PHP | 8.1 or higher |
| MySQL | 5.7+ or MariaDB 10.3+ |
| Apache/Nginx | With mod_rewrite enabled |
| Composer | 2.x (optional, for dependencies) |

---

## Android App Setup

### 1. Clone the Repository

```bash
git clone https://github.com/shayann07/StreamBox.git
cd StreamBox
```

### 2. Open in Android Studio

1. Launch Android Studio
2. Select **File → Open**
3. Navigate to the cloned directory and select it
4. Wait for Gradle sync to complete

### 3. Configure Firebase (Optional)

If you want push notifications:

1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com)
2. Add an Android app with package name `nemosofts.streambox`
3. Download `google-services.json`
4. Replace `app/google-services.json` with your file

### 4. Configure API Endpoint

Edit `app/src/main/java/nemosofts/streambox/callback/Callback.java`:

```java
public class Callback {
    // Set your admin panel API URL
    public static String API_URL = "https://your-domain.com/sbox_api.php";
    
    // Other configuration...
}
```

### 5. Build and Run

1. Connect an Android device or start an emulator
2. Click **Run → Run 'app'** or press `Shift+F10`
3. Select your target device

---

## Admin Panel Setup

### 1. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE streamflux CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p streamflux < db/mubashir_mubashir.sql
```

### 2. Configure Environment

Edit `adminpanelcode/.env`:

```ini
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = streamflux
database.default.username = your_db_user
database.default.password = your_db_password
database.default.DBDriver = MySQLi

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://your-domain.com/'
```

### 3. Web Server Configuration

#### Apache (.htaccess is included)

Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 4. Set Permissions

```bash
chmod -R 755 adminpanelcode/
chmod -R 777 adminpanelcode/writable/
```

### 5. Access Admin Panel

Navigate to `https://your-domain.com/` and log in with default credentials:
- **Username**: `admin`
- **Password**: `admin123`

> ⚠️ **Change the default password immediately after first login!**

---

## Build & Release

### Debug Build

```bash
./gradlew assembleDebug
```

Output: `app/build/outputs/apk/debug/app-debug.apk`

### Release Build

1. **Configure signing** in `app/build.gradle`:

```gradle
android {
    signingConfigs {
        release {
            storeFile file('../Key/streamdo.jks')
            storePassword 'your_store_password'
            keyAlias 'your_key_alias'
            keyPassword 'your_key_password'
        }
    }
    
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

2. **Build release APK**:

```bash
./gradlew assembleRelease
```

Output: `app/build/outputs/apk/release/app-release.apk`

### Generate Signed Bundle (for Play Store)

```bash
./gradlew bundleRelease
```

Output: `app/build/outputs/bundle/release/app-release.aab`

---

## Configuration

### App Configuration (`Callback.java`)

```java
public class Callback {
    // API Configuration
    public static String API_URL = "https://your-api.com/sbox_api.php";
    
    // Feature Flags
    public static final boolean IS_VPN_ENABLED = true;
    public static final boolean IS_DOWNLOAD_ENABLED = true;
    public static final boolean IS_MULTI_SCREEN_ENABLED = true;
    
    // Ad Configuration
    public static final String ADMOB_BANNER_ID = "ca-app-pub-xxx";
    public static final String ADMOB_INTERSTITIAL_ID = "ca-app-pub-xxx";
    
    // Login Types
    public static final String TAG_LOGIN_ONE_UI = "streaming";
    public static final String TAG_LOGIN_PLAYLIST = "playlist";
    public static final String TAG_LOGIN_SINGLE_STREAM = "single";
}
```

### Player Configuration (`SPHelper.java`)

```java
// Default player settings (can be changed by user in Settings)
spHelper.setPlayerBuffer(2000);        // Buffer size in ms
spHelper.setLiveFormat(0);             // 0 = m3u8, 1 = ts
spHelper.setMovieFormat(0);            // 0 = m3u8, 1 = ts
spHelper.setHardwareDecoder(true);     // Hardware acceleration
```

---

## Key Components

### Activities

| Activity | Purpose |
|----------|---------|
| `LauncherActivity` | Splash screen and initialization |
| `SignInActivity` | Xtream Codes login |
| `PlaylistActivity` | M3U playlist management |
| `LiveTvActivity` | Live TV channel browser |
| `MovieActivity` | Movie catalog |
| `SeriesActivity` | TV series catalog |
| `ExoPlayerLiveActivity` | Live stream playback |
| `ExoPlayerActivity` | VOD playback |
| `MultipleScreenActivity` | Multi-screen viewing |
| `SettingActivity` | App settings |

### Executors (Background Tasks)

| Executor | Purpose |
|----------|---------|
| `LoadLogin` | Authenticate with Xtream API |
| `GetCategory` | Fetch category lists |
| `GetLive` | Fetch live channels |
| `GetMovies` | Fetch movies |
| `GetSeries` | Fetch TV series |
| `LoadEpg` | Parse EPG data |
| `GetChannelPlaylist` | Parse M3U playlists |

### Database Helpers

| Helper | Purpose |
|--------|---------|
| `DBHelper` | SQLite database operations |
| `SPHelper` | SharedPreferences wrapper |
| `JSHelper` | JSON parsing utilities |

---

## API Integration

### Xtream Codes API

The app integrates with standard Xtream Codes API:

```
GET /player_api.php?username={user}&password={pass}
GET /player_api.php?username={user}&password={pass}&action=get_live_categories
GET /player_api.php?username={user}&password={pass}&action=get_live_streams
GET /player_api.php?username={user}&password={pass}&action=get_vod_categories
GET /player_api.php?username={user}&password={pass}&action=get_vod_streams
GET /player_api.php?username={user}&password={pass}&action=get_series_categories
GET /player_api.php?username={user}&password={pass}&action=get_series
```

### Stream URLs

```
Live:  {server}/live/{user}/{pass}/{stream_id}.ts
VOD:   {server}/movie/{user}/{pass}/{stream_id}.mkv
```

### Admin Panel API

The admin panel exposes a REST API at `/sbox_api.php`:

| Endpoint | Method | Description |
|----------|--------|-------------|
| `?method=app_details` | GET | Get app configuration |
| `?method=login` | POST | User authentication |
| `?method=report` | POST | Submit error reports |

---

## Customization

### Changing App Branding

1. **App Name**: Edit `app/src/main/res/values/strings.xml`
   ```xml
   <string name="app_name">Your App Name</string>
   ```

2. **Package Name**: Refactor package in Android Studio
   - Right-click `nemosofts.streambox` → Refactor → Rename

3. **App Icon**: Replace files in `app/src/main/res/mipmap-*`

4. **Splash Screen**: Modify `activity_launcher.xml` and `LauncherActivity.java`

### Adding New Theme

1. Create new Activity extending base theme activity
2. Add corresponding layout XML
3. Register in `AndroidManifest.xml`
4. Add to theme selection in `SettingUIActivity`

### Custom Player UI

Modify `custom_controls_live.xml` or create new controller layouts following ExoPlayer's `StyledPlayerView` customization guide.

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| **404 errors on streams** | Check stream URL construction in `getChannelUrl()`, ensure `.ts` or `.m3u8` extension is included |
| **Black screen in player** | Enable/disable hardware decoding in settings |
| **Images not loading** | Check network connectivity and Picasso cache |
| **Login fails** | Verify API endpoint URL and server connectivity |
| **Multi-screen not working** | Ensure OkHttpDataSource is configured with proper headers |

### Debug Logging

Enable verbose logging in `ApplicationUtil.java`:

```java
public static void log(String tag, String message) {
    if (BuildConfig.DEBUG) {
        Log.d(tag, message);
    }
}
```

### ProGuard Issues

If release build crashes, check `proguard-rules.pro` for missing keep rules.

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This is a private repository. All rights reserved.

---

## Contact

**shayann07** - [GitHub Profile](https://github.com/shayann07)

---

<p align="center">
  <b>Built with ❤️ for the streaming community</b>
</p>
