<?php
/**
 * API Router - Manejo de rutas específicas sin .htaccess
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

// Incluir configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/utils/response.php';

// Obtener la ruta de la API
$path = $_GET['path'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Manejar CORS preflight
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Rutas de la API
switch (true) {
    // Status de la API
    case $path === 'status' && $method === 'GET':
        $status = [
            'success' => true,
            'message' => 'API funcionando correctamente',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'endpoints' => [
                'auth' => ['POST /auth/register', 'POST /auth/login'],
                'camisetas' => ['GET /camisetas', 'POST /camisetas', 'GET /camisetas/{id}', 'PUT /camisetas/{id}', 'DELETE /camisetas/{id}'],
                'categorias' => ['GET /categorias', 'POST /categorias', 'GET /categorias/{id}', 'PUT /categorias/{id}', 'DELETE /categorias/{id}'],
                'marcas' => ['GET /marcas', 'POST /marcas', 'GET /marcas/{id}', 'PUT /marcas/{id}', 'DELETE /marcas/{id}']
            ]
        ];
        Response::json($status);
        break;
        
    // Autenticación
    case $path === 'auth/register' && $method === 'POST':
        require_once __DIR__ . '/routes/auth.php';
        handleRegister();
        break;
        
    case $path === 'auth/login' && $method === 'POST':
        require_once __DIR__ . '/routes/auth.php';
        handleLogin();
        break;
    
    // Camisetas
    case $path === 'camisetas' && $method === 'GET':
        require_once __DIR__ . '/routes/camisetas.php';
        handleGetCamisetas();
        break;
        
    case preg_match('/^camisetas\/(\d+)$/', $path, $matches) && $method === 'GET':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/camisetas.php';
        handleGetCamiseta();
        break;
        
    case $path === 'camisetas' && $method === 'POST':
        require_once __DIR__ . '/routes/camisetas.php';
        handleCreateCamiseta();
        break;
        
    case preg_match('/^camisetas\/(\d+)$/', $path, $matches) && $method === 'PUT':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/camisetas.php';
        handleUpdateCamiseta();
        break;
        
    case preg_match('/^camisetas\/(\d+)$/', $path, $matches) && $method === 'DELETE':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/camisetas.php';
        handleDeleteCamiseta();
        break;
    
    // Categorías
    case $path === 'categorias' && $method === 'GET':
        require_once __DIR__ . '/routes/categorias.php';
        handleGetCategorias();
        break;
        
    case preg_match('/^categorias\/(\d+)$/', $path, $matches) && $method === 'GET':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/categorias.php';
        handleGetCategoria();
        break;
        
    case $path === 'categorias' && $method === 'POST':
        require_once __DIR__ . '/routes/categorias.php';
        handleCreateCategoria();
        break;
        
    case preg_match('/^categorias\/(\d+)$/', $path, $matches) && $method === 'PUT':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/categorias.php';
        handleUpdateCategoria();
        break;
        
    case preg_match('/^categorias\/(\d+)$/', $path, $matches) && $method === 'DELETE':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/categorias.php';
        handleDeleteCategoria();
        break;
    
    // Marcas
    case $path === 'marcas' && $method === 'GET':
        require_once __DIR__ . '/routes/marcas.php';
        handleGetMarcas();
        break;
        
    case preg_match('/^marcas\/(\d+)$/', $path, $matches) && $method === 'GET':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/marcas.php';
        handleGetMarca();
        break;
        
    case $path === 'marcas' && $method === 'POST':
        require_once __DIR__ . '/routes/marcas.php';
        handleCreateMarca();
        break;
        
    case preg_match('/^marcas\/(\d+)$/', $path, $matches) && $method === 'PUT':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/marcas.php';
        handleUpdateMarca();
        break;
        
    case preg_match('/^marcas\/(\d+)$/', $path, $matches) && $method === 'DELETE':
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/routes/marcas.php';
        handleDeleteMarca();
        break;
    
    // Ruta no encontrada
    default:
        Response::error("Ruta no encontrada: $method $path", 404);
}
?>
