# Changelog

Todos los cambios relevantes de VetraTheme se documentarán aquí. El proyecto sigue [Semantic Versioning](https://semver.org/) y el formato de [Keep a Changelog](https://keepachangelog.com/).

## [1.0.0] — 2026-11-03

Primera publicación pública.

### Añadido

- Tema Glass original para los paneles `admin`, `app` y `server` de Pelican.
- Estilos Glass, Solid, Floating y modo claro con paletas independientes.
- Configuración persistente de colores, opacidad, blur, radios, navegación, densidad y accesibilidad.
- Subida de fondos JPG, PNG, WebP, GIF, AVIF, BMP, ICO y SVG saneado.
- Aviso global con Markdown seguro y separación visual de la barra superior.
- Cabecera de servidor, seis tarjetas de recursos independientes y botones de energía con SVG nativos.
- Consola, gestor de archivos, tablas, formularios y modales integrados con el lenguaje visual del tema.
- Disposición opcional de gráficos nativos a la derecha en pantallas amplias.
- Traducciones en español e inglés.
- Pruebas de apariencia, seguridad, integración Laravel/Filament y regresión visual en navegador.
- Empaquetado reproducible, workflow de validación y preparación para GitHub Releases y Pelican Hub.

### Seguridad

- Sin SQL, migraciones, telemetría, código ofuscado ni llamadas remotas ocultas.
- Autorización administrativa antes de persistir ajustes.
- Saneamiento estricto de Markdown y SVG.
- Almacenamiento persistente de fondos con nombres aleatorios y ruta pública de solo lectura.
- CSS adicional cargado sin sustituir el tema fundamental de Filament ni modificar el núcleo.

