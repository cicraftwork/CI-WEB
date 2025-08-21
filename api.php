<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Manejar preflight requests de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Archivos de datos
$jsonFile = 'agenda.json';
$historialFile = 'historial.json';
$backupDir = 'backups/';

// Crear directorio de backups si no existe
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

/**
 * Función principal de enrutamiento
 */
function main() {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? 'agenda';
    
    switch ($method) {
        case 'GET':
            handleGet($action);
            break;
        case 'POST':
            handlePost($action);
            break;
        case 'PUT':
            handlePut($action);
            break;
        case 'DELETE':
            handleDelete($action);
            break;
        default:
            sendError('Método no permitido', 405);
    }
}

/**
 * Manejar peticiones GET
 */
function handleGet($action) {
    switch ($action) {
        case 'agenda':
            getAgenda();
            break;
        case 'estadisticas':
            getEstadisticas();
            break;
        case 'semana':
            getSemana();
            break;
        case 'contenido':
            getContenido();
            break;
        case 'historial':
            getHistorial();
            break;
        case 'exportar':
            exportarDatos();
            break;
        case 'filtrar':
            filtrarContenidos();
            break;
        default:
            sendError('Acción no válida para GET', 400);
    }
}

/**
 * Manejar peticiones POST
 */
function handlePost($action) {
    switch ($action) {
        case 'contenido':
            crearContenido();
            break;
        case 'semana':
            crearSemana();
            break;
        case 'backup':
            crearBackup();
            break;
        case 'restaurar':
            restaurarBackup();
            break;
        default:
            sendError('Acción no válida para POST', 400);
    }
}

/**
 * Manejar peticiones PUT
 */
function handlePut($action) {
    switch ($action) {
        case 'agenda':
            actualizarAgenda();
            break;
        case 'contenido':
            actualizarContenido();
            break;
        case 'semana':
            actualizarSemana();
            break;
        default:
            sendError('Acción no válida para PUT', 400);
    }
}

/**
 * Manejar peticiones DELETE
 */
function handleDelete($action) {
    switch ($action) {
        case 'contenido':
            eliminarContenido();
            break;
        case 'semana':
            eliminarSemana();
            break;
        default:
            sendError('Acción no válida para DELETE', 400);
    }
}

/**
 * Leer archivo de agenda
 */
function leerAgenda() {
    global $jsonFile;
    
    if (!file_exists($jsonFile)) {
        return [
            'error' => true,
            'mensaje' => 'Archivo de agenda no encontrado'
        ];
    }
    
    $jsonContent = file_get_contents($jsonFile);
    $agenda = json_decode($jsonContent, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => true,
            'mensaje' => 'Error al decodificar JSON: ' . json_last_error_msg()
        ];
    }
    
    return [
        'error' => false,
        'data' => $agenda
    ];
}

/**
 * Guardar agenda
 */
function guardarAgenda($agenda) {
    global $jsonFile;
    
    // Crear backup antes de guardar
    crearBackupSilencioso();
    
    $jsonData = json_encode($agenda, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($jsonFile, $jsonData) === false) {
        return [
            'error' => true,
            'mensaje' => 'Error al guardar la agenda'
        ];
    }
    
    // Registrar en historial
    registrarCambio('agenda_actualizada', $agenda);
    
    return [
        'error' => false,
        'mensaje' => 'Agenda guardada correctamente'
    ];
}

/**
 * Obtener agenda completa
 */
function getAgenda() {
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
    } else {
        sendSuccess($resultado['data']);
    }
}

/**
 * Obtener estadísticas
 */
function getEstadisticas() {
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
    } else {
        $stats = calcularEstadisticas($resultado['data']);
        sendSuccess($stats);
    }
}

/**
 * Calcular estadísticas detalladas
 */
