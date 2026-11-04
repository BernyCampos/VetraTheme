# Compatibilidad

## Versión objetivo

| Componente | Versión verificada |
| --- | --- |
| Pelican Panel | `1.0.0-rc.2` |
| Plugin API | 1 |
| PHP | 8.4 |
| Laravel | 13.38.0 en el lock verificado |
| Filament | 5.10.2 en el lock verificado |
| Node.js de desarrollo | 22.23.2 |
| Vite de desarrollo | 8.3.4 |

El manifiesto usa una restricción estricta `panel_version: 1.0.0-rc.2`. No se declara compatibilidad con otras versiones hasta probarlas.

## Paneles

| Panel | Estado | Alcance |
| --- | --- | --- |
| `admin` | Compatible | Navegación, encabezados, tablas, formularios, modales y avisos |
| `app` | Compatible | Listado de servidores y topbar nativos; no se inventa sidebar |
| `server` | Compatible | Navegación, cabecera, recursos, consola, archivos y gráficos nativos |

## Navegadores

Chrome/Chromium moderno es el navegador validado automáticamente. El tema usa `:has()`, `color-mix()`, variables CSS y, de forma progresiva, `backdrop-filter`. Firefox y Safari modernos deberían aplicar la mayor parte del diseño, pero requieren validación de release si se anuncian como oficialmente soportados.

Sin `backdrop-filter`, Vetra mantiene superficies sólidas. Las preferencias `prefers-reduced-motion`, `prefers-reduced-transparency` y `forced-colors` reciben tratamiento explícito.

## Dependencias visuales

Estas clases pertenecen a la estructura de Filament/Pelican y deben revisarse al actualizar:

| Selector / API | Uso |
| --- | --- |
| `.fi-sidebar`, `.fi-topbar`, `.fi-header` | Navegación y cabeceras |
| `.fi-section`, `.fi-ta-ctn`, `.fi-ta-record` | Tarjetas y tablas |
| `.fi-console-page`, `.fi-wi-widget`, `.fi-wi-chart` | Consola y gráficos |
| `.fi-small-stat-block`, `#terminal`, `#send-command` | Recursos y terminal |
| `.fi-header-actions-ctn .fi-btn-group` | Controles de energía nativos |
| `Filament::getTenant()` | Contexto autorizado del servidor |
| `EnvironmentWriterTrait` | Persistencia oficial de ajustes |

Otros plugins que reemplacen navegación, cabeceras, tablas o widgets pueden requerir reglas de compatibilidad específicas.

## Fuentes oficiales consultadas

Consulta verificada el 3 de noviembre de 2026:

- [Documentación oficial de plugins](https://pelican.dev/docs/panel/advanced/plugins/).
- [Release oficial Pelican v1.0.0-rc.2](https://github.com/pelican/panel/releases/tag/v1.0.0-rc.2).
- [Repositorio de Pelican en el commit verificado](https://github.com/pelican/panel/tree/f64a167eea349bfbb89cda8732eacf682a82a4b3).
- [Repositorio oficial de plugins](https://github.com/pelican/plugins).
- [Términos de Pelican Hub](https://hub.pelican.dev/tos).
- [File Upload de Filament 5](https://filamentphp.com/docs/5.x/forms/file-upload).
- [Colores de Filament 5](https://filamentphp.com/docs/5.x/styling/colors).
- [Markdown de Laravel 13](https://laravel.com/docs/13.x/strings#method-str-markdown).

El sistema oficial de plugins continúa marcado como una superficie en desarrollo. El esquema de `plugin.json`, API 1, `HasPluginSettings`, auto-descubrimiento y semántica de compatibilidad son la base estable documentada; clases internas y markup visual pueden cambiar.
