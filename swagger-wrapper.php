<?php
/**
 * Swagger Wrapper - Todo Camisetas API
 * 
 * Este archivo actúa como un proxy/wrapper entre Swagger UI y nuestra API.
 * Convierte las rutas REST estándar de Swagger al formato de query parameter
 * que utiliza nuestra API (api.php?path=).
 * 
 * Ejemplo:
 * - Swagger solicita: /auth/login
 * - Wrapper convierte a: api.php?path=auth/login
 * 
 * @author Todo Camisetas API Team
 * @version 1.0.0
 */

// Configurar manejo de errores
require_once __DIR__ . '/utils/env.php';
$debug_mode = $_ENV['DEBUG_MODE'] ?? 'false';
if ($debug_mode === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Incluir configuración CORS
require_once __DIR__ . '/config/cors.php';

// Obtener la ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Manejar CORS preflight
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Extraer el path de la URI después de /swagger-wrapper/
$basePath = '/todo-camisetas/swagger-wrapper.php';
$path = '';

// Obtener todo lo que viene después del archivo .php
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
if ($pathInfo) {
    $path = trim($pathInfo, '/');
} else {
    // Fallback: parsear desde REQUEST_URI
    $parts = parse_url($requestUri);
    if (isset($parts['path'])) {
        $fullPath = $parts['path'];
        if (strpos($fullPath, $basePath) === 0) {
            $path = substr($fullPath, strlen($basePath));
            $path = trim($path, '/');
        }
    }
}

// Construir la URL de redirección a nuestra API real
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/todo-camisetas/api.php';
$queryParams = [];

// Agregar el path como parámetro
if ($path) {
    $queryParams['path'] = $path;
}

// Preservar otros parámetros de query si existen
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $existingParams);
    $queryParams = array_merge($queryParams, $existingParams);
}

// Construir la URL final
$finalUrl = $apiUrl;
if (!empty($queryParams)) {
    $finalUrl .= '?' . http_build_query($queryParams);
}

// Realizar la request a nuestra API usando cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $finalUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_HEADER, true);

// Reenviar headers de la request original
$headers = [];
foreach (getallheaders() as $name => $value) {
    // Excluir algunos headers que pueden causar problemas
    if (!in_array(strtolower($name), ['host', 'content-length', 'connection'])) {
        $headers[] = "$name: $value";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Si hay un cuerpo de request, reenviarlo
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $input = file_get_contents('php://input');
    if ($input) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    }
}

// Ejecutar la request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

if (curl_error($ch)) {
    // Error en cURL
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit();
}

curl_close($ch);

// Separar headers y body
$responseHeaders = substr($response, 0, $headerSize);
$responseBody = substr($response, $headerSize);

// Establecer el código de respuesta
http_response_code($httpCode);

// Solo enviar el Content-Type y otros headers esenciales
foreach (explode("\r\n", $responseHeaders) as $header) {
    if (stripos($header, 'content-type:') === 0) {
        header($header);
        break;
    }
}

// Enviar el cuerpo de la respuesta
echo $responseBody;
?>
