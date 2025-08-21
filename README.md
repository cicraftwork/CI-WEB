# 📅 Agenda FEN Sustentabilidad - Versión Avanzada

Sistema de gestión profesional para la Agenda de Contenidos FEN Sustentabilidad basada en el APL II (Acuerdo de Producción Limpia II - Educación Superior Sustentable).

## 🚀 Características Principales

### ✨ **Funcionalidades Avanzadas**
- **Edición Completa**: Cada contenido es completamente editable
- **Estados de Progreso**: Pendiente, En progreso, Completado, Pausado, No incluir
- **Sistema de Etiquetas**: 6 pilares del APL II
- **Filtros Avanzados**: Búsqueda, estado, pilar, semana
- **Exportación**: JSON, Excel/CSV
- **Modo Presentación**: Vista limpia para reuniones
- **API REST**: Sistema backend robusto
- **Estadísticas en Tiempo Real**: Dashboard con métricas
- **Auto-guardado**: Persistencia automática
- **Historial de Cambios**: Tracking básico de modificaciones

### 🎨 **Diseño Institucional**
- **Colores FEN**: Basado en manual de marca oficial
- **Tipografía**: Inter (universalmente compatible)
- **Responsive**: Perfecto en móvil y desktop
- **Animaciones**: Sutiles y profesionales
- **Accesibilidad**: Contraste y semántica optimizados

## 📋 Requisitos del Sistema

### **Hosting Compatibilidad**
- ✅ **Hostgator compartido** (tu configuración actual)
- ✅ **PHP 7.4+** (disponible en Hostgator)
- ✅ **Escritura de archivos** (para backups y datos)
- ✅ **Sin base de datos requerida** (usa archivos JSON)

### **Tecnologías**
- **Frontend**: HTML5 + CSS3 + JavaScript ES6
- **Backend**: PHP 7.4+
- **Datos**: JSON (sin MySQL necesario)
- **Iconos**: Font Awesome 6.4.0
- **Compatible**: Todos los navegadores modernos

## 🛠️ Instalación

### **Paso 1: Subir Archivos**
Sube estos archivos a tu directorio web en Hostgator:

```
/public_html/agenda-fen/
├── index.html              # Interfaz principal
├── api.php                 # API backend
├── agenda.json             # Datos de la agenda
├── .htaccess              # Configuración Apache
├── README.md              # Este archivo
└── backups/               # Directorio para backups (se crea automáticamente)
```

### **Paso 2: Configurar Permisos**
Asegúrate de que estos directorios tengan permisos de escritura (755 o 775):

```bash
# En cPanel File Manager o por FTP:
chmod 755 /agenda-fen/
chmod 755 /agenda-fen/backups/
chmod 644 /agenda-fen/*.json
chmod 644 /agenda-fen/*.php
chmod 644 /agenda-fen/*.html
```

### **Paso 3: Acceder al Sistema**
- **URL Principal**: `https://tudominio.com/agenda-fen/`
- **API REST**: `https://tudominio.com/agenda-fen/api.php`

## 🔧 Configuración

### **Variables de Configuración**
Edita `api.php` si necesitas cambiar configuraciones:

```php
// Archivos de datos
$jsonFile = 'agenda.json';           // Archivo principal
$historialFile = 'historial.json';  // Archivo de historial
$backupDir = 'backups/';             // Directorio de backups
```

### **Configuración del Frontend**
Edita `index.html` si necesitas ajustar:

```javascript
const CONFIG = {
    API_BASE: 'api.php',        // URL de la API
    AUTO_REFRESH: 30000,        // Auto-refresh en ms (30 seg)
    ANIMATION_DELAY: 100        // Delay de animaciones
};
```

## 📊 Estructura de Datos

### **Formato de Contenido**
```json
{
  "id": "1-1",
  "titulo": "Política institucional de sustentabilidad",
  "tipo": "Texto web + infografía",
  "recursos": "Política UChile existente, adaptación FEN",
  "estado": "",                    // pendiente, en-progreso, completado, pausado, no-incluir
  "etiquetas": [],                 // gobernanza, cultura, academia, campus, vinculacion, responsabilidad
  "comentarios": "",               // Notas adicionales
  "archivos": "",                  // Links a documentos
  "fechaCreacion": "2025-08-21T00:00:00Z",
  "fechaModificacion": "2025-08-21T00:00:00Z"
}
```

### **Pilares del APL II**
1. **Gobernanza y Seguimiento**
2. **Cultura Sustentable**
3. **Academia**
4. **Gestión de Campus**
5. **Vinculación con el Medio**
6. **Responsabilidad Social**

## 🔌 API REST

### **Endpoints Disponibles**

#### **Obtener Datos**
```bash
GET /api.php?action=agenda              # Agenda completa
GET /api.php?action=estadisticas        # Estadísticas generales
GET /api.php?action=semana&numero=1     # Semana específica
GET /api.php?action=contenido&id=1-1    # Contenido específico
GET /api.php?action=historial           # Historial de cambios
```

#### **Filtrar Contenidos**
```bash
GET /api.php?action=filtrar&busqueda=politica&estado=completado&etiqueta=gobernanza&semana=1
```

