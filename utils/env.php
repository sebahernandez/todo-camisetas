<?php
/**
 * Cargador de variables de entorno desde archivo .env
 */

class Env {
    private static $loaded = false;
    
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }
        
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Separar clave=valor
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remover comillas si existen
                if (preg_match('/^".*"$/', $value) || preg_match("/^'.*'$/", $value)) {
                    $value = substr($value, 1, -1);
                }
                
                // Solo establecer si no existe ya
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                }
                
                // También establecer en $_SERVER para getenv()
                if (!array_key_exists($key, $_SERVER)) {
                    $_SERVER[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}
?>
