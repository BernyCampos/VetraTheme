# Changelog

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
