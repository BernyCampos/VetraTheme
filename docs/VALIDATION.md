# Validación de VetraTheme 1.0.0

Fecha: 3 de noviembre de 2026. Referencia: Pelican `v1.0.0-rc.2`, commit `f64a167eea349bfbb89cda8732eacf682a82a4b3`.

## Entorno automatizado

- Windows, PHP 8.4.27, Node.js 22.23.2 y Vite 8.3.4.
- Copia aislada de Pelican con Laravel 13.38.0 y Filament 5.10.2.
- Base SQLite y usuario QA desechables.
- Chrome/Chromium para regresión visual.

Las pruebas automatizadas no se ejecutan contra el panel de producción del usuario.

## Resultados

| Área | Resultado |
| --- | --- |
| Sintaxis PHP propia | Correcta |
| Normalización, límites, rutas, CSS y traducciones | 19 comprobaciones correctas |
| Integración real Laravel/Filament | 65 comprobaciones correctas |
| Manifiesto, CSS compilado, documentación y paquete | 4 pruebas Node correctas |
| Usuario ordinario rechazado / administrador autorizado | Correcto |
| Persistencia mediante `EnvironmentWriterTrait` | Correcta |
| 29 colores y paletas claro/oscuro | Guardado, recarga y restauración correctos |
| Markdown | Formato permitido y vectores inseguros rechazados |
| Imágenes raster | PNG, JPEG, GIF, BMP, ICO y WebP validados |
| SVG | Geometría permitida conservada; contenido activo eliminado |
| Directorio del plugin no escribible | El fondo continúa desde almacenamiento persistente |
| Ruta pública de fondo | HTTP 200, MIME explícito y nombre restringido |
| CSS sin consulta de Vite en petición | Verificado |
| Admin, app y server | Carga y estilos verificados en la copia QA |
| Claro/oscuro y 1440/390 px | Capturados sin desbordamiento horizontal en vistas probadas |
| Aviso y topbar | Separación de 20 px verificada |
| Recursos de consola | Grupo sin marco adicional; seis tarjetas con borde individual |
| Controles de energía | Grupo sin marco; tres botones con SVG, borde y nombre accesible |
| Encabezados y tablas | Superficie, borde, radio y sombra conservados |
| ZIP reproducible | Hash idéntico entre dos compilaciones consecutivas |
| Núcleo de Pelican | Sin modificaciones en app, resources, config, routes, bootstrap, Composer o Vite |

## Evidencia visual proporcionada

Las capturas incluidas muestran una instalación de Pelican con VetraTheme en:

- Administración de Eggs.
- Modal de ajustes y paletas.
- Consola de un servidor en ejecución.
- Gestor de archivos.

Consulta [SCREENSHOTS.md](SCREENSHOTS.md). Estas capturas prueban el resultado visual observado, pero no sustituyen pruebas automatizadas de cada operación de Wings.

## Seguridad comprobada

- Sin SQL directo, migraciones, ejecución de procesos o telemetría.
- Rechazo de rutas remotas, traversal, consultas, fragmentos e inyección CSS.
- Límite de 5 MB y dimensiones máximas para imágenes.
- Rechazo de DTD, entidades, scripts, eventos, estilos, imágenes y referencias remotas en SVG.
- Rechazo de HTML, imágenes y esquemas inseguros en Markdown.
- Reversión de archivos nuevos cuando falla la persistencia de ajustes.
- El ZIP no contiene `.env`, sesiones, fondos de usuario, `node_modules` o capturas de prueba internas.

## Pendiente antes de ampliar compatibilidad

- Pruebas completas en Firefox y Safari.
- Pruebas en versiones de Pelican distintas de `1.0.0-rc.2`.
- Matriz extensa con plugins que alteren navegación, consola o tablas.
- Auditoría externa de accesibilidad y seguridad.
- Operaciones destructivas de archivos y energía bajo distintas políticas de subusuario.
- Cargas prolongadas de WebSocket, gráficas y terminal.
- Revisión y aprobación de Pelican Hub.

## Reproducción

```sh
npm ci
npm run build
npm test
php tests/appearance.php
php tests/integration.php /ruta/a/pelican-aislado
python scripts/package.py
```

Los scripts `tests/browser*.mjs` requieren una instancia QA, Chrome y las variables `VETRA_URL`, `VETRA_EMAIL`, `VETRA_PASSWORD` y `VETRA_SERVER_PATH` cuando corresponda. Las sesiones y capturas generadas viven en `test-results`, que está excluido del paquete.

## Criterio de publicación

La versión 1.0.0 es una primera entrega instalable para la versión exacta de Pelican declarada. La ficha de Marketplace debe mantener visibles las limitaciones anteriores y no afirmar aprobación antes de completar la revisión de Pelican Hub.
