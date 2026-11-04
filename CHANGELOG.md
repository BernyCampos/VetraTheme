# Changelog

All notable Vetra Theme changes are documented here. The project follows [Semantic Versioning](https://semver.org/) and [Keep a Changelog](https://keepachangelog.com/).

## [1.0.0] - 2026-11-04

First public release.

### Added

- Original glass theme for Pelican's `admin`, `app`, and `server` panels.
- Glass, Solid, Floating, and Light Glass styles.
- Persistent controls for palettes, opacity, blur, radius, navigation, density, and accessibility.
- Validated background upload with persistent Pelican storage and read-only delivery.
- Safe global Markdown notice with responsive topbar spacing.
- Server header, six independent resource cards, and native SVG power controls.
- Readable console, editor, table, and form surfaces.
- Optional right-side placement of native charts on wide screens.
- English source language plus Spanish panel translation.
- Deterministic packaging, GitHub Actions validation, and Pelican Hub release documentation.

### Security

- No direct SQL, migrations, telemetry, analytics, obfuscated code, process execution, or hidden remote calls.
- Administrative authorization is checked before settings are persisted.
- Markdown, CSS values, paths, raster content, and SVG content are validated.
- Uploaded backgrounds use random names and a constrained read-only route.
- Filament's foundational theme remains intact; Vetra Theme adds scoped CSS only.

### Publication corrections

- Standardized all public branding as **Vetra Theme**.
- Standardized code, metadata, tests, documentation, and Marketplace copy in English.
- Kept Spanish only as a runtime translation selected by the panel locale.
- Removed the blocked `tests/integration.php` and its CI execution after the first Pelican Hub review.
