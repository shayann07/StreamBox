# StreamBox

Multi-module Android **IPTV streaming client** + companion **CodeIgniter 4 PHP admin panel** + **MySQL schema**, built on top of a paid Envato/CodeCanyon "nemosofts" template. The `applicationId` is `com.mubashir.streamflux`, the original product name is "StreamFlux", and the latest commit (`72ecdc4`) is `Bug Fixes/ Rebranding` to "StreamBox".

## 🚨 Read this before you do anything else

The previous README sold the product without flagging a single issue with what is actually committed. The audit found six things that need to be addressed before this repo is run, redistributed, or shared further.

### 1. The Android release signing keystore is committed

`Key/streamdo.jks` is the production release keystore. Anyone with this file plus the keystore/key passwords can sign and publish app updates as the original developer. The repo's own `.gitignore:146-149` literally documents this risk:

```gitignore
# Signing keys (RECOMMENDED to exclude for public repos!)
# Key/
# *.jks
```

…and then leaves both rules commented out. Recovery:

```bash
# 1. Generate a new release keystore locally and keep it OFF git.
# 2. Stop tracking the old one and harden .gitignore:
git rm --cached Key/streamdo.jks
# uncomment Key/ and *.jks in .gitignore:146-149
git commit -m "Stop tracking signing keystore"
# 3. Purge from history:
git filter-repo --path Key/streamdo.jks --invert-paths
# 4. If a Play Store upload key was derived from this keystore, contact Play
#    Console support to rotate the upload key.
```

### 2. Paid CodeCanyon template with licence verification deliberately disabled

The `nemosofts-material/` and `nemosofts-library-mod/` modules are derived from a paid Envato/CodeCanyon "nemosofts" Android template. The `app/` source contains explicit bypass code:

| File | What it does |
| --- | --- |
| `LauncherActivity.java:29-30` | `// import androidx.nemosofts.LauncherListener; // Bypassed - license verification disabled` |
| `BaseActivity.java` | Intercepts `startActivity(...)` and blocks any intent whose host is `nemosofts.com` |
| `MyApplication.java` | Registers a `DialogBlockerCallback` to suppress the verification dialog on every Activity |
| `LoadAbout.java` | `nemosofts.setVerificationCode(...)` call commented out |
| `db/mubashir_mubashir.sql` | `tbl_settings.envato_api_key = 'bypass_057590fd7c33e6c74d5b80715797e746'` |

Redistributing a paid Envato item without a legitimate Regular/Extended licence is a copyright/EULA violation. Either purchase a real licence and re-enable verification, rewrite the UI/theme system without the paid template, or take this repo private.

### 3. `app/google-services.json` is committed

Firebase project `streamflux-5f126`, Android API key `REDACTED_API_KEY`, package `com.mubashir.streamflux`. Android API keys aren't strictly secret (Google restricts them by package + signing certificate), but this should still be replaced with **your own** Firebase project before any deployment, and API-key restrictions + Firebase App Check should be applied. `.gitignore:32` has `# google-services.json` commented out — fix that too.

### 4. `db/mubashir_mubashir.sql` is a real production-ish dump with live credentials

This 27 KB MySQL dump (generated 2026-02-01 from `192.168.0.100:3306`) is **not** seed data — it contains:

- `tbl_activation_code` row with **real third-party IPTV provider credentials**: `username = REDACTED_USER`, `password = REDACTED_PASS`, `server = http://zavqeno.com`, activation code `5949638966`. **Notify the provider and rotate the password — these have been public.**
- `tbl_admin` row with bcrypt hash for the `admin` user (the previous README documented the default password as `admin123` — change it).
- `tbl_settings` containing the **OneSignal REST API key `REDACTED_ONESIGNAL_KEY`** (a real secret — anyone with it can broadcast push notifications to every device that has installed this app). **Rotate immediately.**

Replace the file with a schema-only dump (`mysqldump --no-data`) and history-purge.

### 5. Hardcoded encryption key, IV, and plaintext-HTTP backend in `gradle.properties:36-48`

```
BASE_URL="http://mubashir.co/streambox/"
API_NAME="NEMOSOFTS_APP"
ENC_KEY="onlinenstencrypt"
IV="nstencryptiv1234"
```

`Callback.java:20` uses `BuildConfig.BASE_URL + "api"` for every server call, and `AndroidManifest.xml:74` sets `android:usesCleartextTraffic="true"` to permit the HTTP. Move these into a `secrets.properties` ignored by git or into CI environment variables, switch the backend to HTTPS, drop `usesCleartextTraffic`, and rotate `ENC_KEY` + `IV`.

### 6. Default admin password documented

The previous README documented the admin panel default login as `admin` / `admin123`. If the deployed panel still uses this, change it.

## What the codebase actually contains

