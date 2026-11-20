# Changelog

## 1.1.1 — Pelican Panel v1.0.0-rc.4 compatibility

- Declare strict compatibility with Pelican Panel `1.0.0-rc.4` (released 19 November 2026); rc.3-only plugins become incompatible under Pelican's exact version matching.
- Verify unchanged official `HasTheme` interface, active-only theme resources, and Vite inputs in rc.4.
- Preserve Vetra's existing palette, CSS, accessibility behavior, console layout, settings persistence, notices, and validated backgrounds with no visual changes or migrations.
- Update release metadata, packaging checks, docs, and automated validations for rc.4.
- Keep v1.1.0 available for Pelican rc.3 and v1.0.1 for rc.2; full live-panel rc.4 validation remains pending.

## 1.1.0 — Native theme switcher

- Support Pelican Panel 1.0.0-rc.3 native theme switcher using the HasTheme contract.
- Load Vetra CSS, palette, ambient background, notices and console context only when Vetra is the selected theme.
- Preserve Filament's built-in layout and color-mode controls instead of mutating all panels in register().
- Stop exposing legacy global mode, navigation-layout and sidebar-width controls. Existing values remain safely stored but are no longer applied.
- Preserve all other appearance settings without changing the plugin ID, core panel files, or database.

## 1.0.1

Packaging release for Pelican Hub synchronization. No functional theme changes.

## 1.0.0

Initial release.
