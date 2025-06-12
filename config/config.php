<?php
/**
 * Configuración general de la aplicación
 */

// Intentar cargar variables de entorno desde .env
try {
    require_once __DIR__ . '/../utils/env.php';
    Env::load();
    
    // Variables desde .env (sin valores por defecto para producción)
    $db_host = Env::get('DB_HOST');
    $db_name = Env::get('DB_NAME');
    $db_user = Env::get('DB_USER');
    $db_pass = Env::get('DB_PASS');
    $jwt_secret = Env::get('JWT_SECRET');
    $production = Env::get('PRODUCTION', 'false') === 'true';
    
    // Verificar que todas las variables requeridas estén definidas
    if (empty($db_host) || empty($db_name) || empty($db_user) || empty($jwt_secret)) {
        throw new Exception('Variables de entorno requeridas no están definidas en .env');
    }
    
} catch (Exception $e) {
    // Si estamos en desarrollo local, usar valores por defecto (solo en desarrollo)
    if (php_sapi_name() == 'cli-server' || 
        strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
        strpos($_SERVER['SERVER_ADDR'] ?? '', '127.0.0.1') !== false) {
        
        $db_host = 'localhost'; // Sin puerto predeterminado
        $db_name = 'test_db';   // Nombre genérico
        $db_user = 'dbuser';    // Usuario genérico
        $db_pass = '';          // Sin contraseña predeterminada
        $jwt_secret = md5(time()); // Generado dinámicamente
        $production = false;
        
        error_log('ADVERTENCIA: Usando configuración por defecto en desarrollo. Por favor configure un archivo .env');
    } else {
        // En producción, detenemos la ejecución si no hay .env configurado
        header('HTTP/1.1 500 Internal Server Error');
        die('Error de configuración: ' . $e->getMessage());
    }
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
