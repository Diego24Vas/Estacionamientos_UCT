# Diagramas UML - Sistema de Estacionamientos UCT

Este directorio contiene los diagramas UML del Sistema de Gestión de Estacionamientos de la Universidad Católica de Temuco (UCT).

## 📁 Archivos Disponibles

### 🎨 Diagramas Visuales

1. **`UML_Estacionamientos_DrawIO.drawio`**
   - Diagrama editable para Draw.io (app.diagrams.net)
   - Modelo Entidad-Relación de la base de datos
   - 7 entidades principales con relaciones completas
   - Incluye leyenda y estadísticas del modelo

2. **`UML_Estacionamientos_DB.xml`**
   - Versión XML independiente del diagrama UML
   - Compatible con herramientas de modelado UML
   - Mismo contenido que el archivo .drawio pero en formato XML puro

3. **`diagrama_clases_drawio.xml`**
   - Diagrama de clases orientado a programación
   - Incluye interfaces, clases abstractas y patrones de diseño
   - Enfoque en la arquitectura de código del sistema

4. **`diagrama_clases.png`**
   - Imagen PNG del diagrama de clases
   - Para visualización rápida y documentación

### 📋 Documentación

5. **`MER_Sistema_Estacionamientos.md`**
   - Documentación completa del Modelo Entidad-Relación
   - Descripción detallada de todas las entidades
   - Especificaciones de campos y relaciones

6. **`MER_Documentacion_Completa.md`**
   - Documentación técnica exhaustiva
   - Incluye justificaciones de diseño
   - Consideraciones de rendimiento y escalabilidad

7. **`MER_Diagrama_Visual.md`**
   - Representación del MER en formato Mermaid
   - Diagramas visualizables en GitHub/GitLab
   - Código copiable para otras plataformas

8. **`MER_Formato_Academico.txt`**
   - Documentación en formato académico
   - Ideal para informes y presentaciones universitarias
   - Estructura formal de ingeniería de software

## 🚀 Cómo Usar los Diagramas

### Para Editar en Draw.io:
1. Ve a [app.diagrams.net](https://app.diagrams.net)
2. Selecciona "Open Existing Diagram"
3. Sube el archivo `UML_Estacionamientos_DrawIO.drawio`
4. Edita y guarda como necesites

### Para Importar en Otras Herramientas:
- Usa el archivo `UML_Estacionamientos_DB.xml` para herramientas que soporten XML
- El archivo es compatible con la mayoría de herramientas de modelado UML

### Para Documentación:
- Los archivos `.md` son perfectos para GitHub, GitLab, y editores de Markdown
- El archivo `.txt` es ideal para documentos formales y presentaciones

## 📊 Contenido del Modelo

### Entidades Principales:
1. **🚗 VEHÍCULOS** - Información completa de vehículos registrados
2. **🏢 CONFIGURACIÓN ZONAS** - Definición y configuración de zonas de estacionamiento
3. **📅 RESERVAS** - Sistema de reservas de espacios
4. **📈 HISTORIAL OCUPACIÓN** - Seguimiento de ocupación por zona
5. **🔔 EVENTOS** - Sistema de notificaciones y eventos
6. **🏷️ MARCAS VEHÍCULOS** - Catálogo de marcas de vehículos
7. **👤 USUARIOS** - Gestión de usuarios del sistema

### Relaciones Implementadas:
- **8 relaciones** entre entidades
- **5 claves foráneas** para integridad referencial
- **3 relaciones opcionales** para flexibilidad
- **5 relaciones obligatorias** para consistencia de datos

### Características Técnicas:
- **4 triggers automáticos** para mantenimiento de datos
- **4 vistas optimizadas** para consultas frecuentes
- **4 procedimientos almacenados** para operaciones complejas
- **Índices optimizados** para mejor rendimiento

## 🔧 Configuración de Base de Datos

El diagrama está diseñado para la base de datos **a2024_dvasquez** en MySQL/MariaDB.

### Características:
- **MySQL 8.0+** o **MariaDB 10.4+**
- **UTF8MB4** encoding para soporte completo de caracteres
- **InnoDB** engine para transacciones ACID
- **Constraints** y validaciones a nivel de BD

## 📝 Versiones

- **Versión 1.0** - Junio 2025
- **Base de datos:** a2024_dvasquez
- **Autor:** Sistema UCT
- **Estado:** Producción

## 🔄 Actualizaciones

Para actualizar los diagramas:

1. **Edita el archivo .drawio** en Draw.io
2. **Exporta como XML** para mantener sincronización
3. **Actualiza la documentación** en archivos .md correspondientes
4. **Verifica consistencia** con el esquema de base de datos actual

## 📞 Soporte

Para dudas sobre los diagramas o modificaciones necesarias, contacta al equipo de desarrollo del Sistema de Estacionamientos UCT.

---

*Última actualización: Junio 2025*
