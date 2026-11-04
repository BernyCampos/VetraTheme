# Release process

## Prepare

1. Keep `plugin.json`, `package.json`, and `package-lock.json` on the same semantic version.
2. Update `CHANGELOG.md` and affected documentation in English.
3. Confirm `plugin.json` uses ID `vetra-theme`, name `Vetra Theme`, category `theme`, API 1, supported panels, exact tested Pelican version, and the Pelican Hub update URL.
4. Confirm English and Spanish translation key structures match.
5. Build and validate:

```bash
npm ci
npm run build
npm test
php tests/appearance.php
python scripts/package.py --repository BernyCampos/VetraTheme
```

Validate PHP syntax for all PHP files under `src`, `config`, `lang`, and `tests`.

## Artifacts

- `vetra-theme-VERSION.zip` — installable package.
- `vetra-theme-VERSION-source.zip` — source, tests, and workflow.
- `SHA256SUMS` — SHA-256 values for both ZIPs.
- `update.json` — release feed generated when `--repository` is present.

The installable ZIP must contain one `vetra-theme/` root, `plugin.json` without `meta`, compiled assets, documentation, and the GPL license. It must exclude secrets, `node_modules`, QA sessions, test results, and user backgrounds.

Build twice and compare the installable SHA-256 value. The packaging script fixes entry ordering, timestamps, and permissions to support reproducibility.

## GitHub Release

1. Commit only reviewed source and generated `dist/vetra.css`; do not commit local credentials or test results.
2. Push and wait for **Validate and package** to pass.
3. Tag `vVERSION` from the exact reviewed commit.
4. Create a release titled `Vetra Theme VERSION`.
5. Upload both ZIPs, `SHA256SUMS`, and `update.json`.
6. Publish notes based on the changelog, requirements, limitations, migration notes, and security behavior.

CI intentionally does not clone Pelican or run the removed `tests/integration.php`. That file caused the first Hub submission block and must not be restored.

## Update feed

`plugin.json` declares the configured Pelican Hub endpoint:

```text
https://hub.pelican.dev/plugins/vetra-theme/update.json
```

The repository release artifact `update.json` maps the exact Pelican version to the release download and SHA-256 checksum. It is generated for release verification; Pelican uses the manifest endpoint declared by the plugin.

## Pelican Hub submission

- Use category **Theme**.
- Submit the exact commit that passed CI, not the previously rejected commit.
- Use the English listing in [PELICAN_HUB.md](PELICAN_HUB.md).
- Upload the installable ZIP and five supplied screenshots.
- Declare GPL-3.0-only, supported Pelican version, support channel, limitations, and privacy behavior.
- State that Spanish is a panel-locale translation, not the source language.
- Confirm no telemetry, direct SQL, migrations, obfuscation, or hidden remote calls.
- Verify the submitted revision no longer references commit `fb269f9adce6`; the corrected baseline was commit `4e73f6b` before the current language and branding work.

Publication requirements can change. Recheck the [official Pelican plugin documentation](https://pelican.dev/docs/panel/advanced/plugins/) and [Pelican Hub terms](https://hub.pelican.dev/tos) before submission.

## Checklist

- [ ] English code, metadata, tests, documentation, and listing.
- [ ] Spanish limited to `lang/es` runtime translation.
- [ ] `npm run build`, `npm test`, PHP syntax, and appearance tests pass.
- [ ] No `tests/integration.php` in source or ZIP.
- [ ] Reproducible ZIP hashes confirmed.
- [ ] Clean staging installation, update, and removal verified.
- [ ] All five screenshots match the current Vetra Theme name.
- [ ] GitHub Actions green on the submitted commit.
- [ ] Hub submission points to the new commit.
