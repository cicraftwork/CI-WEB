# 📅 Agenda FEN Sustentabilidad v4.0 - Sistema de Gestión Avanzada

Sistema profesional de gestión para la Agenda de Contenidos FEN Sustentabilidad basada en el **APL II (Acuerdo de Producción Limpia II - Educación Superior Sustentable)**. 

Nueva versión 4.0 con **gestión completa de contenidos excluidos** y funcionalidades avanzadas para la administración integral de proyectos de sustentabilidad universitaria.

## 🚀 **Novedades v4.0 - Fase 1**

### ✨ **Funcionalidades Nuevas**
- **🔄 Backup Automático Silencioso**: Sin confirmaciones molestas, carga automática de última versión
- **🎨 Contenidos Excluidos Visuales**: Estado "no-incluir" con descolorido automático y tachado
- **📊 Estadísticas Precisas**: Cálculos que excluyen contenidos marcados como "no-incluir"
- **👁️ Filtro de Visibilidad**: Toggle para mostrar/ocultar contenidos excluidos
- **⚡ Herramientas Administrativas**: Botones para cambios de estado masivos
- **🎯 Gestión de Excluidos**: Control total sobre qué contenidos incluir en mediciones

### 📈 **Mejoras de Rendimiento**
- Carga automática inteligente de datos
- Auto-guardado optimizado cada 2 segundos
- Notificaciones discretas y contextuales
- Renderizado dinámico mejorado

## 🎯 **Características Principales**

### ✨ **Funcionalidades Completas v4.0**
- **Edición Total**: Cada contenido completamente editable con validación
- **Estados Avanzados**: Pendiente, En progreso, Completado, Pausado, No incluir
- **Sistema de Etiquetas**: 6 pilares del APL II con asignación múltiple
- **Filtros Inteligentes**: Búsqueda, estado, pilar, semana + visibilidad de excluidos
- **Exportación Múltiple**: JSON estructurado, Excel/CSV con todos los datos
- **Modo Presentación**: Vista profesional sin controles para reuniones
- **API REST Completa**: Backend robusto con endpoints especializados
- **Estadísticas en Tiempo Real**: Dashboard dinámico con métricas precisas
- **Auto-guardado Inteligente**: Persistencia automática con backup local
- **Historial de Cambios**: Tracking detallado de todas las modificaciones

### 🎨 **Diseño Institucional FEN**
- **Colores Oficiales**: Paleta basada en manual de marca Universidad de Chile
- **Tipografía Inter**: Universalmente compatible y profesional
- **Diseño Responsivo**: Perfecto en móvil, tablet y desktop
- **Animaciones Sutiles**: Transiciones profesionales y accesibles
- **Accesibilidad**: Contraste optimizado y semántica correcta

## 📋 **Requisitos del Sistema**

### **✅ Compatibilidad de Hosting**
- **Hostgator Compartido** ✅ (configuración actual optimizada)
- **PHP 7.4+** ✅ (disponible en la mayoría de hostings)
- **Permisos de Escritura** ✅ (para backups y persistencia)
- **Sin Base de Datos** ✅ (usa archivos JSON optimizados)

### **🛠️ Tecnologías**
- **Frontend**: HTML5 + CSS3 + JavaScript ES6
- **Backend**: PHP 7.4+ (opcional, funciona standalone)
- **Datos**: JSON estructurado (sin MySQL requerido)
- **Iconos**: Font Awesome 6.4.0 (CDN)
- **Compatible**: Todos los navegadores modernos (Chrome, Firefox, Safari, Edge)

## 🛠️ **Instalación Rápida**

### **Paso 1: Subir Archivos**
Sube estos archivos a tu directorio web:

```
/public_html/agenda-fen/
├── index.html              # Interfaz principal v4.0
├── api.php                 # API backend (opcional)
├── agenda.json             # Datos estructurados
├── .htaccess              # Configuración Apache optimizada
├── README.md              # Esta documentación
├── verificar.php          # Herramienta de diagnóstico
└── backups/               # Directorio para backups (se crea automáticamente)
```

