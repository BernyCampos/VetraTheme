# Security and privacy

Vetra Theme changes presentation and visual configuration. It does not add authentication, authorization models, server permissions, direct SQL, migrations, telemetry, or Wings operations.

## Authorization

The settings action lives in Pelican's plugin administration. Before persistence, `saveSettings()` authorizes the `update` policy against the plugin model again. Ordinary users cannot change global appearance settings.

## Settings and CSS

- Only documented keys are accepted.
- Colors must be six-digit hexadecimal values.
- Numbers are clamped to documented ranges.
- Boolean and enum values are normalized.
- Background paths reject remote URLs, traversal, query strings, fragments, unsafe characters, and direct SVG paths.
- Generated CSS contains normalized values only.
- Compiled CSS is static, additive, and contains no remote imports.

## Markdown notice

The renderer processes Markdown, then `HtmlSanitizer` keeps only documented formatting elements and safe link attributes. Images, styles, iframes, scripts, event attributes, embeds, and unsafe schemes such as `javascript:` and `data:` are rejected. A rendering failure returns escaped text instead of breaking the panel.

## Images

Raster files are verified from their actual content with `getimagesizefromstring()`, not only by extension. Size and dimension limits apply. SVG files are parsed without external entities and rebuilt from a strict allowlist; scripts, styles, images, animations, events, links, DTDs, entities, and remote references are removed or rejected.

## Storage and public route

- Persistent storage is under `storage/app/vetra-theme/background`.
- Filenames contain 40 hexadecimal characters and a validated extension.
- The optional plugin-directory copy is best effort and never triggers `chmod` or `chown`.
- The public route accepts GET/HEAD only and provides no listing or write action.
- Responses set an explicit MIME type, `X-Content-Type-Options: nosniff`, a restrictive CSP, and immutable caching.

Backgrounds are public theme assets. Do not upload confidential information.

## Network and dependencies

The runtime has no telemetry, analytics, image lookup, hidden remote service, or obfuscated payload. The manifest explicitly declares only the configured Pelican Hub update endpoint. Vetra Theme declares no additional Composer packages; Node dependencies are development-only and are excluded from the installable ZIP.

## Database and system operations

There are no direct SQL statements, raw queries, migrations, seeders, or plugin-owned tables. The only model access during saving locates the plugin and checks its policy. Runtime code does not execute shell commands, spawn child processes, change permissions, or run a custom installer.

## Reporting

Do not publish exploitable details in a public issue. Follow [the repository security policy](../SECURITY.md) and include sanitized versions, impact, and minimal steps. Never attach tokens, cookies, keys, `.env`, databases, or personal data.
