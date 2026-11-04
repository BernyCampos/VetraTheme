# Contributing to Vetra Theme

Thank you for improving Vetra Theme. Every change must preserve Pelican's security, stability, native permissions, and upgrade path.

## Rules

- Do not modify, copy, or patch Pelican core files.
- Use only the documented plugin API and Filament hooks exposed by the supported Pelican release.
- Keep the theme additive; never simulate console, file, metric, or power operations.
- Do not add direct SQL, migrations, telemetry, obfuscation, process execution, or hidden remote services.
- Keep English as the source language. Add user-facing strings to `lang/en/ui.php`, then add matching translations such as `lang/es/ui.php`.
- Add tests for fixes and new behavior.
- Update documentation and the changelog in the same pull request.

## Local validation

```bash
npm ci
npm run build
npm test
php tests/appearance.php
```

Validate PHP syntax for every file under `src`, `config`, `lang`, and `tests`. Browser scripts under `tests/browser*.mjs` require a disposable QA panel, never production.

The repository must not restore `tests/integration.php` or a workflow step that clones Pelican and executes it. That file was removed after a Pelican Hub submission block. Compatibility work should be documented and tested in an isolated maintainer environment without shipping a blocked test harness.

## Style

- PHP uses `strict_types=1`, explicit types, and small responsibilities.
- JavaScript, PHP comments, identifiers, tests, metadata, and documentation are written in English.
- CSS stays scoped under Vetra selectors or documented Filament selectors.
- English and Spanish translation arrays must have identical key structures.
- Do not commit generated test sessions, credentials, user backgrounds, or `node_modules`.

## Pull requests

Include:

- The problem and design decision.
- Tests executed and their results.
- Before/after screenshots for visual changes.
- Compatibility and security impact.
- Confirmation that Pelican core was not modified.

Report security issues privately as described in [SECURITY.md](SECURITY.md).