### **Paso 2: Configurar Permisos**
En cPanel File Manager o por FTP:

```bash
chmod 755 /agenda-fen/
chmod 755 /agenda-fen/backups/
chmod 644 /agenda-fen/*.json
chmod 644 /agenda-fen/*.php
chmod 644 /agenda-fen/*.html
```

### **Paso 3: Verificar Instalación**
1. **Acceder**: `https://tudominio.com/agenda-fen/`
2. **Verificar**: `https://tudominio.com/agenda-fen/verificar.php`
3. **API (opcional)**: `https://tudominio.com/agenda-fen/api.php`

## 🔧 **Configuración Avanzada**

### **Variables de Sistema**
Edita `index.html` para personalizar:

```javascript
const CONFIG = {
    API_BASE: 'api.php',        // URL de la API
    AUTO_REFRESH: 30000,        // Auto-refresh en ms
    ANIMATION_DELAY: 100        // Delay de animaciones
};
```

### **Configuración PHP** (opcional)
Edita `api.php` para cambios backend:

```php
$jsonFile = 'agenda.json';           // Archivo principal
$historialFile = 'historial.json';  // Archivo de historial
$backupDir = 'backups/';             // Directorio de backups
```

## 📊 **Estructura de Datos v4.0**

### **Formato de Contenido Extendido**
```json
{
  "id": "1-1",
  "titulo": "Política institucional de sustentabilidad",
  "tipo": "Texto web + infografía",
  "recursos": "Política UChile existente, adaptación FEN",
  "estado": "en-progreso",             // pendiente, en-progreso, completado, pausado, no-incluir
  "etiquetas": ["gobernanza"],         // Pilares del APL II
  "comentarios": "Base fundamental",   // Notas adicionales
  "archivos": "https://link.com",     // Enlaces a documentos
  "fechaCreacion": "2025-08-21T00:00:00Z",
  "fechaModificacion": "2025-08-21T15:30:00Z"
}
```

### **Pilares del APL II**
1. **Gobernanza y Seguimiento** - Políticas y estructura institucional
2. **Cultura Sustentable** - Programas de sensibilización y participación
3. **Academia** - Formación e investigación en sustentabilidad
4. **Gestión de Campus** - Manejo de recursos y infraestructura
5. **Vinculación con el Medio** - Impacto territorial y social
6. **Responsabilidad Social** - Inclusión y diversidad

## 🔌 **API REST Completa**

### **Endpoints Principales**

#### **📥 Consultar Datos**
```bash
GET /api.php?action=agenda              # Agenda completa
GET /api.php?action=estadisticas        # Estadísticas con exclusiones
GET /api.php?action=semana&numero=1     # Semana específica
GET /api.php?action=contenido&id=1-1    # Contenido individual
GET /api.php?action=historial           # Historial de cambios
```

#### **🔍 Filtrar y Buscar**
```bash
GET /api.php?action=filtrar&busqueda=politica&estado=completado&etiqueta=gobernanza&semana=1&incluir_excluidos=false
```

#### **📤 Exportar Datos**
```bash
GET /api.php?action=exportar&formato=json    # Exportar JSON completo
GET /api.php?action=exportar&formato=csv     # Exportar Excel/CSV
```

#### **✏️ Modificar Datos**
```bash
PUT /api.php?action=agenda              # Actualizar agenda completa
PUT /api.php?action=contenido           # Actualizar contenido específico
POST /api.php?action=contenido          # Crear nuevo contenido
DELETE /api.php?action=contenido&id=1-1 # Eliminar contenido
```

#### **⚡ Funciones Avanzadas v4.0**
```bash
PUT /api.php?action=estado-masivo       # Cambiar estado de múltiples contenidos
GET /api.php?action=reporte             # Generar reporte completo con exclusiones
POST /api.php?action=backup             # Crear backup manual
```

