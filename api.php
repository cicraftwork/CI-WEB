<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Función para leer el archivo JSON
function leerAgenda() {
    $jsonFile = 'agenda.json';
    
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

// Función para obtener estadísticas de la agenda
function obtenerEstadisticas($agenda) {
    $totalSemanas = count($agenda['semanas']);
    $totalContenidos = 0;
    $categorias = [];
    
    foreach ($agenda['semanas'] as $semana) {
        $totalContenidos += count($semana['contenidos']);
        
        // Extraer categoría principal del tema
        $tema = explode(' ', $semana['tema'])[0];
        if (!isset($categorias[$tema])) {
            $categorias[$tema] = 0;
        }
        $categorias[$tema] += count($semana['contenidos']);
    }
    
    return [
        'total_semanas' => $totalSemanas,
        'total_contenidos' => $totalContenidos,
        'promedio_contenidos_semana' => round($totalContenidos / $totalSemanas, 1),
        'distribucion_categorias' => $categorias
    ];
}

// Manejar diferentes endpoints
$action = $_GET['action'] ?? 'agenda';

switch ($action) {
    case 'agenda':
        $resultado = leerAgenda();
        if (!$resultado['error']) {
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'estadisticas':
        $resultado = leerAgenda();
        if (!$resultado['error']) {
            $stats = obtenerEstadisticas($resultado['data']);
            echo json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'semana':
        $numeroSemana = (int)($_GET['numero'] ?? 0);
        $resultado = leerAgenda();
        
        if (!$resultado['error']) {
            $semanaEncontrada = null;
            foreach ($resultado['data']['semanas'] as $semana) {
                if ($semana['numero'] === $numeroSemana) {
                    $semanaEncontrada = $semana;
                    break;
                }
            }
            
            if ($semanaEncontrada) {
                echo json_encode($semanaEncontrada, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'Semana no encontrada'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            http_response_code(500);
            echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'mensaje' => 'Acción no válida',
            'acciones_disponibles' => ['agenda', 'estadisticas', 'semana']
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>