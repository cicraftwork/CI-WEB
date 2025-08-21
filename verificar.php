<?php
/**
 * Script de Verificación del Sistema
 * Agenda FEN Sustentabilidad - Versión Avanzada
 * 
 * Este script verifica que todos los componentes del sistema
 * estén funcionando correctamente en el hosting.
 * 
 * Uso: Acceder a https://tudominio.com/agenda-fen/verificar.php
 * 
 * IMPORTANTE: Eliminar este archivo en producción por seguridad
 */

// Configuración
$APP_NAME = "Agenda FEN Sustentabilidad";
$VERSION = "2.0";
$FILES_TO_CHECK = [
    'index.html' => 'Interfaz principal',
    'api.php' => 'API REST',
    'agenda.json' => 'Datos de la agenda',
    '.htaccess' => 'Configuración Apache'
];

$DIRECTORIES_TO_CHECK = [
    'backups' => 'Directorio de backups'
];

// Función para verificar PHP
function checkPHPVersion() {
    $version = phpversion();
    $required = '7.4.0';
    
    return [
        'version' => $version,
        'required' => $required,
        'compatible' => version_compare($version, $required, '>='),
        'message' => version_compare($version, $required, '>=') 
            ? "✅ PHP $version (Compatible)" 
            : "❌ PHP $version (Requiere PHP $required o superior)"
    ];
}

// Función para verificar extensiones PHP
function checkPHPExtensions() {
    $required = ['json', 'curl', 'fileinfo'];
    $results = [];
    
    foreach ($required as $ext) {
        $loaded = extension_loaded($ext);
        $results[$ext] = [
            'loaded' => $loaded,
            'message' => $loaded ? "✅ $ext" : "❌ $ext (No disponible)"
        ];
    }
    
    return $results;
}

// Función para verificar archivos
function checkFiles($files) {
    $results = [];
    
    foreach ($files as $file => $description) {
        $exists = file_exists($file);
        $readable = $exists ? is_readable($file) : false;
        $size = $exists ? filesize($file) : 0;
        
        $results[$file] = [
            'exists' => $exists,
            'readable' => $readable,
            'size' => $size,
            'description' => $description,
            'message' => $exists 
                ? ($readable ? "✅ $file (" . formatBytes($size) . ")" : "⚠️ $file (No se puede leer)")
                : "❌ $file (No encontrado)"
        ];
    }
    
    return $results;
}

// Función para verificar directorios
function checkDirectories($directories) {
    $results = [];
    
    foreach ($directories as $dir => $description) {
        $exists = is_dir($dir);
        $writable = $exists ? is_writable($dir) : false;
        
        if (!$exists) {
            // Intentar crear el directorio
            $created = @mkdir($dir, 0755, true);
            $exists = $created;
            $writable = $created ? is_writable($dir) : false;
        }
        
        $results[$dir] = [
            'exists' => $exists,
            'writable' => $writable,
            'description' => $description,
            'message' => $exists 
                ? ($writable ? "✅ $dir (Escribible)" : "⚠️ $dir (No escribible)")
                : "❌ $dir (No se pudo crear)"
        ];
    }
    
    return $results;
}

// Función para verificar la API
function checkAPI() {
    $apiFile = 'api.php';
    
    if (!file_exists($apiFile)) {
        return [
            'available' => false,
            'message' => '❌ API no encontrada'
        ];
    }
    
    // Verificar sintaxis PHP
    $output = [];
    $return_var = 0;
    exec("php -l $apiFile 2>&1", $output, $return_var);
    
    $syntaxOK = $return_var === 0;
    
    // Intentar hacer una petición de prueba
    $testURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . 
               dirname($_SERVER['REQUEST_URI']) . '/api.php?action=agenda';
    
    $apiResponse = @file_get_contents($testURL);
    $apiWorking = $apiResponse !== false;
    
    return [
        'available' => file_exists($apiFile),
        'syntax_ok' => $syntaxOK,
        'responding' => $apiWorking,
        'response_size' => $apiWorking ? strlen($apiResponse) : 0,
        'message' => $syntaxOK && $apiWorking 
            ? '✅ API funcionando correctamente' 
            : '⚠️ API con problemas: ' . ($syntaxOK ? 'Sintaxis OK, ' : 'Error sintaxis, ') . 
              ($apiWorking ? 'Responde OK' : 'No responde')
    ];
}