### **Respuesta Estándar API**
```json
{
  "success": true,
  "data": { /* datos solicitados */ },
  "timestamp": "2025-08-21T15:30:00Z",
  "version": "4.0"
}
```

## 💾 **Sistema de Backup v4.0**

### **🔄 Backups Automáticos Silenciosos**
- **Carga automática** de última versión sin confirmaciones
- **Almacenamiento local** con localStorage del navegador
- **Validez**: 24 horas de persistencia automática
- **Sincronización**: Auto-guardado cada 2 segundos tras cambios

### **📁 Backups del Servidor** (con API)
- **Creación automática** antes de cada modificación importante
- **Almacenamiento**: `/backups/` con timestamp detallado
- **Formato**: `agenda_backup_2025-08-21_15-30-00.json`
- **Limpieza**: Mantiene solo los últimos 10 backups automáticamente

### **🛠️ Gestión Manual**
```bash
GET /api.php?action=backup              # Crear backup manual
POST /api.php?action=restaurar          # Restaurar desde backup específico
```

## 🎯 **Guía de Uso v4.0**

### **👥 Para Administradores**

#### **Gestión de Contenidos**
1. **Editar**: Click directo en cualquier campo o botón "Editar"
2. **Estados**: Usar selectores de estado, incluido "No incluir"
3. **Etiquetas**: Seleccionar múltiples pilares del APL II
4. **Exclusiones**: Marcar como "no-incluir" para excluir de estadísticas

#### **Filtros y Búsqueda**
1. **Búsqueda**: Usar barra de búsqueda con términos específicos
2. **Filtros**: Estados, pilares, semanas específicas
3. **Visibilidad**: Toggle "Mostrar excluidos" para control total
4. **Limpiar**: Botón "Limpiar filtros" para vista completa

#### **Herramientas Administrativas v4.0**
1. **Cambios Masivos**: Botones para marcar múltiples como "En Progreso", "Completado" o "Excluido"
2. **Exportación**: Múltiples formatos con datos filtrados
3. **Importación**: Cargar agenda completa desde JSON
4. **Estadísticas**: Dashboard en tiempo real con exclusiones

### **👔 Para Presentaciones**
1. **Modo Presentación**: Botón para ocultar controles de edición
2. **Vista Limpia**: Interfaz optimizada para reuniones y exposiciones
3. **Toggle Rápido**: Fácil cambio entre modo edición y presentación

### **📊 Para Reportes y Análisis**
1. **Estadísticas Automáticas**: Dashboard con métricas en tiempo real
2. **Exportación Excel**: Para análisis externos con herramientas especializadas
3. **Filtros de Reporte**: Generar reportes de pilares o estados específicos
4. **Historial**: Seguimiento detallado de cambios y modificaciones

## 🔍 **Solución de Problemas**

### **⚠️ Problemas Comunes y Soluciones**

#### **🚫 No carga la agenda**
- ✅ **Verificar permisos**: 644 para JSON, 755 para directorios
- ✅ **Comprobar archivo**: Que `agenda.json` existe y es válido JSON
- ✅ **Revisar logs**: PHP error log en cPanel → Error Logs
- ✅ **Verificar JavaScript**: F12 → Console para errores de navegador

#### **💾 No se guardan cambios**
- ✅ **Permisos de escritura**: Verificar que el directorio es escribible
- ✅ **Espacio en disco**: Comprobar espacio disponible en hosting
- ✅ **PHP habilitado**: Verificar que PHP puede escribir archivos
- ✅ **Backup local**: Cambios se guardan en localStorage como respaldo

#### **🔌 API no responde**
- ✅ **PHP activo**: Verificar que PHP está habilitado en el hosting
- ✅ **Sintaxis correcta**: Comprobar sintaxis de `api.php`
- ✅ **Logs de error**: Revisar logs de Apache/PHP en cPanel
- ✅ **Permisos API**: Verificar que api.php tiene permisos 644

