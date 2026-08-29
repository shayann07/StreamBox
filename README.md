# StreamBox

StreamBox is a comprehensive multi-module Android IPTV Streaming Application and CodeIgniter 4 Web Admin Panel.

## Key Features

*   **Playback Engine:** Powered by Media3 ExoPlayer (supports HLS, DASH, RTSP, SmoothStreaming).
*   **Dynamic UI Themes:** 9 unique UI themes including Glossy, OneUI, VUI, Movie, and Ramadan.
*   **Live TV & VOD:** Live TV with EPG grid, Movies & Series catalog with subtitles and episode picker.
*   **Authentication:** Multiple modes including Xtream Codes API, Device ID activation, 1-Click Trial, and M3U playlists.
*   **Push Notifications:** OneSignal integration for real-time updates.
*   **Admin Backend:** CodeIgniter 4 PHP Admin Panel backend for seamless content and user management.

## Architecture & Modules

*   :app - Android Client
*   :nemosofts-material - Custom Material widget & theme engine
*   dminpanelcode/ - CodeIgniter 4 REST API & Web Admin

## Setup & Installation Guide

1.  **Android Studio:**
    *   Open the project in Android Studio.
    *   Setup your Firebase project using the pp/google-services.json.example template.
    *   Update gradle.properties (see gradle.properties.example) with your base URL and encryption keys.
2.  **CodeIgniter 4 Backend:**
    *   Import your database schema (create one based on .env.example).
    *   Configure the database connection in dminpanelcode/.env (using .env.example as a reference).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
