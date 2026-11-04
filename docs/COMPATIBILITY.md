# Compatibility

## Supported target

| Component | Verified version |
| --- | --- |
| Pelican Panel | `1.0.0-rc.2` |
| Pelican plugin API | `1` |
| Filament | Version bundled with the target Pelican release |
| PHP | `8.4` in CI |
| Node.js | `22` for asset builds and tests |

`plugin.json` intentionally declares `panel_version: 1.0.0-rc.2`. Other Pelican versions are not claimed until they are tested.

## Panels

| Panel | Status | Covered interface |
| --- | --- | --- |
| `admin` | Supported | Navigation, headers, tables, forms, dialogs, notifications |
| `app` | Supported | Native account/application layout and shared surfaces |
| `server` | Supported | Navigation, header, resource cards, console, files, native charts |

## Browsers and accessibility

Modern Chrome/Chromium is the automated visual-test target. The theme uses CSS variables, `color-mix()`, `:has()`, and progressively enhanced `backdrop-filter`. Current Firefox and Safari should render most styling, but release claims require manual verification.

Fallbacks preserve solid surfaces when blur is unavailable. `prefers-reduced-motion`, `prefers-reduced-transparency`, visible focus, and `forced-colors` receive explicit treatment.

## Native selectors

Vetra Theme styles documented or observed Filament/Pelican structures such as `.fi-sidebar`, `.fi-topbar`, `.fi-header`, `.fi-section`, `.fi-ta`, `.fi-modal`, `.fi-console-page`, `.fi-wi-widget`, `.fi-wi-chart`, and the native header action group. Markup changes in a future Pelican/Filament release may require a new Vetra Theme version.

Plugins that replace navigation, tables, headers, terminal layout, or widgets may require specific compatibility work.

## References

- [Pelican plugin documentation](https://pelican.dev/docs/panel/advanced/plugins/)
- [Pelican Panel releases](https://github.com/pelican-dev/panel/releases)
- [Pelican Hub terms](https://hub.pelican.dev/tos)
- [Filament styling documentation](https://filamentphp.com/docs/5.x/styling/overview)

Pelican's plugin system is still evolving. Vetra Theme relies on the documented manifest, API 1, settings contract, auto-discovery, translations, and render hooks; internal classes or markup may change.
