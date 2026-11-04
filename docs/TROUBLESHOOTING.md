# Troubleshooting

## Installation remains pending

- Confirm Pelican's queue worker is running.
- Review failed queue jobs and `storage/logs/laravel.log`.
- Confirm the installable ZIP has one `vetra-theme/` root.
- Do not repeatedly import while a job is active.

## The theme is installed but unchanged

- Confirm the plugin row shows category **Theme**, version `1.0.1`, and enabled status.
- Open Vetra Theme settings and submit once.
- Fully reload the browser.
- Rebuild only the caches required by your normal Pelican deployment.
- Check that `dist/vetra.css` exists inside the installed plugin.

## Settings do not persist

- Confirm the current account can update plugins.
- Wait for the **Appearance saved** notification.
- Check the panel log for environment write errors.
- If configuration is cached, rebuild it with the normal deployment procedure.

## A background upload fails

- Keep the file under 5 MB and raster dimensions at or below 8192 × 8192.
- Use JPEG, PNG, WebP, GIF, AVIF, BMP, ICO, or a simple SVG.
- Convert HEIC, TIFF, or PSD first.
- Confirm Pelican's `storage` is writable by the PHP process.
- Read the validation message and panel log.

The optional `plugins/vetra-theme/background` mirror may remain empty when the plugin directory is read-only. That is not a failure if persistent storage and `/vetra-theme/background/...` work.

## The background route returns 404

- Save settings again and verify the stored path begins with `/vetra-theme/background/`.
- Confirm the referenced file exists under `storage/app/vetra-theme/background`.
- Rebuild route cache using the deployment's normal procedure if routes were cached before installation.

## Markdown formatting disappears

This is intentional for unsafe content. The notice supports documented Markdown but removes raw HTML, images, styles, scripts, events, iframes, embeds, and unsafe URLs. See [CONFIGURATION.md](CONFIGURATION.md).

## HTTP 500 recovery

1. Save the final relevant lines of `storage/logs/laravel.log`.
2. Enable Pelican plugin safe mode in `.env`:

```env
PANEL_PLUGIN_SAFE_MODE=true
```

3. Rebuild configuration cache using your deployment's normal procedure.
4. Disable Vetra Theme from administration when the panel opens.
5. Verify the Pelican version and ZIP integrity before reinstalling.
6. Set `PANEL_PLUGIN_SAFE_MODE=false` and rebuild configuration cache after recovery.

Do not delete `storage`, the database, dependency lock files, or broad cache directories without a diagnosis and backup.

## Native actions fail

Vetra Theme does not implement file operations, metrics, WebSockets, permissions, or power actions. Temporarily disable the theme to separate a presentation problem from Pelican, Wings, networking, or permissions. If the operation also fails without the theme, review Pelican/Wings logs and documentation.

## Useful report details

- Vetra Theme and Pelican versions.
- Affected panel and URL type, without secrets.
- Browser and viewport.
- Minimal reproduction steps.
- Sanitized log lines and screenshot.
- Whether the issue remains with Vetra Theme disabled.
