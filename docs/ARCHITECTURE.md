# Arquitectura

VetraTheme es un plugin de tema aditivo. Se integra mediante las APIs de plugins y los hooks de Filament que Pelican expone; no reemplaza vistas, controladores, recursos ni assets del núcleo.

## Estructura

| Ruta | Responsabilidad |
| --- | --- |
| `plugin.json` | Identidad, categoría `theme`, paneles y compatibilidad |
| `src/VetraThemePlugin.php` | Registro del tema, hooks, colores, navegación y persistencia |
| `src/Appearance.php` | Valores predeterminados, normalización y variables CSS |
| `src/SettingsForm.php` | Formulario Filament y reglas de validación |
| `src/Notice.php` | Renderizado y saneamiento del aviso Markdown |
| `src/BackgroundImage.php` | Validación de contenido, tamaño, dimensiones y formato |
| `src/SvgSanitizer.php` | Lista permitida para SVG sin red ni entidades |
| `src/BackgroundStorage.php` | Copia persistente, espejo opcional y respuesta de imagen |
| `src/Providers/BackgroundRouteServiceProvider.php` | Ruta GET pública y acotada para fondos |
| `resources/views` | Fragmentos Blade registrados bajo `vetra-theme::` |
| `resources/css/vetra.css` | Fuente CSS adicional |
| `dist/vetra.css` | CSS compilado distribuido en el ZIP |
| `tests` | Pruebas unitarias ligeras, integración y navegador |

## Registro en los paneles

`VetraThemePlugin` implementa `Filament\Contracts\Plugin` y `App\Contracts\Plugins\HasPluginSettings`. El manifiesto declara `admin`, `app` y `server`.

Se utilizan estos hooks:

- `STYLES_AFTER`: inserta variables y CSS compilado después de los estilos nativos.
- `BODY_START`: añade una capa ambiental fija, decorativa y sin eventos.
- `CONTENT_START`: muestra el aviso global cuando tiene contenido.
- `PAGE_HEADER_HEADING_AFTER`: añade contexto del servidor únicamente en la consola.

No se llama `theme()` ni `viteTheme()`. El CSS distribuido se lee desde `dist/vetra.css`, por lo que una petición no depende de resolver el manifiesto de Vite del panel.

## Presentación

La capa ambiental usa tres gradientes y, opcionalmente, una imagen. Las superficies de Filament reciben color, alfa, radio y sombra, pero conservan su DOM, eventos y permisos.

La terminal y el editor se mantienen sólidos. Vetra no recolorea el canvas, los códigos ANSI ni el contenido de Monaco. En pantallas amplias, un selector CSS coloca a la derecha los gráficos que ya existen; si Pelican no los renderiza, no se crea información sustituta.

Los seis recursos de la consola conservan tarjetas individuales. El contenedor que los agrupa no añade una séptima superficie. Los botones de energía usan sus SVG y acciones Livewire originales; solo cambia su presentación.

## Persistencia

Los ajustes se escriben mediante `EnvironmentWriterTrait::writeToEnvironment()` y claves `VETRA_THEME_*`. No existen tablas, migraciones ni un escritor de entorno propio.

El aviso se codifica en base64 para conservar Markdown multilínea dentro de una variable. Los colores y números se normalizan antes de convertirse en CSS. El formulario vuelve a validar y autorizar los datos en `saveSettings()`.

## Flujo de fondos

1. Filament entrega un `UploadedFile` temporal sin almacenarlo automáticamente.
2. `BackgroundImage` valida tamaño, contenido y dimensiones.
3. Los mapas de bits se preservan; los SVG pasan por `SvgSanitizer`.
4. Se crea un nombre aleatorio de 40 caracteres hexadecimales.
5. La copia autoritativa se escribe en `storage/app/vetra-theme/background`.
6. Se intenta crear un espejo en `plugins/vetra-theme/background` sin alterar permisos.
7. La configuración guarda una ruta `/vetra-theme/background/NOMBRE`.
8. Una ruta GET/HEAD devuelve la copia persistente con MIME explícito, `nosniff`, CSP y caché inmutable.

Si el entorno no puede guardarse, la imagen recién subida se revierte. Quitar un fondo solo elimina su asociación, no los archivos previos.

## Degradación segura

- Sin `backdrop-filter`, las superficies conservan color sólido.
- Sin soporte completo de selectores modernos, Filament conserva su estructura funcional.
- Si falla el renderizador Markdown, el aviso se muestra como texto escapado.
- Si no puede registrarse la ruta opcional de fondos, el tema sigue arrancando sin esa imagen.
- Un directorio de plugins de solo lectura no bloquea el almacenamiento persistente.

## Límites

Vetra no implementa métricas, WebSockets, controles de energía, operaciones de archivos, permisos, preferencias por usuario ni un editor alternativo. Esas funciones permanecen bajo Pelican y Wings.