function calcularEstadisticas($agenda) {
    $totalSemanas = count($agenda['semanas']);
    $totalContenidos = 0;
    $estadosCont = [
        'pendiente' => 0,
        'en-progreso' => 0,
        'completado' => 0,
        'pausado' => 0,
        'no-incluir' => 0,
        'sin-estado' => 0
    ];
    $pilaresCont = [
        'gobernanza' => 0,
        'cultura' => 0,
        'academia' => 0,
        'campus' => 0,
        'vinculacion' => 0,
        'responsabilidad' => 0,
        'sin-etiqueta' => 0
    ];
    
    foreach ($agenda['semanas'] as $semana) {
        $totalContenidos += count($semana['contenidos']);
        
        foreach ($semana['contenidos'] as $contenido) {
            // Contar estados
            $estado = $contenido['estado'] ?? '';
            if (empty($estado)) {
                $estadosCont['sin-estado']++;
            } else {
                $estadosCont[$estado] = ($estadosCont[$estado] ?? 0) + 1;
            }
            
            // Contar pilares
            $etiquetas = $contenido['etiquetas'] ?? [];
            if (empty($etiquetas)) {
                $pilaresCont['sin-etiqueta']++;
            } else {
                foreach ($etiquetas as $etiqueta) {
                    $pilaresCont[$etiqueta] = ($pilaresCont[$etiqueta] ?? 0) + 1;
                }
            }
        }
    }
    
    $porcentajeCompletado = $totalContenidos > 0 ? 
        round(($estadosCont['completado'] / $totalContenidos) * 100, 1) : 0;
    
    return [
        'total_semanas' => $totalSemanas,
        'total_contenidos' => $totalContenidos,
        'promedio_contenidos_semana' => $totalSemanas > 0 ? 
            round($totalContenidos / $totalSemanas, 1) : 0,
        'distribucion_estados' => $estadosCont,
        'distribucion_pilares' => $pilaresCont,
        'porcentaje_completado' => $porcentajeCompletado,
        'contenidos_completados' => $estadosCont['completado'],
        'contenidos_en_progreso' => $estadosCont['en-progreso'],
        'fecha_calculo' => date('Y-m-d H:i:s')
    ];
}

/**
 * Obtener semana específica
 */
function getSemana() {
    $numeroSemana = (int)($_GET['numero'] ?? 0);
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $semanaEncontrada = null;
    foreach ($resultado['data']['semanas'] as $semana) {
        if ($semana['numero'] === $numeroSemana) {
            $semanaEncontrada = $semana;
            break;
        }
    }
    
    if ($semanaEncontrada) {
        sendSuccess($semanaEncontrada);
    } else {
        sendError('Semana no encontrada', 404);
    }
}

/**
 * Obtener contenido específico
 */
function getContenido() {
    $contenidoId = $_GET['id'] ?? '';
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $contenidoEncontrado = null;
    foreach ($resultado['data']['semanas'] as $semana) {
        foreach ($semana['contenidos'] as $contenido) {
            if ($contenido['id'] === $contenidoId) {
                $contenidoEncontrado = $contenido;
                break 2;
            }
        }
    }
    
    if ($contenidoEncontrado) {
        sendSuccess($contenidoEncontrado);
    } else {
        sendError('Contenido no encontrado', 404);
    }
}

/**
 * Filtrar contenidos
 */
function filtrarContenidos() {
    $filtros = [
        'busqueda' => $_GET['busqueda'] ?? '',
        'estado' => $_GET['estado'] ?? '',
        'etiqueta' => $_GET['etiqueta'] ?? '',
        'semana' => $_GET['semana'] ?? ''
    ];
    
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $contenidosFiltrados = [];
    
    foreach ($resultado['data']['semanas'] as $semana) {
        // Filtro por semana
        if (!empty($filtros['semana']) && $semana['numero'] != $filtros['semana']) {
            continue;
        }
        
        foreach ($semana['contenidos'] as $contenido) {
            $incluir = true;
            
            // Filtro de búsqueda
            if (!empty($filtros['busqueda'])) {
                $textoBusqueda = strtolower($filtros['busqueda']);
                $textoContenido = strtolower(
                    ($contenido['titulo'] ?? '') . ' ' .
                    ($contenido['tipo'] ?? '') . ' ' .
                    ($contenido['recursos'] ?? '') . ' ' .
                    ($contenido['comentarios'] ?? '')
                );
                
                if (strpos($textoContenido, $textoBusqueda) === false) {
                    $incluir = false;
                }
            }
            
            // Filtro por estado
            if (!empty($filtros['estado']) && ($contenido['estado'] ?? '') !== $filtros['estado']) {
                $incluir = false;
            }
            
            // Filtro por etiqueta
            if (!empty($filtros['etiqueta'])) {
                $etiquetas = $contenido['etiquetas'] ?? [];
                if (!in_array($filtros['etiqueta'], $etiquetas)) {
                    $incluir = false;
                }
            }
            
            if ($incluir) {
                $contenidosFiltrados[] = array_merge($contenido, [
                    'semana_numero' => $semana['numero'],
                    'semana_fechas' => $semana['fechas'],
                    'semana_tema' => $semana['tema']
                ]);
            }
        }
    }
    
    sendSuccess([
        'contenidos' => $contenidosFiltrados,
        'total' => count($contenidosFiltrados),
        'filtros_aplicados' => $filtros
    ]);
}