#### **Exportar Datos**
```bash
GET /api.php?action=exportar&formato=json    # Exportar JSON
GET /api.php?action=exportar&formato=csv     # Exportar CSV/Excel
```

#### **Actualizar Datos**
```bash
PUT /api.php?action=agenda              # Actualizar agenda completa
PUT /api.php?action=contenido           # Actualizar contenido específico
```

#### **Crear Contenido**
```bash
POST /api.php?action=contenido          # Crear nuevo contenido
```

#### **Eliminar Contenido**
```bash
DELETE /api.php?action=contenido&id=1-1 # Eliminar contenido
```

### **Respuestas de la API**
```json
{
  "success": true,
  "data": { /* datos solicitados */ },
  "timestamp": "2025-08-21T10:30:00Z"
}
```

## 💾 Sistema de Backup

### **Backups Automáticos**
- Se crean automáticamente antes de cada modificación
- Se almacenan en `/backups/` con timestamp
- Formato: `agenda_backup_2025-08-21_14-30-15.json`

### **Backup Manual**
```bash
GET /api.php?action=backup              # Crear backup manual
```

### **Restaurar Backup**
```bash
POST /api.php?action=restaurar          # Restaurar desde backup
```

## 🎯 Uso del Sistema

### **Para Administradores**
1. **Editar Contenidos**: Click directo en cualquier campo
2. **Cambiar Estados**: Usar selectores de estado
3. **Agregar Etiquetas**: Seleccionar pilares del APL
4. **Filtrar**: Usar barra de búsqueda y filtros
5. **Exportar**: Botón "Exportar" → seleccionar formato

### **Para Presentaciones**
1. **Modo Presentación**: Botón "Modo presentación"
2. **Oculta controles** de edición
3. **Vista limpia** para reuniones
4. **Fácil toggle** para volver a edición

### **Para Reportes**
1. **Estadísticas**: Dashboard automático en home
2. **Exportar Excel**: Para análisis externos
3. **Filtros**: Para reportes específicos
4. **Historial**: Para seguimiento de cambios

## 🔍 Solución de Problemas

### **Problemas Comunes**

#### **No carga la agenda**
- ✅ Verificar permisos de archivos (644 para JSON, 755 para directorios)
- ✅ Comprobar que `agenda.json` existe y es válido
- ✅ Revisar PHP error log en cPanel

#### **No se guardan cambios**
- ✅ Verificar permisos de escritura en directorio
- ✅ Comprobar espacio disponible en hosting
- ✅ Verificar que PHP puede escribir archivos

#### **API no responde**
- ✅ Verificar que PHP está habilitado
- ✅ Comprobar sintaxis de `api.php`
- ✅ Revisar logs de Apache/PHP

#### **Filtros no funcionan**
- ✅ Verificar JavaScript habilitado en navegador
- ✅ Comprobar consola de desarrollo (F12)
- ✅ Verificar formato de datos en JSON

### **Logs y Debugging**
- **PHP Errors**: cPanel → Error Logs
- **JavaScript**: F12 → Console en navegador
- **API Response**: Network tab en Developer Tools

## 🚀 Optimizaciones

### **Para Mejor Rendimiento**
1. **Activar Gzip** en `.htaccess`
2. **Cache de archivos** estáticos
3. **Minificar CSS/JS** para producción
4. **CDN** para Font Awesome

### **Para Mejor SEO**
1. **Meta tags** apropiados
2. **Structured data** JSON-LD
3. **Sitemap XML** si es público
4. **Analytics** integrado

## 📞 Soporte

### **Recursos de Ayuda**
- **Manual APL II**: Documento oficial adjunto
- **Documentación FEN**: Brand guidelines institucionales
- **PHP Documentation**: [php.net](https://php.net)
- **Hostgator Support**: [support.hostgator.com](https://support.hostgator.com)

### **Contacto Técnico**
Para soporte técnico específico del sistema:
1. **Issues**: Crear ticket con descripción detallada
2. **Logs**: Incluir logs de error relevantes
3. **Screenshots**: Capturas de pantalla del problema
4. **Environment**: Especificar navegador y versión PHP

## 📝 Changelog

### **Versión 2.0** (Agosto 2025)
- ✅ Sistema completo de gestión avanzada
- ✅ API REST funcional
- ✅ 46 contenidos estructurados
- ✅ Filtros y búsqueda avanzada
- ✅ Exportación múltiple formato
- ✅ Sistema de backup automático
- ✅ Diseño responsivo profesional
- ✅ Compatible con Hostgator compartido

### **Versión 1.0** (Base Original)
- ✅ Agenda básica con acordeones
- ✅ Datos estáticos en JSON
- ✅ Interfaz simple de lectura

## 📄 Licencia

Este sistema está desarrollado específicamente para la **Facultad de Economía y Negocios (FEN)** de la **Universidad de Chile** como parte del cumplimiento del **APL II - Educación Superior Sustentable**.

**Uso autorizado** para:
- Personal académico y administrativo FEN
- Proyectos relacionados con sustentabilidad institucional
- Cumplimiento de compromisos APL II

---

**🎯 ¡Sistema listo para implementar!** 

Sube los archivos a tu Hostgator y comienza a gestionar tu agenda de sustentabilidad de manera profesional.