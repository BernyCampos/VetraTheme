# Instalación

Esta guía cubre la instalación oficial de VetraTheme 1.0.0 en Pelican Panel 1.0.0-rc.2.

## Antes de comenzar

- Confirma la versión de Pelican en **Administración → Estado**.
- Comprueba que el worker de cola esté activo.
- Crea una copia de seguridad del panel y de su archivo `.env`.
- Desactiva otros temas; Pelican solo debe cargar un tema a la vez.
- No extraigas el ZIP dentro de `app`, `resources`, `public` o `vendor`.

## Instalación desde el panel

1. Descarga `vetra-theme-1.0.0.zip`.
2. Abre **Administración → Plugins**.
3. Pulsa **Importar** y selecciona el ZIP instalable, no el paquete `-source.zip`.
4. Cuando finalice la importación, pulsa **Instalar**.
5. Espera a que termine el trabajo de cola. No recargues repetidamente durante la compilación.
6. Pulsa **Activar** si el tema no quedó activo automáticamente.
7. Abre **Ajustes**, revisa la configuración inicial y pulsa **Enviar**.
8. Haz una recarga completa del navegador.

## Instalación manual oficial

Usa este método únicamente si administras el servidor por consola.

1. Coloca la carpeta extraída `vetra-theme` dentro de `plugins` en la raíz de Pelican. La ruta habitual es `/var/www/pelican/plugins/vetra-theme`.
2. Desde la raíz del panel ejecuta:

```sh
php artisan p:plugin:install vetra-theme
```

3. Espera a que finalice la instalación y activa el tema desde **Administración → Plugins**.

El ID del manifiesto y el nombre de la carpeta deben seguir siendo `vetra-theme`.

## Verificación

Después de instalar:

- La fila de VetraTheme debe mostrar versión `1.0.0`, categoría **Tema** y estado **Activado**.
- Deben aparecer ajustes de apariencia al pulsar **Ajustes**.
- Las páginas administrativas, el listado de servidores y la consola deben cargar sin errores.
- La consola y el gestor de archivos deben conservar sus acciones nativas.
- El navegador no debe mostrar errores JavaScript relacionados con Vetra.

## Actualización

1. Realiza una copia de seguridad.
2. Importa el nuevo ZIP desde **Administración → Plugins**.
3. Usa la acción **Actualizar** y espera al worker.
4. Recarga completamente el navegador.
5. Revisa el changelog y vuelve a validar consola, archivos y ajustes.

Los ajustes `VETRA_THEME_*` y los fondos de `storage/app/vetra-theme/background` permanecen fuera de la carpeta reemplazable del plugin.

## Desinstalación

1. Desactiva VetraTheme.
2. Confirma que el aspecto nativo de Pelican regresa después de recargar.
3. Usa **Desinstalar** desde la lista de plugins o ejecuta:

```sh
php artisan p:plugin:uninstall vetra-theme
```

Pelican retira el plugin mediante su mecanismo oficial. Las variables `VETRA_THEME_*` y los fondos persistentes se conservan para evitar pérdida accidental. Un administrador puede eliminarlos posteriormente siguiendo sus procedimientos habituales de respaldo y mantenimiento.

## Migración desde `vetra-ui`

Este cambio es un renombrado interno del plugin, no una actualización del mismo ID. Una instancia existente de `vetra-ui` **no actualiza automáticamente** a `vetra-theme`.

1. Haz respaldo del `.env` y de `storage/app/vetra-ui/background` si aún existen en tu instalación antigua.
2. Desactiva `vetra-ui` desde Pelican antes de instalar o activar `vetra-theme`.
3. Instala `vetra-theme` y comprueba que el tema cargue. No mantengas los dos temas activos a la vez.
4. Si necesitas conservar ajustes, copia **solo** las variables del plugin antiguo `VETRA_UI_*` a nuevas claves `VETRA_THEME_*` en el `.env`, conservando los valores. Para fondos locales, copia los archivos antiguos al almacenamiento nuevo `storage/app/vetra-theme/background` y cambia la ruta configurada `/vetra-ui/background/NOMBRE` a `/vetra-theme/background/NOMBRE` únicamente si el archivo está presente.
5. Vacía la caché de configuración de Pelican mediante su procedimiento de mantenimiento, comprueba los enlaces de los fondos y verifica los ajustes antes de desinstalar el plugin anterior.

No se realiza una migración automática ni se eliminan datos del plugin anterior. Haz la migración primero en una instancia de pruebas.

## No hacer

- No modifiques archivos del núcleo para instalar el tema.
- No ejecutes `chmod -R 777`.
- No copies dependencias dentro de `vendor` manualmente.
- No uses el paquete de código fuente como ZIP instalable.
- No actives Vetra en una versión de Pelican distinta sin una prueba previa en staging.

Para errores de cola, caché, carga o fondos, consulta [TROUBLESHOOTING.md](TROUBLESHOOTING.md).
