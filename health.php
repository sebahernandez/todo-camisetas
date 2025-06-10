<?php
/**
 * Health Check - Todo Camisetas API
 * 
 * Script para verificar el estado completo del sistema
 */

require_once __DIR__ . '/utils/env.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/response.php';

header('Content-Type: application/json; charset=utf-8');

$healthCheck = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'api_version' => '1.0.0',
    'status' => 'healthy',
    'checks' => []
];

try {
    // 1. Verificar conectividad de base de datos
    $db = Database::getInstance();
    $result = $db->select("SELECT 1 as test");
    $healthCheck['checks']['database'] = [
        'status' => 'ok',
        'message' => 'Conexión a base de datos exitosa'
    ];
    
    // 2. Verificar tablas principales
    $tables = ['usuarios', 'camisetas', 'categorias', 'marcas'];
    foreach ($tables as $table) {
        $result = $db->select("SELECT COUNT(*) as count FROM $table");
        $count = $result[0]['count'];
        $healthCheck['checks']["table_$table"] = [
            'status' => 'ok',
            'count' => (int)$count,
            'message' => "Tabla $table: $count registros"
        ];
    }
    
    // 3. Verificar directorio uploads
    $uploadsPath = __DIR__ . '/uploads';
    $uploadsWritable = is_writable($uploadsPath);
    $healthCheck['checks']['uploads_directory'] = [
        'status' => $uploadsWritable ? 'ok' : 'error',
        'writable' => $uploadsWritable,
        'message' => $uploadsWritable ? 'Directorio uploads escribible' : 'Directorio uploads no escribible'
    ];
    
    // 4. Verificar configuración JWT
    $jwtSecret = $_ENV['JWT_SECRET'] ?? '';
    $jwtConfigured = !empty($jwtSecret) && $jwtSecret !== 'CambiarEstePorUnSecretoSeguroEnProduccion';
    $healthCheck['checks']['jwt_config'] = [
        'status' => $jwtConfigured ? 'ok' : 'warning',
        'configured' => $jwtConfigured,
        'message' => $jwtConfigured ? 'JWT configurado correctamente' : 'JWT usando configuración por defecto'
    ];
    
    // 5. Verificar modo de producción
    $isProduction = ($_ENV['PRODUCTION'] ?? 'false') === 'true';
    $debugMode = ($_ENV['DEBUG_MODE'] ?? 'false') === 'true';
    $healthCheck['checks']['environment'] = [
        'status' => 'ok',
        'production_mode' => $isProduction,
        'debug_mode' => $debugMode,
        'message' => $isProduction ? 'Modo producción activo' : 'Modo desarrollo activo'
    ];
    
    // Verificar si hay algún error
    $hasErrors = false;
    foreach ($healthCheck['checks'] as $check) {
        if ($check['status'] === 'error') {
            $hasErrors = true;
            break;
        }
    }
    
    if ($hasErrors) {
        $healthCheck['success'] = false;
        $healthCheck['status'] = 'unhealthy';
        http_response_code(503);
    }
    
} catch (Exception $e) {
    $healthCheck['success'] = false;
    $healthCheck['status'] = 'error';
    $healthCheck['error'] = $e->getMessage();
    $healthCheck['checks']['system'] = [
        'status' => 'error',
        'message' => 'Error crítico del sistema: ' . $e->getMessage()
    ];
    http_response_code(500);
}

echo json_encode($healthCheck, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
