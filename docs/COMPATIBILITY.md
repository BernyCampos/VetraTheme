# Compatibility

## Supported target

| Component | Target / validation |
| --- | --- |
| Pelican Panel | `1.0.0-rc.4` |
| Pelican plugin API | `1` |
| Filament | Version bundled with the target Pelican release |
| PHP | `8.4` in CI |
| Node.js | `22` for asset builds and tests |

`plugin.json` intentionally declares `panel_version: 1.0.0-rc.4` because Pelican's strict plugin compatibility check rejects an exact rc.3 constraint on rc.4. The official `HasTheme` and active-theme hook implementation did not change between these releases. Vetra v1.1.0 remains available for Pelican rc.3; v1.0.1 remains available for rc.2. The rc.4 target has automated package/contract tests, but no connected live-panel end-to-end validation yet.

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

Pelican's native theme picker activates Vetra's Vite stylesheet, colors and render hooks only when selected. The old panel-wide mode, navigation layout and sidebar width options are not used by Vetra 1.1.1 because they cannot be applied safely to an individual user's selected theme. Plugins that replace navigation, tables, headers, terminal layout, or widgets may require specific compatibility work.

## References

- [Pelican plugin documentation](https://pelican.dev/docs/panel/advanced/plugins/)
- [Pelican Panel releases](https://github.com/pelican-dev/panel/releases)
- [Pelican Hub terms](https://hub.pelican.dev/tos)
- [Filament styling documentation](https://filamentphp.com/docs/5.x/styling/overview)

Pelican's plugin system is still evolving. Vetra Theme relies on the documented manifest, API 1, settings contract, auto-discovery, translations, and render hooks; internal classes or markup may change.
