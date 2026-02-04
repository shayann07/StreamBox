# StreamFlux 📺

A feature-rich IPTV streaming application for Android with a comprehensive admin panel for content management.

## Features

### Android App
- 🎬 **Live TV** - Stream live channels with EPG support
- 🎥 **Movies & Series** - Browse and watch VOD content
- 📻 **Radio** - Listen to radio streams
- 📺 **Multi-Screen** - Watch up to 4 streams simultaneously
- ⬇️ **Downloads** - Download content for offline viewing
- 🔐 **Multiple Login Types** - Xtream Codes, M3U Playlist, Device activation
- 🎨 **Multiple Themes** - 9 unique UI themes (Glossy, Black Panther, Sports, etc.)
- 🔒 **VPN Support** - Built-in OpenVPN integration
- advancement **Catch-Up TV** - Watch previously aired content

### Admin Panel
- 👥 User & device management
- 📊 Analytics dashboard
- 🔔 Push notifications
- 🚫 Blocklist management
- 🎟️ Token-based activation
- 📢 Custom ads management

## Project Structure

```
StreamFlux/
├── app/                      # Android application
│   ├── src/main/java/        # Java source code
│   └── src/main/res/         # Resources (layouts, drawables, etc.)
├── adminpanelcode/           # CodeIgniter 4 admin panel
│   ├── app/Controllers/      # API & web controllers
│   ├── app/Models/           # Database models
│   └── public/               # Web assets
├── nemosofts-material/       # Material design library
├── nemosofts-library-mod/    # Utility library
├── db/                       # Database schema
├── documentation/            # HTML documentation
└── Key/                      # Signing keystore
```

## Tech Stack

### Android
- **Language**: Java
- **Min SDK**: 24 (Android 7.0)
- **Player**: ExoPlayer (Media3)
- **Networking**: OkHttp, Volley
- **Image Loading**: Picasso (optimized)
- **Database**: Room

### Admin Panel
- **Framework**: CodeIgniter 4
- **Language**: PHP 8.1+
- **Database**: MySQL

## Setup

### Android App
1. Open project in Android Studio
2. Sync Gradle files
3. Configure `app/google-services.json` for Firebase
4. Build and run

### Admin Panel
1. Configure database in `adminpanelcode/.env`
2. Import `db/mubashir_mubashir.sql` to MySQL
3. Deploy to PHP 8.1+ server with mod_rewrite enabled

## Building Release APK

```bash
./gradlew assembleRelease
```

Signed APK will be in `app/build/outputs/apk/release/`

## License

Private repository - All rights reserved.

---

**Made with ❤️ by shayann07**
