# Installation

## Before installing

- Confirm Pelican Panel is exactly `1.0.0-rc.2`.
- Back up the panel according to your deployment procedure.
- Confirm Pelican's queue worker is running.
- Download the installable `vetra-theme-1.0.1.zip`, not the source ZIP.
- Optionally verify its SHA-256 value against `SHA256SUMS`.

## Install through Pelican

1. Sign in as an administrator.
2. Open **Administration → Plugins**.
3. Choose **Import** and select `vetra-theme-1.0.1.zip`.
4. Wait for the queued import to finish; do not repeatedly import the same file.
5. Open Vetra Theme and choose **Install** if Pelican presents that action.
6. Enable the theme.
7. Open **Settings**, review the defaults, and submit once.
8. Fully reload the browser.

The plugin must appear with ID `vetra-theme`, category **Theme**, version `1.0.1`, and support for `admin`, `app`, and `server`.

Do not manually copy the package into Pelican core directories. The ZIP already contains the required `vetra-theme/` root and Pelican owns extraction and lifecycle operations.

## Verify

- Open one administration list and one dialog.
- Open the application/server list.
- Open a server console and file manager.
- Confirm six resource cards retain individual borders while their shared wrapper has no extra frame.
- Confirm power controls still invoke native actions and display SVG icons.
- Save a small test background from settings, reload, and request its `/vetra-theme/background/...` URL.
- Save a Markdown notice and confirm spacing below the topbar on desktop and mobile.

## Update

Use Pelican's normal plugin update flow and read the release notes first. The manifest points to the configured Pelican Hub update feed. Back up configuration and backgrounds before updating. Never replace files while queue jobs or PHP workers are actively loading a partial package.

## Remove

1. Restore defaults if you want environment-backed Vetra values reset.
2. Disable and uninstall Vetra Theme through Pelican.
3. Clear or rebuild only the caches required by your normal deployment.
4. Remove stored backgrounds manually only after confirming they are no longer needed.

Uninstalling does not delete user-uploaded files automatically. This is deliberate and prevents irreversible data loss.

## Recovery from an HTTP 500

Use Pelican's documented plugin safe mode from the panel root:

```env
PANEL_PLUGIN_SAFE_MODE=true
```

Regenerate configuration cache using your deployment's normal Pelican procedure, inspect `storage/logs/laravel.log`, disable the plugin, and restore `PANEL_PLUGIN_SAFE_MODE=false` after the panel is stable. Do not delete the database, `storage`, dependency locks, or broad cache directories as a first response.
