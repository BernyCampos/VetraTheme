# Seguridad y privacidad

## Resumen

VetraTheme modifica presentación y configuración visual. No añade autenticación, permisos de servidor, SQL, migraciones, telemetría ni operaciones de Wings.

## Autorización

El formulario vive dentro de la administración de plugins. Antes de persistir, `saveSettings()` ejecuta nuevamente la política `update` sobre el modelo del plugin. Un usuario ordinario no puede modificar la configuración global.

## Ajustes y CSS

- Solo se aceptan claves conocidas.
- Los colores deben cumplir `#RRGGBB`.
- Los valores numéricos se limitan a rangos documentados.
- Las rutas de fondo deben ser locales, sin consulta, fragmento, comillas o segmentos `..`.
- El aviso nunca entra en las variables CSS.
- El CSS compilado es estático y adicional.

## Markdown

El aviso pasa por dos capas:

1. `Str::markdown()` elimina HTML crudo y bloquea enlaces inseguros.
2. `HtmlSanitizer` conserva únicamente elementos y atributos de formato documentados.

No se permiten imágenes, estilos, iframes, scripts, eventos ni esquemas como `javascript:` o `data:`. Si el renderizador falla, se devuelve texto escapado.

## Imágenes

Los mapas de bits se verifican por contenido real con `getimagesizefromstring()`, no solo por extensión. Se imponen tamaño máximo y dimensiones máximas.

Los SVG:

- Rechazan `DOCTYPE` y `ENTITY`.
- Se cargan con `LIBXML_NONET`.
- Conservan únicamente elementos y atributos gráficos permitidos.
- Eliminan scripts, estilos, imágenes, animaciones, eventos, enlaces y referencias remotas.
- Solo admiten referencias locales `url(#id)` en atributos permitidos.

## Almacenamiento y ruta pública

- Los nombres son aleatorios y no contienen el nombre original.
- La copia persistente vive en `storage/app/vetra-theme/background`.
- La ruta pública es GET/HEAD y no ofrece listado ni escritura.
- El parámetro exige 40 caracteres hexadecimales y una extensión permitida.
- La respuesta envía MIME explícito, `X-Content-Type-Options: nosniff` y CSP sandbox.

Los fondos son recursos públicos del tema. No subas información privada o confidencial.

## Red y privacidad

La distribución local:

- No envía telemetría ni analítica.
- No contiene llamadas HTTP salientes.
- No consulta servicios de imágenes.
- No incluye código ofuscado.
- No declara `update_url`.

El generador de releases puede añadir un `update_url` de GitHub cuando el mantenedor pasa expresamente `--repository owner/repo`. Pelican realizará entonces la comprobación de actualización según su mecanismo oficial y el feed documentado.

## Base de datos y sistema

No hay SQL directo, consultas raw, migraciones, seeders ni tablas. La única lectura de modelo en el guardado verifica el plugin y su política. No se ejecutan comandos del sistema, `chmod`, `chown`, procesos secundarios ni instaladores propios.

## Dependencias

El plugin no declara paquetes Composer adicionales. Usa las dependencias que ya forman parte de la versión objetivo de Pelican. Las dependencias Node son exclusivamente de desarrollo y no se distribuyen en el ZIP instalable.

## Reportar una vulnerabilidad

No publiques detalles explotables en una incidencia pública. Sigue la política de [SECURITY.md](../SECURITY.md) y proporciona versión de Pelican, versión de Vetra, pasos mínimos, impacto y registros saneados. Nunca adjuntes tokens, claves, cookies, `.env` o bases de datos reales.
