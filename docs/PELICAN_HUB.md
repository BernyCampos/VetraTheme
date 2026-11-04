# Pelican Hub listing

This file contains the English plugin description, requirements, installation instructions, and screenshot order.

## Name

Vetra Theme

## Short description

Customizable glass theme for Pelican Panel with persistent palettes, safe background uploads, Markdown notices, and readable native server interfaces.

## Full description

Vetra Theme is an original appearance plugin for Pelican Panel. It adds balanced ambient lighting, translucent navigation, configurable surfaces, and accessible light/dark palettes while preserving Pelican and Filament behavior.

Administrators can choose Glass, Solid, Floating, or Light Glass styles; configure 29 colors; adjust opacity, blur, radius, density, and navigation; upload a validated background; and publish a safe global Markdown notice. Settings use Pelican's supported plugin mechanisms and require no database migrations.

Vetra Theme does not replace the console, file manager, server metrics, permissions, or power actions. It styles the real native components already rendered by Pelican.

## Highlights

- Supports Pelican `admin`, `app`, and `server` panels.
- Four visual styles with separate light and dark palettes.
- Persistent background upload from plugin settings.
- Safe global Markdown notice without active HTML.
- Coherent server header, six resource cards, console, and file manager.
- Native SVG power buttons and accessible names.
- Reduced-motion, reduced-transparency, focus, and high-contrast support.
- English source interface with Spanish panel translation.
- No telemetry, direct SQL, migrations, obfuscation, or extra Composer packages.

## Requirements

- Pelican Panel `1.0.0-rc.2`.
- Plugin API 1.
- Queue worker active for installation and updates.
- Modern browser.

## Installation

Import `vetra-theme-1.0.1.zip` from **Administration → Plugins**, wait for the queue, install, enable, and save the appearance settings. Do not use the `-source.zip` asset as the installable package.

## Privacy and security

The plugin package has no telemetry, analytics, hidden remote calls, direct SQL, migrations, or obfuscated code. Uploads, paths, Markdown, colors, and numeric settings are validated. The only declared update endpoint is the configured Pelican Hub feed at `https://hub.pelican.dev/plugins/vetra-theme/update.json`.

## Suggested screenshot order

1. `docs/images/server-console.png` — main server experience.
2. `docs/images/file-manager.png` — file manager integration.
3. `docs/images/admin-eggs.png` — administration integration.
4. `docs/images/settings-appearance.png` — primary appearance controls.
5. `docs/images/settings-notice.png` — palettes and safe notice editor.

## Language

English is the source language. Spanish is available through Pelican's plugin translation system when the panel locale is Spanish.
