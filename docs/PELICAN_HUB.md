# Ficha para Pelican Hub

## Nombre

VetraTheme

## Categoría

Theme

## Resumen corto

Tema Glass configurable para Pelican Panel con fondos, paletas claro/oscuro, aviso Markdown seguro y superficies legibles para administración, consola y archivos.

## Descripción

VetraTheme transforma la presentación de Pelican Panel con iluminación ambiental azul, violeta y ámbar, navegación translúcida y superficies adaptables. Conserva las funciones, permisos y componentes nativos de Pelican y Filament: no reemplaza la consola, el gestor de archivos, las métricas ni los controles del servidor.

El administrador puede elegir estilo Glass, Solid, Floating o claro; ajustar 29 colores; cambiar opacidad, blur, radios, densidad y navegación; subir un fondo; y publicar un aviso global con Markdown seguro. La configuración se guarda mediante los mecanismos oficiales del panel y no requiere migraciones.

## Características destacadas

- Soporte para `admin`, `app` y `server`.
- Paletas claro/oscuro y estados nativos de Filament.
- Fondos JPG, PNG, WebP, GIF, AVIF, BMP, ICO y SVG saneado.
- Aviso global Markdown sin HTML activo.
- Cabecera, recursos, consola y archivos con diseño coherente.
- Botones de energía con SVG nativos y nombres accesibles.
- Preferencias de reducción de movimiento, transparencia y alto contraste.
- Sin telemetría, SQL, migraciones o dependencias Composer adicionales.

## Requisitos

- Pelican Panel `1.0.0-rc.2`.
- Plugin API 1.
- PHP 8.4 y extensiones requeridas por Pelican.
- Worker de cola activo durante instalación/actualización.

## Instalación

Importa `vetra-theme-1.0.0.zip` desde **Administración → Plugins**, instala, activa el tema y guarda sus ajustes. No uses el paquete `-source.zip` como instalable.

## Privacidad y red

La distribución no contiene telemetría, analítica, código ofuscado ni llamadas remotas ocultas. El feed de GitHub solo existe en builds de release que declaran expresamente un repositorio.

## Capturas sugeridas

1. `docs/images/server-console.png` — Consola y recursos.
2. `docs/images/file-manager.png` — Gestor de archivos.
3. `docs/images/admin-eggs.png` — Administración.
4. `docs/images/settings-appearance.png` — Ajustes principales.
5. `docs/images/settings-notice.png` — Paletas y aviso.

## Soporte y limitaciones

Compatibilidad verificada únicamente con Pelican `1.0.0-rc.2`. Otros temas o plugins que alteren navegación, tablas o consola pueden requerir validación conjunta. La aprobación del Marketplace corresponde a Pelican Hub y se realiza por versión.

## Licencia

MIT.
