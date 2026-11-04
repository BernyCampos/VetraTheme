# Architecture

Vetra Theme is an additive Pelican theme plugin. It integrates through Pelican's plugin API and exposed Filament hooks; it does not replace core views, controllers, resources, assets, or operational features.

## Components

| Path | Responsibility |
| --- | --- |
| `plugin.json` | Theme identity, supported panels, versions, and update URL |
| `src/VetraThemePlugin.php` | Plugin registration, hooks, colors, navigation, and settings persistence |
| `src/Appearance.php` | Defaults, normalization, environment mapping, and CSS variables |
| `src/SettingsForm.php` | Filament settings schema and validation |
| `src/Notice.php` | Markdown rendering and HTML sanitization |
| `src/BackgroundImage.php` | Content, size, dimension, and format validation |
| `src/BackgroundStorage.php` | Persistent storage and optional plugin-directory mirror |
| `src/SvgSanitizer.php` | Strict SVG allowlist transformation |
| `src/Providers/BackgroundRouteServiceProvider.php` | Constrained GET/HEAD route for stored backgrounds |
| `resources/views` | Small hook fragments; no copied Pelican screens |
| `resources/css/vetra.css` | Scoped source styles compiled to `dist/vetra.css` |
| `lang/en`, `lang/es` | English source UI and Spanish locale translation |

## Pelican and Filament integration

The plugin registers with API version 1 and declares category `theme`. It uses these rendering hooks:

- `STYLES_AFTER` inserts normalized variables and compiled CSS after native styles.
- `BODY_START` adds one fixed decorative ambient layer without pointer events.
- `CONTENT_START` renders the global notice only when content exists.
- `PAGE_HEADER_HEADING_AFTER` adds real server context on the console page.

It does not call `theme()` or `viteTheme()`. Requests read the packaged `dist/vetra.css`, so rendering does not depend on Pelican's Vite manifest.

Navigation preferences use Filament configuration exposed by Pelican. The `app` panel keeps its native structure when a requested navigation component is unavailable.

## Presentation boundaries

The terminal and editor keep solid readable surfaces. Vetra Theme does not recolor ANSI output, Monaco content, or canvas data. On wide screens, CSS may place charts that already exist on the right; it never invents metrics.

The six console resources retain individual cards. Their grouping container is transparent and does not create a seventh frame. Native power buttons keep their Livewire actions and SVG icons; only spacing, color, and shape change.

## Settings flow

1. Pelican loads values through the plugin configuration/environment mechanism.
2. `Appearance::normalize()` allowlists keys, formats, ranges, and colors.
3. Filament renders translated labels from `vetra-theme::ui`.
4. `saveSettings()` authorizes the plugin update again.
5. A background upload, when present, is validated and stored.
6. Normalized values are written through Pelican's supported environment writer.
7. CSS variables are generated only from normalized values.

The notice is base64-encoded in the environment mapping so multiline Markdown remains a single valid value. Base64 is transport encoding, not encryption.

## Background storage flow

1. Filament supplies a temporary `UploadedFile` without automatic permanent storage.
2. `BackgroundImage` validates size, real content, dimensions, and format.
3. Raster bytes are preserved; SVG is parsed and rebuilt from a graphical allowlist.
4. A SHA-1-derived random-looking filename plus validated extension is generated.
5. Bytes are written under `storage/app/vetra-theme/background`.
6. An optional best-effort mirror is written under `plugins/vetra-theme/background` when already writable.
7. Configuration stores `/vetra-theme/background/NAME`.
8. A read-only route returns the persistent copy with an explicit MIME type, `nosniff`, CSP, and immutable caching.

If settings persistence fails, a newly stored upload is rolled back. Removing a selected background clears the association but intentionally does not delete older files.

## Safe degradation

- Without `backdrop-filter`, opaque surface colors remain usable.
- Without a background, ambient gradients remain active.
- Without charts, no empty chart simulation appears.
- If Markdown rendering fails, escaped plain text is returned.
- If the plugin mirror is not writable, persistent Pelican storage remains authoritative.

## Non-goals

Vetra Theme does not implement authentication, permissions, metrics, WebSockets, power actions, file operations, user preferences, a terminal, or an editor. Those capabilities remain owned by Pelican and Wings.