- **`app/`** — main Android module, ~38k LOC Java. Entry: `LauncherActivity` → splash → calls `BuildConfig.BASE_URL + "api"` to fetch settings, ad units, theme config → routes to one of `SignInActivity`, `SignInDeviceActivity`, `SignInCodeActivity`, `TrialAccountActivity`. Five auth flows: **Xtream Codes** (server / username / password), **M3U/M3U8 playlist URL**, **device activation** (MAC + admin-generated code), **token code**, **1Stream portal**. Content categories: Live TV (with EPG + catch-up), Movies, Series, Radio, plus offline encrypted downloads. Player is **ExoPlayer Media3 1.3.0** with TS / HLS / direct streams, gesture controls, audio/subtitle tracks, PiP, and external player handoff (MX, VLC). 9 selectable themes (Glossy, One UI, Black Panther, Movie, Sports, VUI, Christmas, Halloween, Ramadan).
- **`adminpanelcode/`** — ~17.8k LOC PHP. CodeIgniter 4 admin panel — 21+ controllers, 19 models, 56 views, REST endpoint at `public/sbox_api.php`. CodeIgniter itself is MIT-licensed; the bespoke admin code is part of the paid template.
- **`nemosofts-material/`** — ~16.7k LOC custom Material library lifted from the Nemosofts template (custom dialogs, multi-view player layout, theme switcher).
- **`nemosofts-library-mod/`** — empty placeholder module.
- **`db/mubashir_mubashir.sql`** — see security warning above.
- **`Key/streamdo.jks`** — see security warning above.
- **`documentation/`** — auto-generated HTML docs from "Advanced Document Creator" with `nemosofts.com` watermark; bloat — replace with markdown.
- **Helper scripts at repo root** (`build_fix.ps1`, `fix_dialog_leaks.py`, `list_aar_classes.ps1`, `verify_jar.ps1`) — all Windows-developer-specific; `fix_dialog_leaks.py:73` and `list_aar_classes.ps1` hardcode the developer's `d:/Work/...` path. Drop them.

## Tech stack

- **Languages:** Java 17 (Android), PHP (CodeIgniter 4 admin), MySQL.
- **Android:** AGP 8.2+, `compileSdk 36`, `minSdk 23`, `targetSdk 36`, `viewBinding` enabled, no Kotlin.
- **Player:** `androidx.media3:media3-exoplayer 1.3.0` (modern, not the legacy `com.google.android.exoplayer2`).
- **Networking:** Volley + OkHttp 4.12.0; HTTP allowed via `usesCleartextTraffic="true"`.
- **Image loading:** Picasso 2.8.
- **Persistence:** SharedPreferences via `SPHelper` (no Room).
- **Push:** OneSignal 5.1.6.
- **Ads:** Google Mobile Ads 22.6.0 (banner / interstitial / reward — unit IDs delivered dynamically by the admin panel).
- **Cast:** Google Cast Framework 21.4.0.
- **Firebase BOM 32.7.0:** Analytics + In-App Messaging only (no Auth, no Firestore, no FCM).
- **YouTube playback:** `youtube-android-player-api` 12.1.0.

## Permissions of note (`app/src/main/AndroidManifest.xml`)

Standard streaming + media + foreground-service + Cast + AdMob + OneSignal permissions. Notable:

- `android:usesCleartextTraffic="true"` (line 74) — allows HTTP. Drop after backend migrates to HTTPS.
- `RECEIVE_BOOT_COMPLETED` (line 41) — used to restore the app's notification/cast state on boot.

No `READ_PHONE_STATE` (no IMEI grab), no AccessibilityService, no DeviceAdmin, no `SYSTEM_ALERT_WINDOW`.

## Setup / run

> Do **not** ship anything built from this tree as-is. Resolve every item in the security section first.

### Android

```bash
# 1. Generate your own keystore and replace Key/streamdo.jks.
# 2. Replace app/google-services.json with your own Firebase project.
# 3. Replace BASE_URL/ENC_KEY/IV in gradle.properties with your own values
#    (and move them into a secrets.properties that's in .gitignore).
# 4. Switch BASE_URL to https:// and drop android:usesCleartextTraffic.
./gradlew :app:assembleDebug
```

Open in Android Studio Hedgehog or newer for AGP 8.2.

### Admin panel

```bash
# 1. Sanitize db/mubashir_mubashir.sql (schema only) before importing.
# 2. Drop adminpanelcode/ into a PHP 8 + MySQL host.
# 3. Configure adminpanelcode/app/Config/Database.php with your DB creds (NOT in git).
# 4. Force a password reset for the seed `admin` account.
# 5. Point gradle.properties.BASE_URL at the deployed admin URL.
```

## Status

Working tree clean on `main`. 4 commits: `3b00da0` Initial commit - StreamFlux IPTV app with admin panel, `a4c41b1` Add README.md, `b331046` Add comprehensive README documentation, `72ecdc4` Bug Fixes/ Rebranding. No GitPulse pollution.

Remote: `https://github.com/shayann07/StreamBox.git`.

## License

The previous README declared "All rights reserved" while redistributing a paid Nemosofts CodeCanyon template with its licence-verification bypassed — those two claims are incoherent. Resolve the Envato licence situation before adding any open-source `LICENSE` file. Until then, treat as **all rights reserved AND third-party-licence-disputed**.