#### **🔍 Filtros no funcionan**
- ✅ **JavaScript habilitado**: Verificar en configuración del navegador
- ✅ **Consola de desarrollo**: F12 → Console para errores
- ✅ **Formato JSON**: Verificar que datos tienen formato correcto
- ✅ **Cache del navegador**: Forzar recarga con Ctrl+F5

### **📋 Herramientas de Diagnóstico**

#### **🔧 Verificación del Sistema**
Usa `verificar.php` para diagnóstico completo:
```
https://tudominio.com/agenda-fen/verificar.php
```

Verifica:
- Versión PHP y extensiones necesarias
- Permisos de archivos y directorios
- Funcionamiento de la API
- Validez de archivos JSON
- Configuración del servidor

#### **📊 Logs y Debugging**
- **PHP Errors**: cPanel → Error Logs
- **JavaScript**: F12 → Console en navegador
- **API Response**: Network tab en Developer Tools
- **Backup Status**: Consola del navegador para mensajes de auto-guardado

## 🚀 **Optimizaciones y Rendimiento**

### **⚡ Para Mejor Rendimiento**
1. **Gzip habilitado** en `.htaccess` (incluido)
2. **Cache de archivos** estáticos configurado
3. **Compresión automática** de CSS/JS
4. **CDN para Font Awesome** (ya configurado)
5. **Lazy loading** de imágenes grandes

### **🔍 Para Mejor SEO** (si es público)
1. **Meta tags** apropiados incluidos
2. **Structured data** JSON-LD para contenido
3. **URLs amigables** configurables en `.htaccess`
4. **Sitemap XML** generación automática (próxima versión)

### **📊 Para Analytics** (opcional)
1. **Google Analytics** fácil integración
2. **Tracking de eventos** de interacción
3. **Métricas de uso** personalizadas
4. **Reportes de rendimiento** automáticos

## 🛣️ **Hoja de Ruta - Fases de Desarrollo**

### **✅ Fase 1 - COMPLETADA**
- [x] Backup automático silencioso
- [x] Gestión visual de contenidos excluidos
- [x] Estadísticas precisas excluyendo "no-incluir"
- [x] Filtro de visibilidad de excluidos
- [x] Herramientas administrativas básicas

### **🔄 Fase 2 - EN PLANIFICACIÓN**
- [ ] **Modal extendido**: Crear semanas nuevas desde interfaz
- [ ] **Enlaces múltiples**: Sistema dinámico de URLs con +/-
- [ ] **Plantillas de contenido**: Tipos predefinidos
- [ ] **Duplicar semanas**: Clonar semana completa con contenidos

### **🔮 Fase 3 - FUTURO**
- [ ] **Drag & Drop**: Reordenar contenidos entre semanas
- [ ] **Colaboración**: Múltiples usuarios simultáneos
- [ ] **Notificaciones**: Sistema de alertas y recordatorios
- [ ] **Integración Calendar**: Sincronización con Google Calendar
- [ ] **Reportes avanzados**: Dashboard con gráficos interactivos

## 📞 **Soporte y Contacto**

