# Solución de problemas

## La instalación queda pendiente

- Comprueba que el worker de cola de Pelican esté activo.
- Revisa los trabajos fallidos y `storage/logs/laravel.log`.
- Confirma que instalaste `vetra-theme-1.0.0.zip` y no `vetra-theme-1.0.0-source.zip`.
- No vuelvas a importar repetidamente mientras la instalación continúa.

## El tema no aparece

- Confirma que la carpeta se llama exactamente `vetra-theme`.
- Revisa que la fila indique categoría **Tema**, versión `1.0.0` y estado **Activado**.
- Desactiva cualquier otro tema.
- Realiza una recarga completa y limpia únicamente las cachés siguiendo la documentación de Pelican.

## Los cambios no se aplican

- Pulsa **Enviar** en los ajustes y espera la notificación de guardado.
- Recarga completamente el navegador.
- Comprueba que el proceso PHP puede actualizar el `.env` mediante el mecanismo oficial.
- Si Pelican usa configuración cacheada, regenera esa caché con el procedimiento normal de tu despliegue.

## No se puede subir el fondo

- Verifica el límite de 5 MB y 8192 × 8192 px.
- Convierte HEIC, TIFF o PSD a PNG, JPG o WebP.
- Confirma que `storage/app` sea escribible por el proceso del panel.
- No es obligatorio que `plugins/vetra-theme/background` sea escribible; ese directorio es solo un espejo opcional.
- Revisa el mensaje de validación y el registro del panel.

## El fondo devuelve 404

- Guarda otra vez los ajustes.
- Comprueba que el archivo exista en `storage/app/vetra-theme/background`.
- Revisa que la ruta empiece por `/vetra-theme/background/` y contenga un nombre hexadecimal generado por el plugin.
- Si existe una caché de rutas previa, límpiala o regénérala usando el procedimiento oficial del despliegue.

## El aviso no muestra cierto HTML

Es intencional. El aviso admite Markdown seguro, pero elimina HTML, imágenes, estilos, scripts, eventos e incrustaciones. Usa los elementos documentados en [CONFIGURATION.md](CONFIGURATION.md).

## El panel muestra HTTP 500

1. Conserva las últimas líneas de `storage/logs/laravel.log`.
2. En el `.env` del panel establece temporalmente:

```env
PANEL_PLUGIN_SAFE_MODE=true
```

3. Si existe una configuración cacheada, elimina únicamente `bootstrap/cache/config.php` o regenera la caché según tu despliegue.
4. Recarga Pelican. En modo seguro los plugins no se cargan.
5. Desactiva VetraTheme desde la administración o, desde la raíz del panel, ejecuta:

```sh
php artisan p:plugin:disable vetra-theme
```

6. Revisa la versión de Pelican, el log y la integridad del ZIP antes de reinstalar.
7. Cuando el panel esté estable, establece `PANEL_PLUGIN_SAFE_MODE=false` y vuelve a regenerar la caché.

No elimines `storage`, la base de datos, `composer.json`, `composer.lock` ni el directorio completo de caché sin un diagnóstico y respaldo.

## La consola o los archivos no funcionan

Vetra no implementa esas operaciones. Desactiva temporalmente el tema para distinguir un problema visual de un problema de Pelican, Wings, permisos o red. Si la operación también falla con el tema desactivado, consulta los registros y la documentación de Pelican/Wings.

## Información para un reporte

Incluye:

- Versiones exactas de Pelican, Vetra, PHP y navegador.
- Panel afectado: `admin`, `app` o `server`.
- Pasos mínimos para reproducir.
- Resultado esperado y resultado real.
- Captura sin secretos.
- Extracto relevante del log, eliminando tokens, claves, cookies, IP privadas y datos personales.