/**
 * Actualizar agenda completa
 */
function actualizarAgenda() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendError('Datos inválidos', 400);
        return;
    }
    
    $resultado = guardarAgenda($input);
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
    } else {
        sendSuccess(['mensaje' => $resultado['mensaje']]);
    }
}

/**
 * Crear nuevo contenido
 */
function crearContenido() {
    $input = json_decode(file_get_contents('php://input'), true);
    $numeroSemana = $input['semana'] ?? 0;
    $nuevoContenido = $input['contenido'] ?? [];
    
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $agenda = $resultado['data'];
    
    // Buscar la semana
    $semanaIndex = -1;
    foreach ($agenda['semanas'] as $index => $semana) {
        if ($semana['numero'] == $numeroSemana) {
            $semanaIndex = $index;
            break;
        }
    }
    
    if ($semanaIndex === -1) {
        sendError('Semana no encontrada', 404);
        return;
    }
    
    // Generar ID único
    $nuevoContenido['id'] = $numeroSemana . '-' . time() . '-' . rand(100, 999);
    $nuevoContenido['fechaCreacion'] = date('c');
    $nuevoContenido['fechaModificacion'] = date('c');
    
    // Valores por defecto
    $nuevoContenido = array_merge([
        'titulo' => 'Nuevo contenido',
        'tipo' => '',
        'recursos' => '',
        'estado' => '',
        'etiquetas' => [],
        'comentarios' => '',
        'archivos' => ''
    ], $nuevoContenido);
    
    $agenda['semanas'][$semanaIndex]['contenidos'][] = $nuevoContenido;
    
    $resultadoGuardar = guardarAgenda($agenda);
    
    if ($resultadoGuardar['error']) {
        sendError($resultadoGuardar['mensaje'], 500);
    } else {
        sendSuccess([
            'mensaje' => 'Contenido creado correctamente',
            'contenido' => $nuevoContenido
        ]);
    }
}

/**
 * Actualizar contenido existente
 */
function actualizarContenido() {
    $input = json_decode(file_get_contents('php://input'), true);
    $contenidoId = $input['id'] ?? '';
    
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $agenda = $resultado['data'];
    $encontrado = false;
    
    foreach ($agenda['semanas'] as &$semana) {
        foreach ($semana['contenidos'] as &$contenido) {
            if ($contenido['id'] === $contenidoId) {
                // Actualizar campos
                foreach ($input as $campo => $valor) {
                    if ($campo !== 'id') {
                        $contenido[$campo] = $valor;
                    }
                }
                $contenido['fechaModificacion'] = date('c');
                $encontrado = true;
                break 2;
            }
        }
    }
    
    if (!$encontrado) {
        sendError('Contenido no encontrado', 404);
        return;
    }
    
    $resultadoGuardar = guardarAgenda($agenda);
    
    if ($resultadoGuardar['error']) {
        sendError($resultadoGuardar['mensaje'], 500);
    } else {
        sendSuccess(['mensaje' => 'Contenido actualizado correctamente']);
    }
}

/**
 * Eliminar contenido
 */
function eliminarContenido() {
    $contenidoId = $_GET['id'] ?? '';
    
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $agenda = $resultado['data'];
    $encontrado = false;
    
    foreach ($agenda['semanas'] as &$semana) {
        foreach ($semana['contenidos'] as $index => $contenido) {
            if ($contenido['id'] === $contenidoId) {
                array_splice($semana['contenidos'], $index, 1);
                $encontrado = true;
                break 2;
            }
        }
    }
    
    if (!$encontrado) {
        sendError('Contenido no encontrado', 404);
        return;
    }
    
    $resultadoGuardar = guardarAgenda($agenda);
    
    if ($resultadoGuardar['error']) {
        sendError($resultadoGuardar['mensaje'], 500);
    } else {
        sendSuccess(['mensaje' => 'Contenido eliminado correctamente']);
    }
}

/**
 * Exportar datos
 */
