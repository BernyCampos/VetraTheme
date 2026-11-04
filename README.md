# VetraTheme

Tema Glass configurable para **Pelican Panel 1.0.0-rc.2**, distribuido como plugin oficial de categoría `theme` y API 1.

[![Version](https://img.shields.io/badge/version-1.0.0-739fe3)](CHANGELOG.md)
[![Pelican](https://img.shields.io/badge/Pelican-1.0.0--rc.2-9a86bc)](docs/COMPATIBILITY.md)
[![License](https://img.shields.io/badge/license-GPL--3.0-2da44e)](LICENSE)

VetraTheme añade iluminación ambiental, superficies translúcidas y personalización persistente sin sustituir el CSS fundamental de Filament, modificar el núcleo de Pelican ni recrear sus funciones. Consola, archivos, métricas, permisos y acciones de energía siguen perteneciendo al panel.

![Consola de servidor con VetraTheme](docs/images/server-console.png)

## Características

- Compatibilidad visual con los paneles `admin`, `app` y `server`.
- Estilos Glass, Solid, Floating y Light Glass.
- Navegación lateral, superior o mixta mediante las APIs de Filament.
- Paletas independientes para modo claro y oscuro.
- 29 colores configurables para iluminación, superficies, navegación, textos, bordes, campos, avisos y estados.
- Fondo configurable desde los ajustes: JPG, PNG, WebP, GIF, AVIF, BMP, ICO y SVG saneado.
- Aviso global con Markdown seguro, enlaces permitidos y diseño adaptable.
- Cabecera de servidor, tarjetas de recursos y botones de energía con iconos SVG nativos.
- Consola y editor con superficies sólidas y legibles.
- Gráficos nativos en columna lateral cuando Pelican realmente los proporciona.
- Configuración persistente sin tablas, migraciones ni escrituras en el núcleo.
- Español e inglés, foco visible, alto contraste y reducción de movimiento/transparencia.

## Galería

### Administración

![Listado administrativo de Eggs](docs/images/admin-eggs.png)

### Ajustes visuales

![Ajustes principales de VetraTheme](docs/images/settings-appearance.png)

![Paletas y aviso global](docs/images/settings-notice.png)

### Gestor de archivos

![Gestor de archivos con VetraTheme](docs/images/file-manager.png)

Consulta la [galería completa](docs/SCREENSHOTS.md) para conocer qué demuestra cada captura.

## Requisitos

- Pelican Panel `1.0.0-rc.2`.
- Plugin API 1.
- PHP 8.4 con las extensiones exigidas por Pelican, incluidas DOM, Fileinfo y Mbstring.
- Worker de cola de Pelican activo durante instalación y actualización.
- Navegador moderno con soporte de `:has()` y `color-mix()` para el acabado completo.

La compatibilidad está acotada deliberadamente a la versión verificada. Consulta [COMPATIBILITY.md](docs/COMPATIBILITY.md) antes de usar otra versión.

## Instalación rápida

1. Realiza una copia de seguridad del panel.
2. Abre **Administración → Plugins → Importar**.
3. Selecciona `vetra-theme-1.0.0.zip`.
4. Pulsa **Instalar** y espera a que termine el trabajo de cola.
5. Activa **VetraTheme**. Pelican solo debe tener un tema activo.
6. Abre **Ajustes**, guarda la apariencia y recarga completamente la página.

No copies archivos dentro de `app`, `resources`, `vendor` ni otros directorios del núcleo. La [guía de instalación](docs/INSTALLATION.md) incluye actualización, desinstalación y verificación.

## Configuración

Los ajustes se administran desde **Administración → Plugins → VetraTheme → Ajustes**. Incluyen estilo, navegación, densidad, colores, opacidad, blur, radio, ancho lateral, fondo, aviso Markdown y preferencias de accesibilidad.

Las imágenes se guardan de forma persistente en `storage/app/vetra-theme/background`. Cuando es posible se mantiene un espejo en `plugins/vetra-theme/background`; Vetra no cambia permisos del servidor. El fondo se sirve desde una ruta pública de solo lectura con nombre aleatorio y cabeceras restrictivas.

Consulta [CONFIGURATION.md](docs/CONFIGURATION.md) para ver todos los campos, formatos admitidos y comportamiento de restauración.

## Migración de instalaciones anteriores

Esta edición cambia el identificador del plugin de `vetra-ui` a `vetra-theme`, el espacio de nombres de `Vetra\UI` a `Vetra\Theme` y el prefijo de configuración de `VETRA_UI_` a `VETRA_THEME_`. **Pelican los identifica como plugins distintos.** Si ya tenías una instalación anterior, respalda `.env` y las imágenes antes de desactivar el plugin antiguo. Revisa los pasos de migración manual en [Instalación](docs/INSTALLATION.md). No se migran automáticamente valores ni archivos.

## Seguridad y privacidad

VetraTheme no contiene consultas SQL, migraciones, telemetría, analítica, código ofuscado ni llamadas remotas ocultas. El paquete local no declara `update_url`; solo se añade al generar expresamente una release vinculada a GitHub.

- Los ajustes requieren la política administrativa de actualización del plugin.
- Markdown pasa por dos capas de saneamiento.
- SVG usa una lista permitida, `LIBXML_NONET` y bloqueo de DTD, entidades, scripts, eventos y referencias remotas.
- Las rutas de fondos aceptan únicamente nombres aleatorios con extensiones permitidas.
- El CSS es adicional y no reemplaza el tema base de Filament.

Consulta [SECURITY.md](SECURITY.md) y [docs/SECURITY.md](docs/SECURITY.md) para el modelo de seguridad y la política de reportes.

## Documentación

| Documento | Contenido |
| --- | --- |
| [Instalación](docs/INSTALLATION.md) | Importación, activación, actualización y desinstalación |
| [Configuración](docs/CONFIGURATION.md) | Opciones visuales, fondos, colores y aviso Markdown |
| [Capturas](docs/SCREENSHOTS.md) | Galería y alcance de la evidencia visual |
| [Arquitectura](docs/ARCHITECTURE.md) | Integración, componentes, almacenamiento y límites |
| [Seguridad](docs/SECURITY.md) | Superficies de ataque, validación y privacidad |
| [Compatibilidad](docs/COMPATIBILITY.md) | Versiones, paneles, navegadores y dependencias visuales |
| [Validación](docs/VALIDATION.md) | Pruebas ejecutadas y limitaciones conocidas |
| [Solución de problemas](docs/TROUBLESHOOTING.md) | Cola, caché, fondos, errores 500 y modo seguro |
| [Publicación](docs/RELEASE.md) | GitHub Releases, feed de actualización y Pelican Hub |
| [Ficha de Hub](docs/PELICAN_HUB.md) | Texto, características y capturas para Marketplace |
| [Contribuir](CONTRIBUTING.md) | Desarrollo, pruebas y pull requests |

## Desarrollo

```sh
npm ci
npm run build
npm test
php tests/appearance.php
php tests/integration.php /ruta/a/pelican-aislado
python scripts/package.py
```

El test de integración debe ejecutarse contra una copia aislada de Pelican con sus dependencias instaladas. Nunca uses una instancia de producción para desarrollar o ejecutar fixtures.

## Distribución

`scripts/package.py` genera dos paquetes reproducibles:

- `vetra-theme-1.0.0.zip`: instalable en Pelican.
- `vetra-theme-1.0.0-source.zip`: fuente, pruebas y herramientas de desarrollo.

El paquete instalable contiene una única carpeta raíz `vetra-theme`, manifiesto sin `meta`, recursos compilados, documentación y licencia. No incluye dependencias de desarrollo, secretos, sesiones de prueba ni fondos subidos por usuarios.

## Licencia

[GNU General Public License v3.0 (GPL-3.0-only)](LICENSE). Copyright © 2026 Berny Campos. VetraTheme es un proyecto independiente y no implica aprobación automática de Pelican Hub.
