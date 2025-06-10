<?php
/**
 * Punto de entrada principal de la API
 * Todo Camisetas - Examen Transversal Final
 */

// Configurar manejo de errores basado en entorno
require_once __DIR__ . '/utils/env.php';
$debug_mode = $_ENV['DEBUG_MODE'] ?? 'false';
if ($debug_mode === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Incluir configuración básica
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/utils/response.php';

// Página de bienvenida de la API
$response_data = [
    'success' => true,
    'message' => 'Todo Camisetas API - Examen Transversal Final',
    'version' => '1.0.0',
    'status' => 'Production Ready',
    'documentation' => 'views/swagger.php',
    'test_panel' => 'panel_pruebas.html',
    'api_endpoints' => [
        'note' => 'Usar api.php?path= para acceder a los endpoints',
        'examples' => [
            'register' => 'api.php?path=auth/register',
            'login' => 'api.php?path=auth/login', 
            'camisetas' => 'api.php?path=camisetas',
            'categorias' => 'api.php?path=categorias',
            'marcas' => 'api.php?path=marcas'
        ]
    ]
];

// Agregar información de debug solo si está habilitado
if (($debug_mode ?? 'false') === 'true') {
    $response_data['debug_info'] = [
        'debug_mode' => true,
        'health_check' => 'health_check.php',
        'method' => $_SERVER['REQUEST_METHOD'],
        'php_version' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

Response::json($response_data);
?>