### **📚 Recursos de Ayuda**
- **Manual APL II**: [Documento oficial adjunto](docs/APL-II-Educacion-Superior-Sustentable.pdf)
- **Brand Guidelines FEN**: Colores y tipografías institucionales
- **PHP Documentation**: [php.net](https://php.net/manual/)
- **Hostgator Support**: [support.hostgator.com](https://support.hostgator.com)

### **🐛 Reportar Problemas**
Para soporte técnico específico:

1. **Descripción detallada** del problema encontrado
2. **Pasos para reproducir** el error
3. **Logs de error** relevantes (PHP, JavaScript)
4. **Screenshots** del problema
5. **Información del entorno**: Navegador, versión PHP, hosting

### **💡 Solicitar Funciones**
Para nuevas funcionalidades:

1. **Descripción clara** de la función deseada
2. **Caso de uso** específico
3. **Prioridad** para tu institución
4. **Compatibilidad** con APL II

## 📊 **Estadísticas del Proyecto v4.0**

### **📈 Métricas del Sistema**
- **47 contenidos** estructurados según APL II
- **8 semanas** de implementación programada
- **6 pilares** del APL II cubiertos completamente
- **5 estados** de progreso diferentes
- **100% responsive** en todos los dispositivos
- **0 dependencias** de bases de datos externas

### **🎯 Cobertura APL II**
- **100% Gobernanza** y Seguimiento
- **100% Cultura** Sustentable 
- **100% Academia** y Formación
- **100% Gestión** de Campus
- **100% Vinculación** con el Medio
- **100% Responsabilidad** Social

## 📝 **Changelog v4.0**

### **🆕 Versión 4.0 - Fase 1** (Agosto 2025)
- ✅ **NEW**: Gestión completa de contenidos excluidos con estado "no-incluir"
- ✅ **NEW**: Backup automático silencioso sin confirmaciones molestas
- ✅ **NEW**: Visualización descolorida de contenidos excluidos
- ✅ **NEW**: Estadísticas precisas que excluyen contenidos marcados
- ✅ **NEW**: Filtro toggle para mostrar/ocultar excluidos
- ✅ **NEW**: Herramientas administrativas para cambios masivos
- ✅ **IMPROVED**: Rendimiento de auto-guardado optimizado
- ✅ **IMPROVED**: Notificaciones más discretas y contextuales
- ✅ **IMPROVED**: Interfaz de usuario más intuitiva
- ✅ **FIXED**: Problemas de persistencia de datos
- ✅ **FIXED**: Cálculos incorrectos en estadísticas

### **📋 Versión 3.0** (Julio 2025)
- ✅ Sistema completo de gestión avanzada
- ✅ API REST funcional y documentada
- ✅ 47 contenidos estructurados según APL II
- ✅ Filtros y búsqueda avanzada
- ✅ Exportación múltiple formato
- ✅ Sistema de backup automático básico
- ✅ Diseño responsivo profesional
- ✅ Compatible con Hostgator compartido

### **📝 Versión 1.0** (Base Original)
- ✅ Agenda básica con acordeones
- ✅ Datos estáticos en JSON
- ✅ Interfaz simple de lectura

## 📄 **Licencia y Uso**

### **🏛️ Uso Institucional Autorizado**
Este sistema está desarrollado específicamente para la **Facultad de Economía y Negocios (FEN)** de la **Universidad de Chile** como parte del cumplimiento del **APL II - Educación Superior Sustentable**.

### **✅ Uso Permitido**
- Personal académico y administrativo FEN
- Proyectos relacionados con sustentabilidad institucional
- Cumplimiento de compromisos APL II
- Adaptación para otras instituciones de educación superior
- Uso educativo y de investigación

### **📋 Condiciones**
- Mantener créditos de desarrollo
- Uso responsable de datos institucionales
- Cumplimiento de normativas de privacidad
- Reportar mejoras significativas a la comunidad

---

## 🎯 **¡Sistema v4.0 Listo para Implementar!**

Sube los archivos a tu Hostgator y comienza a gestionar tu agenda de sustentabilidad de manera profesional con las nuevas funcionalidades de **gestión de excluidos**, **backup automático silencioso** y **estadísticas precisas**.

### **🚀 Inicio Rápido**
1. **Descarga** todos los archivos del sistema
2. **Sube** a tu hosting en `/public_html/agenda-fen/`
3. **Configura** permisos según instrucciones
4. **Verifica** con `verificar.php`
5. **¡Comienza** a gestionar tu agenda de sustentabilidad!

---

**📧 Para soporte técnico o consultas:** Contacta al equipo de desarrollo con los detalles de tu configuración y el problema específico.

**🌱 ¡Contribuye al futuro sustentable de la educación superior!**