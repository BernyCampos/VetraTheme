# Configuración

VetraTheme se configura desde **Administración → Plugins → VetraTheme → Ajustes**. Los cambios son globales y requieren permiso administrativo para actualizar el plugin.

![Ajustes principales](images/settings-appearance.png)

## Apariencia

| Campo | Opciones / rango | Función |
| --- | --- | --- |
| Estilo | Glass, Solid, Floating, Light Glass | Define transparencia, navegación flotante y tratamiento general |
| Modo inicial | Sistema, claro, oscuro | Valor inicial; el selector personal de Pelican conserva la elección del usuario |
| Navegación | Preferencia de Pelican, lateral, superior, mixta | Usa la navegación nativa disponible en cada panel |
| Densidad | Cómoda, compacta | Ajusta espaciado de tablas y cabeceras |
| Radio | 8–28 px | Controla el redondeo de superficies |
| Ancho lateral | 220–360 px | Ajusta la barra lateral donde existe |
| Gráficos laterales | Activado/desactivado | Coloca gráficos nativos a la derecha en pantallas desde 1280 px |
| Reducir transiciones | Activado/desactivado | Desactiva movimiento decorativo del tema |

El panel `app` no recibe una barra lateral inventada: conserva su estructura nativa. La preferencia de navegación solo se aplica donde Pelican expone ese componente.

## Colores

Los tres colores ambientales principales son:

- **Primario:** botones, selección e iluminación superior/derecha.
- **Secundario:** iluminación central.
- **Acento:** iluminación inferior/izquierda.

Los estados `success`, `warning`, `danger` e `info` se entregan a `Panel::colors()` para que Filament genere sus paletas nativas.

Las paletas claro y oscuro permiten configurar por separado:

- Fondo de página.
- Superficies.
- Sidebar.
- Topbar.
- Texto principal y texto secundario.
- Bordes.
- Fondo y acento del aviso.
- Marco de consola.
- Campos de formulario.

Todos los colores deben ser hexadecimales de seis dígitos. Los valores que puedan producir inyección CSS se rechazan.

## Superficies y fondo

| Campo | Rango | Función |
| --- | --- | --- |
| Opacidad | 65–100 % | Transparencia de tarjetas y navegación |
| Blur de navegación | 0–40 px | Desenfoque de sidebar y topbar |
| Blur de imagen | 0–30 px | Suavizado del fondo cargado |
| Atenuación | 0–90 % | Oscurece el fondo para preservar legibilidad |

### Subir una imagen

1. Abre **Superficies y fondo**.
2. Arrastra o selecciona una imagen.
3. Pulsa **Enviar**.
4. Recarga la página.

Formatos admitidos:

| Formato | Comportamiento |
| --- | --- |
| JPG / PNG | Bytes originales preservados después de validar el contenido |
| WebP / GIF | Preservados; los GIF animados no se convierten |
| AVIF / BMP / ICO | Admitidos cuando PHP reconoce correctamente el contenido |
| SVG | Analizado y reescrito mediante una lista gráfica permitida |

Límites: 5 MB y hasta 8192 × 8192 píxeles para mapas de bits. HEIC, TIFF y PSD deben convertirse antes.

La copia persistente se guarda en `storage/app/vetra-theme/background`. El espejo `plugins/vetra-theme/background` es opcional y nunca se fuerza cambiando permisos. La ruta almacenada usa un nombre aleatorio y no revela el nombre original.

**Quitar fondo** desasocia la imagen al guardar. No elimina archivos previos automáticamente, para evitar pérdida de datos.

## Aviso global

![Aviso global y paletas](images/settings-notice.png)

El aviso admite hasta 2000 caracteres de Markdown:

```md
## Mantenimiento programado

- Inicio: 22:00
- Consulta el [estado del servicio](https://ejemplo.com/estado).
```

Se permiten encabezados, negrita, cursiva, tachado, listas, citas, código y enlaces `https`, `http` o `mailto`. HTML crudo, imágenes, estilos, eventos, scripts, esquemas inseguros e incrustaciones se eliminan.

El contenido se almacena en `VETRA_THEME_NOTICE_MARKDOWN` codificado en base64 para mantener una sola línea válida dentro del archivo de entorno. Base64 no es cifrado; no introduzcas secretos.

## Restaurar valores

Activa **Restaurar la apariencia predeterminada al guardar** y pulsa **Enviar**. Se restauran todos los valores globales de Vetra, incluido el aviso y la asociación del fondo. No se modifican las preferencias personales de tema, consola o editor administradas por Pelican.

## Variables persistentes

Todas las claves usan el prefijo `VETRA_THEME_`. Las principales son:

- `VETRA_THEME_STYLE`, `VETRA_THEME_MODE`, `VETRA_THEME_NAVIGATION`, `VETRA_THEME_DENSITY`.
- `VETRA_THEME_PRIMARY`, `VETRA_THEME_SECONDARY`, `VETRA_THEME_ACCENT`.
- `VETRA_THEME_OPACITY`, `VETRA_THEME_BLUR`, `VETRA_THEME_RADIUS`, `VETRA_THEME_SIDEBAR_WIDTH`.
- `VETRA_THEME_BACKGROUND`, `VETRA_THEME_BACKGROUND_BLUR`, `VETRA_THEME_BACKGROUND_DIM`.
- `VETRA_THEME_SIDE_CHARTS`, `VETRA_THEME_REDUCE_MOTION`.
- `VETRA_THEME_NOTICE_MARKDOWN`.
- Variables de paleta `VETRA_THEME_LIGHT_*` y `VETRA_THEME_DARK_*`.

Los ajustes deben guardarse desde el formulario. Editar manualmente el `.env` requiere limpiar la caché de configuración y queda bajo responsabilidad del administrador.
