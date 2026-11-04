# Contribuir a VetraTheme

Gracias por mejorar VetraTheme. Los cambios deben conservar la seguridad, estabilidad y capacidad de actualización de Pelican.

## Principios

- No modificar ni copiar archivos del núcleo de Pelican.
- Mantener el plugin como tema aditivo.
- Conservar operaciones, permisos y componentes nativos.
- No introducir SQL, migraciones, telemetría o servicios remotos sin una necesidad documentada y aprobación explícita.
- Evitar selectores globales que puedan romper Filament.
- Añadir pruebas para correcciones y comportamientos nuevos.

## Entorno

```sh
npm ci
npm run build
npm test
php tests/appearance.php
```

Para la integración se necesita una copia aislada de Pelican `1.0.0-rc.2` con dependencias instaladas:

```sh
php tests/integration.php /ruta/a/pelican-aislado
```

No ejecutes fixtures contra producción.

## Estilo de cambios

- PHP con `strict_types=1`, tipos explícitos y responsabilidades pequeñas.
- CSS acotado bajo `body:has(#vetra-ambient)` o clases propias.
- Traducciones con las mismas claves en español e inglés.
- Sin secretos, datos reales o capturas no autorizadas.
- Documentación y changelog actualizados en el mismo cambio.

## Pull request

Incluye:

- Problema resuelto.
- Alcance y archivos modificados.
- Riesgos de compatibilidad.
- Pruebas ejecutadas.
- Capturas antes/después para cambios visuales.
- Confirmación de que no se modificó el núcleo.

Los reportes de seguridad siguen [SECURITY.md](SECURITY.md), no las incidencias públicas.