function exportarDatos() {
    $formato = $_GET['formato'] ?? 'json';
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    switch ($formato) {
        case 'csv':
            exportarCSV($resultado['data']);
            break;
        case 'json':
        default:
            header('Content-Disposition: attachment; filename="agenda-fen-' . date('Y-m-d') . '.json"');
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
    }
}

/**
 * Exportar a CSV
 */
function exportarCSV($agenda) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="agenda-fen-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeceras
    fputcsv($output, [
        'Semana', 'Fechas', 'Tema', 'ID Contenido', 'Título', 'Tipo', 
        'Recursos', 'Estado', 'Etiquetas', 'Comentarios', 'Archivos', 
        'Fecha Creación', 'Fecha Modificación'
    ]);
    
    // Datos
    foreach ($agenda['semanas'] as $semana) {
        foreach ($semana['contenidos'] as $contenido) {
            fputcsv($output, [
                $semana['numero'],
                $semana['fechas'],
                $semana['tema'],
                $contenido['id'],
                $contenido['titulo'] ?? '',
                $contenido['tipo'] ?? '',
                $contenido['recursos'] ?? '',
                $contenido['estado'] ?? '',
                is_array($contenido['etiquetas'] ?? []) ? 
                    implode(';', $contenido['etiquetas']) : '',
                $contenido['comentarios'] ?? '',
                $contenido['archivos'] ?? '',
                $contenido['fechaCreacion'] ?? '',
                $contenido['fechaModificacion'] ?? ''
            ]);
        }
    }
    
    fclose($output);
}

/**
 * Crear backup
 */
function crearBackup() {
    $resultado = crearBackupSilencioso();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
    } else {
        sendSuccess($resultado);
    }
}

/**
 * Crear backup sin respuesta
 */
function crearBackupSilencioso() {
    global $jsonFile, $backupDir;
    
    if (!file_exists($jsonFile)) {
        return [
            'error' => true,
            'mensaje' => 'Archivo de agenda no encontrado'
        ];
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . "agenda_backup_{$timestamp}.json";
    
    if (copy($jsonFile, $backupFile)) {
        return [
            'error' => false,
            'mensaje' => 'Backup creado correctamente',
            'archivo' => $backupFile
        ];
    } else {
        return [
            'error' => true,
            'mensaje' => 'Error al crear backup'
        ];
    }
}

/**
 * Registrar cambio en historial
 */
function registrarCambio($accion, $datos = null) {
    global $historialFile;
    
    $cambio = [
        'timestamp' => date('c'),
        'accion' => $accion,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    if ($datos) {
        $cambio['resumen'] = generarResumenCambio($accion, $datos);
    }
    
    $historial = [];
    if (file_exists($historialFile)) {
        $historialContent = file_get_contents($historialFile);
        $historial = json_decode($historialContent, true) ?? [];
    }
    
    $historial[] = $cambio;
    
    // Mantener solo los últimos 100 cambios
    if (count($historial) > 100) {
        $historial = array_slice($historial, -100);
    }
    
    file_put_contents($historialFile, json_encode($historial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Generar resumen de cambio
 */
function generarResumenCambio($accion, $datos) {
    switch ($accion) {
        case 'agenda_actualizada':
            return 'Agenda completa actualizada';
        case 'contenido_creado':
            return 'Nuevo contenido: ' . ($datos['titulo'] ?? 'Sin título');
        case 'contenido_actualizado':
            return 'Contenido actualizado: ' . ($datos['titulo'] ?? 'Sin título');
        case 'contenido_eliminado':
            return 'Contenido eliminado';
        default:
            return $accion;
    }
}

/**
 * Obtener historial
 */
function getHistorial() {
    global $historialFile;
    
    if (!file_exists($historialFile)) {
        sendSuccess([]);
        return;
    }
    
    $historialContent = file_get_contents($historialFile);
    $historial = json_decode($historialContent, true) ?? [];
    
    // Ordenar por timestamp descendente
    usort($historial, function($a, $b) {
        return strcmp($b['timestamp'], $a['timestamp']);
    });
    
    sendSuccess($historial);
}

/**
 * Enviar respuesta de éxito
 */
function sendSuccess($data) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $data,
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Enviar respuesta de error
 */
function sendError($mensaje, $codigo = 400) {
    http_response_code($codigo);
    echo json_encode([
        'success' => false,
        'error' => true,
        'mensaje' => $mensaje,
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Ejecutar aplicación
main();
?>