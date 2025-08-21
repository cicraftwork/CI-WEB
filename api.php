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
        case 'reporte':
            generarReporte();
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
        case 'estado-masivo':
            cambiarEstadoMasivo();
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
    
    // Actualizar fecha de modificación
    $agenda['fechaModificacion'] = date('c');
    
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
 * Obtener estadísticas mejoradas v4.0
 */
function getEstadisticas() {
    $resultado = leerAgenda();
    
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
    } else {
        $stats = calcularEstadisticasAvanzadas($resultado['data']);
        sendSuccess($stats);
    }
}

/**
 * Calcular estadísticas avanzadas con manejo de excluidos
 */
function calcularEstadisticasAvanzadas($agenda) {
    $totalSemanas = count($agenda['semanas']);
    $totalContenidos = 0;
    $totalContenidosValidos = 0; // Excluye "no-incluir"
    
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
    
    $detallesPorSemana = [];

    foreach ($agenda['semanas'] as $semana) {
        $contenidosSemana = count($semana['contenidos']);
        $validosSemana = 0;
        $completadosSemana = 0;
        $progresoSemana = 0;
        $excuidosSemana = 0;
        
        $totalContenidos += $contenidosSemana;
        
        foreach ($semana['contenidos'] as $contenido) {
            $estado = $contenido['estado'] ?? '';
            
            // Contar todos los estados
            if (empty($estado)) {
                $estadosCont['sin-estado']++;
            } else {
                $estadosCont[$estado]++;
            }

            // Contar solo contenidos válidos (excluye "no-incluir")
            if ($estado !== 'no-incluir') {
                $totalContenidosValidos++;
                $validosSemana++;
                
                if ($estado === 'completado') $completadosSemana++;
                if ($estado === 'en-progreso') $progresoSemana++;
                
                // Contar pilares solo para contenidos válidos
                $etiquetas = $contenido['etiquetas'] ?? [];
                if (empty($etiquetas)) {
                    $pilaresCont['sin-etiqueta']++;
                } else {
                    foreach ($etiquetas as $etiqueta) {
                        if (isset($pilaresCont[$etiqueta])) {
                            $pilaresCont[$etiqueta]++;
                        }
                    }
                }
            } else {
                $excuidosSemana++;
            }
        }
        
        $detallesPorSemana[] = [
            'numero' => $semana['numero'],
            'fechas' => $semana['fechas'],
            'tema' => $semana['tema'],
            'total_contenidos' => $contenidosSemana,
            'contenidos_validos' => $validosSemana,
            'completados' => $completadosSemana,
            'en_progreso' => $progresoSemana,
            'excluidos' => $excuidosSemana,
            'porcentaje_completado' => $validosSemana > 0 ? 
                round(($completadosSemana / $validosSemana) * 100, 1) : 0
        ];
    }

    // Calcular porcentaje solo con contenidos válidos
    $porcentajeCompletado = $totalContenidosValidos > 0 ? 
        round(($estadosCont['completado'] / $totalContenidosValidos) * 100, 1) : 0;

    return [
        'version' => '4.0',
        'total_semanas' => $totalSemanas,
        'total_contenidos' => $totalContenidos,
        'total_contenidos_validos' => $totalContenidosValidos,
        'promedio_contenidos_semana' => $totalSemanas > 0 ? 
            round($totalContenidosValidos / $totalSemanas, 1) : 0,
        'distribucion_estados' => $estadosCont,
        'distribucion_pilares' => $pilaresCont,
        'porcentaje_completado' => $porcentajeCompletado,
        'contenidos_completados' => $estadosCont['completado'],
        'contenidos_en_progreso' => $estadosCont['en-progreso'],
        'contenidos_excluidos' => $estadosCont['no-incluir'],
        'detalles_por_semana' => $detallesPorSemana,
        'fecha_calculo' => date('Y-m-d H:i:s'),
        'timestamp' => time()
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
                $contenidoEncontrado['semana_numero'] = $semana['numero'];
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
 * Filtrar contenidos con soporte para excluidos
 */
function filtrarContenidos() {
    $filtros = [
        'busqueda' => $_GET['busqueda'] ?? '',
        'estado' => $_GET['estado'] ?? '',
        'etiqueta' => $_GET['etiqueta'] ?? '',
        'semana' => $_GET['semana'] ?? '',
        'incluir_excluidos' => ($_GET['incluir_excluidos'] ?? 'true') === 'true'
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
            
            // Filtro de excluidos
            if (!$filtros['incluir_excluidos'] && ($contenido['estado'] ?? '') === 'no-incluir') {
                $incluir = false;
            }
            
            // Filtro de búsqueda
            if (!empty($filtros['busqueda']) && $incluir) {
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
 * Cambiar estado masivo - Nueva función v4.0
 */
function cambiarEstadoMasivo() {
    $input = json_decode(file_get_contents('php://input'), true);
    $nuevoEstado = $input['estado'] ?? '';
    $contenidosIds = $input['contenidos'] ?? [];
    
    if (empty($nuevoEstado) || empty($contenidosIds)) {
        sendError('Estado y contenidos son requeridos', 400);
        return;
    }
    
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $agenda = $resultado['data'];
    $actualizados = 0;
    
    foreach ($agenda['semanas'] as &$semana) {
        foreach ($semana['contenidos'] as &$contenido) {
            if (in_array($contenido['id'], $contenidosIds)) {
                $contenido['estado'] = $nuevoEstado;
                $contenido['fechaModificacion'] = date('c');
                $actualizados++;
            }
        }
    }
    
    if ($actualizados > 0) {
        $resultadoGuardar = guardarAgenda($agenda);
        
        if ($resultadoGuardar['error']) {
            sendError($resultadoGuardar['mensaje'], 500);
        } else {
            registrarCambio('estado_masivo', [
                'estado' => $nuevoEstado,
                'cantidad' => $actualizados
            ]);
            
            sendSuccess([
                'mensaje' => "Se actualizaron $actualizados contenidos a estado: $nuevoEstado",
                'actualizados' => $actualizados,
                'estado' => $nuevoEstado
            ]);
        }
    } else {
        sendError('No se encontraron contenidos para actualizar', 404);
    }
}

/**
 * Generar reporte completo - Nueva función v4.0
 */
function generarReporte() {
    $resultado = leerAgenda();
    if ($resultado['error']) {
        sendError($resultado['mensaje'], 500);
        return;
    }
    
    $stats = calcularEstadisticasAvanzadas($resultado['data']);
    $agenda = $resultado['data'];
    
    $reporte = [
        'metadata' => [
            'titulo' => $agenda['titulo'],
            'periodo' => $agenda['periodo'],
            'version' => $agenda['version'] ?? '4.0',
            'fecha_generacion' => date('Y-m-d H:i:s'),
            'total_semanas' => $stats['total_semanas'],
            'total_contenidos' => $stats['total_contenidos'],
            'total_contenidos_validos' => $stats['total_contenidos_validos']
        ],
        'resumen_ejecutivo' => [
            'porcentaje_completado' => $stats['porcentaje_completado'],
            'contenidos_completados' => $stats['contenidos_completados'],
            'contenidos_en_progreso' => $stats['contenidos_en_progreso'],
            'contenidos_excluidos' => $stats['contenidos_excluidos'],
            'promedio_por_semana' => $stats['promedio_contenidos_semana']
        ],
        'distribucion_estados' => $stats['distribucion_estados'],
        'distribucion_pilares' => $stats['distribucion_pilares'],
        'analisis_por_semana' => $stats['detalles_por_semana'],
        'recomendaciones' => generarRecomendaciones($stats)
    ];
    
    sendSuccess($reporte);
}

/**
 * Generar recomendaciones basadas en estadísticas
 */
function generarRecomendaciones($stats) {
    $recomendaciones = [];
    
    // Analizar progreso general
    if ($stats['porcentaje_completado'] < 30) {
        $recomendaciones[] = [
            'tipo' => 'urgente',
            'titulo' => 'Progreso Bajo',
            'descripcion' => 'El porcentaje de completado es menor al 30%. Se recomienda revisar recursos y plazos.',
            'accion' => 'Revisar contenidos pendientes y asignar responsables'
        ];
    } elseif ($stats['porcentaje_completado'] > 80) {
        $recomendaciones[] = [
            'tipo' => 'excelente',
            'titulo' => 'Excelente Progreso',
            'descripcion' => 'El proyecto mantiene un excelente ritmo de avance.',
            'accion' => 'Continuar con el plan actual'
        ];
    }
    
    // Analizar contenidos excluidos
    if ($stats['contenidos_excluidos'] > 5) {
        $recomendaciones[] = [
            'tipo' => 'atencion',
            'titulo' => 'Muchos Contenidos Excluidos',
            'descripcion' => "Hay {$stats['contenidos_excluidos']} contenidos marcados como 'no-incluir'. Revisar si es necesario.",
            'accion' => 'Evaluar la relevancia de los contenidos excluidos'
        ];
    }
    
    // Analizar distribución por pilares
    $pilarMinimo = min(array_filter($stats['distribucion_pilares']));
    $pilarMaximo = max($stats['distribucion_pilares']);
    
    if ($pilarMaximo - $pilarMinimo > 10) {
        $recomendaciones[] = [
            'tipo' => 'balanceo',
            'titulo' => 'Desequilibrio en Pilares',
            'descripcion' => 'Existe una distribución desigual entre los pilares del APL.',
            'accion' => 'Considerar balancear los contenidos entre todos los pilares'
        ];
    }
    
    return $recomendaciones;
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
        registrarCambio('contenido_creado', $nuevoContenido);
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
        registrarCambio('contenido_actualizado', $input);
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
                $contenidoEliminado = $contenido;
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
        registrarCambio('contenido_eliminado', $contenidoEliminado);
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
        case 'reporte':
            exportarReporte($resultado['data']);
            break;
        case 'json':
        default:
            header('Content-Disposition: attachment; filename="agenda-fen-' . date('Y-m-d') . '.json"');
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
    }
}

/**
 * Exportar a CSV mejorado v4.0
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
        'Fecha Creación', 'Fecha Modificación', 'Excluido'
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
                $contenido['fechaModificacion'] ?? '',
                ($contenido['estado'] ?? '') === 'no-incluir' ? 'SÍ' : 'NO'
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
        // Limpiar backups antiguos (mantener solo los últimos 10)
        $backups = glob($backupDir . 'agenda_backup_*.json');
        if (count($backups) > 10) {
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $aEliminar = array_slice($backups, 0, count($backups) - 10);
            foreach ($aEliminar as $backup) {
                unlink($backup);
            }
        }
        
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
            return 'Contenido eliminado: ' . ($datos['titulo'] ?? 'Sin título');
        case 'estado_masivo':
            return "Cambio masivo: {$datos['cantidad']} contenidos a estado {$datos['estado']}";
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
        'timestamp' => date('c'),
        'version' => '4.0'
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
        'timestamp' => date('c'),
        'version' => '4.0'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Ejecutar aplicación
main();
?>