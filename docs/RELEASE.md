# Publicación

## Preparar una release

1. Confirma que `plugin.json`, `package.json` y `package-lock.json` tienen la misma versión.
2. Actualiza `CHANGELOG.md` y la documentación afectada.
3. Ejecuta:

```sh
npm ci
npm run build
npm test
php tests/appearance.php
php tests/integration.php /ruta/a/pelican-aislado
```

4. Repite las pruebas visuales relevantes en staging.
5. Genera los paquetes:

```sh
python scripts/package.py --repository propietario/repositorio
```

## Artefactos

El comando genera en `build/`:

- `vetra-theme-VERSION.zip`: paquete instalable.
- `vetra-theme-VERSION-source.zip`: código fuente y pruebas.
- `SHA256SUMS`: hashes de ambos paquetes.
- `update.json`: feed de actualización cuando se define `--repository`.

El ZIP instalable debe tener una sola carpeta raíz `vetra-theme/`, incluir `plugin.json` sin `meta` y excluir secretos, dependencias de desarrollo, sesiones, fondos de usuario y resultados QA.

## GitHub Release

1. Crea la etiqueta `vVERSION` desde el commit validado.
2. Crea una release con título `VetraTheme VERSION`.
3. Adjunta el ZIP instalable, el paquete fuente, `SHA256SUMS` y `update.json`.
4. Publica notas derivadas del changelog, requisitos, limitaciones y pasos de actualización.
5. Marca la primera versión estable como `latest` para que el feed `releases/latest/download/update.json` sea resoluble.

El workflow incluido valida y genera artefactos, pero no publica automáticamente ni necesita tokens de publicación.

## Feed de actualización

`scripts/package.py --repository owner/repo` añade `update_url` únicamente al manifiesto empaquetado. La fuente local permanece sin llamadas remotas.

El feed usa la versión exacta del panel:

```json
{
  "1.0.0-rc.2": {
    "version": "1.0.0",
    "download_url": "https://github.com/owner/repo/releases/download/v1.0.0/vetra-theme-1.0.0.zip",
    "sha256": "..."
  }
}
```

No uses `*` hasta validar formalmente un rango de versiones.

## Pelican Hub

Antes de enviar:

- Confirma propiedad o licencias de código e imágenes.
- Declara que no hay telemetría, ofuscación ni servicios remotos ocultos.
- Selecciona categoría **Theme**.
- Indica Pelican `1.0.0-rc.2` y Plugin API 1.
- Sube el ZIP instalable y las capturas de `docs/images`.
- Incluye licencia GPL-3.0, changelog, soporte, limitaciones y política de seguridad.
- No declares aprobación hasta completar la revisión de cada versión.

La ficha preparada está en [PELICAN_HUB.md](PELICAN_HUB.md). Los requisitos de publicación pueden cambiar; revisa siempre la [documentación oficial de plugins](https://pelican.dev/docs/panel/advanced/plugins/) y los [términos de Pelican Hub](https://hub.pelican.dev/tos) antes de enviar.

## Lista final

- [ ] Pruebas PHP, Node e integración correctas.
- [ ] CSS recompilado.
- [ ] Capturas revisadas y sin secretos.
- [ ] ZIP reproducible.
- [ ] Hashes verificados.
- [ ] Instalación limpia en staging.
- [ ] Actualización y desinstalación verificadas.
- [ ] Changelog y compatibilidad actualizados.
- [ ] Release y feed apuntan a los mismos archivos.