// Función para verificar configuración del servidor
function checkServerConfig() {
    $results = [
        'mod_rewrite' => [
            'available' => function_exists('apache_get_modules') ? 
                in_array('mod_rewrite', apache_get_modules()) : 'Desconocido',
            'message' => function_exists('apache_get_modules') ? 
                (in_array('mod_rewrite', apache_get_modules()) ? '✅ mod_rewrite disponible' : '❌ mod_rewrite no disponible')
                : '⚠️ mod_rewrite - Estado desconocido'
        ],
        'allow_url_fopen' => [
            'available' => ini_get('allow_url_fopen'),
            'message' => ini_get('allow_url_fopen') ? '✅ allow_url_fopen habilitado' : '⚠️ allow_url_fopen deshabilitado'
        ],
        'file_uploads' => [
            'available' => ini_get('file_uploads'),
            'message' => ini_get('file_uploads') ? '✅ file_uploads habilitado' : '❌ file_uploads deshabilitado'
        ],
        'max_execution_time' => [
            'value' => ini_get('max_execution_time'),
            'message' => '⏱️ max_execution_time: ' . ini_get('max_execution_time') . 's'
        ],
        'memory_limit' => [
            'value' => ini_get('memory_limit'),
            'message' => '💾 memory_limit: ' . ini_get('memory_limit')
        ]
    ];
    
    return $results;
}

// Función para formatear bytes
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

// Función para verificar JSON
function checkJSONData() {
    $jsonFile = 'agenda.json';
    
    if (!file_exists($jsonFile)) {
        return [
            'valid' => false,
            'message' => '❌ Archivo JSON no encontrado'
        ];
    }
    
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    $jsonError = json_last_error();
    
    if ($jsonError !== JSON_ERROR_NONE) {
        return [
            'valid' => false,
            'error' => $jsonError,
            'message' => '❌ JSON inválido: ' . json_last_error_msg()
        ];
    }
    
    // Verificar estructura básica
    $hasTitle = isset($data['titulo']);
    $hasPeriod = isset($data['periodo']);
    $hasWeeks = isset($data['semanas']) && is_array($data['semanas']);
    $weekCount = $hasWeeks ? count($data['semanas']) : 0;
    $contentCount = 0;
    
    if ($hasWeeks) {
        foreach ($data['semanas'] as $semana) {
            if (isset($semana['contenidos']) && is_array($semana['contenidos'])) {
                $contentCount += count($semana['contenidos']);
            }
        }
    }
    
    return [
        'valid' => $jsonError === JSON_ERROR_NONE,
        'has_structure' => $hasTitle && $hasPeriod && $hasWeeks,
        'weeks' => $weekCount,
        'contents' => $contentCount,
        'size' => formatBytes(strlen($content)),
        'message' => $hasTitle && $hasPeriod && $hasWeeks 
            ? "✅ JSON válido: $weekCount semanas, $contentCount contenidos"
            : '⚠️ JSON válido pero estructura incompleta'
    ];
}

// Ejecutar verificaciones
$checks = [
    'php_version' => checkPHPVersion(),
    'php_extensions' => checkPHPExtensions(),
    'files' => checkFiles($FILES_TO_CHECK),
    'directories' => checkDirectories($DIRECTORIES_TO_CHECK),
    'json_data' => checkJSONData(),
    'api' => checkAPI(),
    'server_config' => checkServerConfig()
];

// Determinar estado general
$allGood = true;
$warnings = 0;
$errors = 0;

// Contar problemas
if (!$checks['php_version']['compatible']) $errors++;
foreach ($checks['php_extensions'] as $ext) {
    if (!$ext['loaded']) $warnings++;
}
foreach ($checks['files'] as $file) {
    if (!$file['exists']) $errors++;
    elseif (!$file['readable']) $warnings++;
}
foreach ($checks['directories'] as $dir) {
    if (!$dir['exists']) $errors++;
    elseif (!$dir['writable']) $warnings++;
}
if (!$checks['json_data']['valid']) $errors++;
if (!$checks['api']['responding']) $warnings++;

$allGood = $errors === 0;

