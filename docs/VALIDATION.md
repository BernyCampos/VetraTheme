# Vetra Theme 1.0.0 validation

## Automated environment

- Node.js 22 for Vite and package tests.
- PHP 8.4 for syntax and appearance/security checks.
- GitHub Actions on Ubuntu.
- Chrome/Chromium for optional browser regression scripts.

Automated tests must never run against a production panel.

## Checks included in CI

| Area | Coverage |
| --- | --- |
| Manifest | Theme category, API 1, panels, namespace, class, identity, and version alignment |
| Integration boundaries | No Filament theme replacement, no remote CSS import, settings authorization |
| Appearance | Defaults, normalization, ranges, safe paths, CSS generation, UTF-8 notice limit |
| Localization | Matching English and Spanish top-level translation keys |
| Assets | Compiled ambient, terminal, resource-card, power-group, and accessibility rules |
| Documentation | Required guides and five screenshots present |
| Packaging | Single root, no `meta`, no development dependencies or secrets, deterministic metadata |
| PHP | Syntax check for `src`, `config`, `lang`, and `tests` |

The blocked `tests/integration.php` is intentionally absent. The first Pelican Hub submission flagged that file, so the corrected GitHub workflow no longer clones Pelican or executes the removed harness.

## Browser scripts available for staging

The scripts under `tests/browser*.mjs` verify, on a disposable English-locale QA panel:

- Settings save, reload, and reset.
- PNG, GIF, WebP, and sanitized SVG uploads.
- Persistent background route, MIME, `nosniff`, and byte preservation for raster files.
- Safe Markdown output and two-list-item rendering.
- 20 px or greater notice/topbar separation.
- No horizontal overflow at 390 px.
- Separate borders on six server resource cards and no outer group frame.
- Native console visibility and light/dark mode switching.
- Palette persistence across `admin`, `app`, and `server` layouts.

They require `VETRA_URL`, `VETRA_EMAIL`, `VETRA_PASSWORD`, and optionally `VETRA_SERVER_PATH` and `VETRA_CHROME`. Generated sessions, HTML, reports, and screenshots belong in `test-results`, which is excluded from release packages.

## Security assertions

- Reject remote paths, traversal, queries, fragments, unsafe characters, and CSS injection.
- Enforce 5 MB and raster dimension limits.
- Reject DTDs, entities, scripts, events, styles, images, links, and remote references in SVG.
- Remove raw HTML, images, and unsafe schemes from Markdown.
- Roll back a newly stored file when settings persistence fails.
- No direct SQL, migrations, telemetry, process execution, permission changes, or core writes.

## Pending release verification

- Fresh install, update, disable, uninstall, and safe-mode recovery on a clean staging panel.
- Full Firefox and Safari review.
- Pelican versions other than `1.0.0-rc.2`.
- Broad compatibility matrix with plugins that replace navigation, console, tables, or widgets.
- External accessibility and security audits.
- Destructive native file and power operations under multiple subuser policies.
- Long-running WebSocket, chart, and terminal sessions.
- Final Pelican Hub review and approval.

## Release criterion

Version 1.0.0 is an installable first release for the exact declared Pelican version. Marketplace copy must keep the limitations above visible and must not state that Pelican Hub approved the plugin until the exact submitted revision is accepted.
