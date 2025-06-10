<?php
/**
 * Configuración general de la aplicación
 */

// Intentar cargar variables de entorno, pero usar valores por defecto si hay errores
try {
    require_once __DIR__ . '/../utils/env.php';
    Env::load();
    
    // Variables desde .env con fallbacks
    $db_host = Env::get('DB_HOST', 'localhost:8889');
    $db_name = Env::get('DB_NAME', 'todo_camisetas');
    $db_user = Env::get('DB_USER', 'root');
    $db_pass = Env::get('DB_PASS', 'root');
    $jwt_secret = Env::get('JWT_SECRET', 'TodoCamisetasAPI2025_SuperSecretKey_ChangeInProduction');
    $production = Env::get('PRODUCTION', 'false') === 'true';
    
} catch (Exception $e) {
    // Si hay error cargando .env, usar valores por defecto
    $db_host = 'localhost:8889';
    $db_name = 'todo_camisetas';
    $db_user = 'root';
    $db_pass = 'root';
    $jwt_secret = 'TodoCamisetasAPI2025_SuperSecretKey_ChangeInProduction';
    $production = false;
}

// Configuración de la base de datos
define('DB_HOST', $db_host);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_CHARSET', 'utf8mb4');

// Configuración JWT
define('JWT_SECRET', $jwt_secret);
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRES_IN', 24 * 60 * 60); // 24 horas en segundos

// Configuración de archivos
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuración general
define('API_VERSION', '1.0.0');
define('TIMEZONE', 'America/Santiago');
if (!defined('PRODUCTION')) {
    define('PRODUCTION', false);
}

// Configurar zona horaria
date_default_timezone_set(TIMEZONE);

// Configuración de errores para desarrollo
if (!PRODUCTION) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