// HTML Output
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación del Sistema - <?php echo $APP_NAME; ?></title>
    <style>
        :root {
            --fen-azul-principal: #1E3A8A;
            --fen-azul-secundario: #3B82F6;
            --fen-gris-claro: #F4F4F4;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, var(--fen-azul-principal), var(--fen-azul-secundario));
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--fen-gris-claro);
        }
        
        .header h1 {
            color: var(--fen-azul-principal);
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .status {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }
        
        .status.success { background: #D1FAE5; color: #065F46; }
        .status.warning { background: #FEF3C7; color: #92400E; }
        .status.error { background: #FEE2E2; color: #991B1B; }
        
        .section {
            margin-bottom: 30px;
            background: var(--fen-gris-claro);
            border-radius: 15px;
            padding: 25px;
        }
        
        .section h3 {
            color: var(--fen-azul-principal);
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .check-item {
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
            font-family: 'Courier New', monospace;
        }
        
        .check-item:last-child { border-bottom: none; }
        
        .details {
            font-size: 0.9em;
            color: #6B7280;
            margin-top: 5px;
        }
        
        .code {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin: 10px 0;
            overflow-x: auto;
        }
        
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--fen-gris-claro);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background: var(--fen-azul-principal);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: var(--fen-azul-secundario);
            transform: translateY(-2px);
        }
        
        .warning-box {
            background: #FEF3C7;
            border: 2px solid #F59E0B;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .warning-box h4 {
            color: #92400E;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo $APP_NAME; ?></h1>
            <p>Verificación del Sistema - Versión <?php echo $VERSION; ?></p>
            <p><strong>Servidor:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>

        <!-- Estado general -->
        <div class="status <?php echo $allGood ? 'success' : ($errors > 0 ? 'error' : 'warning'); ?>">
            <?php if ($allGood): ?>
                ✅ Sistema funcionando correctamente
            <?php elseif ($errors > 0): ?>
                ❌ Sistema con errores críticos (<?php echo $errors; ?> errores, <?php echo $warnings; ?> advertencias)
            <?php else: ?>
                ⚠️ Sistema funcional con advertencias (<?php echo $warnings; ?> advertencias)
            <?php endif; ?>
        </div>

        <!-- Verificación PHP -->
        <div class="section">
            <h3>🐘 Entorno PHP</h3>
            <div class="check-item"><?php echo $checks['php_version']['message']; ?></div>
            
            <h4 style="margin-top: 20px; color: var(--fen-azul-secundario);">Extensiones PHP:</h4>
            <?php foreach ($checks['php_extensions'] as $ext => $result): ?>
                <div class="check-item"><?php echo $result['message']; ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Verificación de archivos -->
        <div class="section">
            <h3>📁 Archivos del Sistema</h3>
            <?php foreach ($checks['files'] as $file => $result): ?>
                <div class="check-item">
                    <?php echo $result['message']; ?>
                    <div class="details"><?php echo $result['description']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Verificación de directorios -->
        <div class="section">
            <h3>📂 Directorios</h3>
            <?php foreach ($checks['directories'] as $dir => $result): ?>
                <div class="check-item">
                    <?php echo $result['message']; ?>
                    <div class="details"><?php echo $result['description']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Verificación JSON -->
        <div class="section">
            <h3>📊 Datos de la Agenda</h3>
            <div class="check-item">
                <?php echo $checks['json_data']['message']; ?>
                <?php if ($checks['json_data']['valid']): ?>
                    <div class="details">
                        Tamaño: <?php echo $checks['json_data']['size']; ?> | 
                        Semanas: <?php echo $checks['json_data']['weeks']; ?> | 
                        Contenidos: <?php echo $checks['json_data']['contents']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verificación API -->
        <div class="section">
            <h3>🔌 API REST</h3>
            <div class="check-item">
                <?php echo $checks['api']['message']; ?>
                <?php if ($checks['api']['responding']): ?>
                    <div class="details">
                        Tamaño respuesta: <?php echo formatBytes($checks['api']['response_size']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Configuración del servidor -->
        <div class="section">
            <h3>⚙️ Configuración del Servidor</h3>
            <?php foreach ($checks['server_config'] as $config => $result): ?>
                <div class="check-item"><?php echo $result['message']; ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Información adicional -->
        <div class="section">
            <h3>ℹ️ Información del Sistema</h3>
            <div class="code">
                <strong>Sistema Operativo:</strong> <?php echo PHP_OS; ?><br>
                <strong>Versión PHP:</strong> <?php echo phpversion(); ?><br>
                <strong>Directorio actual:</strong> <?php echo __DIR__; ?><br>
                <strong>Usuario PHP:</strong> <?php echo get_current_user(); ?><br>
                <strong>Memoria disponible:</strong> <?php echo ini_get('memory_limit'); ?><br>
                <strong>Tiempo ejecución:</strong> <?php echo ini_get('max_execution_time'); ?>s
            </div>
        </div>

        <?php if (!$allGood): ?>
        <div class="warning-box">
            <h4>⚠️ Problemas Detectados</h4>
            <p>Se han encontrado problemas que pueden afectar el funcionamiento del sistema:</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <?php if ($errors > 0): ?>
                    <li><strong><?php echo $errors; ?> errores críticos</strong> - Requieren solución inmediata</li>
                <?php endif; ?>
                <?php if ($warnings > 0): ?>
                    <li><strong><?php echo $warnings; ?> advertencias</strong> - El sistema puede funcionar con limitaciones</li>
                <?php endif; ?>
            </ul>
            <p><strong>Recomendación:</strong> Revisar la documentación o contactar soporte técnico.</p>
        </div>
        <?php endif; ?>

        <!-- Acciones -->
        <div class="actions">
            <a href="index.html" class="btn">🏠 Ir a la Agenda</a>
            <a href="api.php?action=estadisticas" class="btn">📊 Probar API</a>
            <a href="?refresh=1" class="btn">🔄 Verificar Nuevamente</a>
        </div>

        <div style="text-align: center; margin-top: 30px; font-size: 0.9em; color: #6B7280;">
            <p><strong>IMPORTANTE:</strong> Eliminar este archivo (verificar.php) en producción por seguridad.</p>
        </div>
    </div>
</body>
</html>