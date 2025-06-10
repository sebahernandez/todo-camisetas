<?php
/**
 * Middleware de autenticación
 */

require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../config/database.php';

class AuthMiddleware {
    
    /**
     * Verificar autenticación
     */
    public static function authenticate() {
        $token = JWT::getBearerToken();
        
        if (!$token) {
            Response::unauthorized('Token de acceso requerido');
        }
        
        try {
            $payload = JWT::decode($token);
            
            // Verificar que el usuario existe y está activo
            $db = Database::getInstance();
            $user = $db->selectOne(
                'SELECT id, email, nombre, rol, activo FROM usuarios WHERE id = ? AND activo = TRUE',
                [$payload['user_id']]
            );
            
            if (!$user) {
                Response::unauthorized('Usuario no válido o inactivo');
            }
            
            // Guardar usuario en variable global para uso posterior
            $GLOBALS['current_user'] = $user;
            
            return $user;
            
        } catch (Exception $e) {
            Response::unauthorized('Token no válido: ' . $e->getMessage());
        }
    }
    
    /**
     * Verificar que el usuario sea administrador
     */
    public static function requireAdmin() {
        $user = self::authenticate();
        
        if ($user['rol'] !== 'admin') {
            Response::forbidden('Se requieren permisos de administrador');
        }
        
        return $user;
    }
    
    /**
     * Verificar autenticación opcional (no falla si no hay token)
     */
    public static function authenticateOptional() {
        $token = JWT::getBearerToken();
        
        if (!$token) {
            return null;
        }
        
        try {
            $payload = JWT::decode($token);
            
            $db = Database::getInstance();
            $user = $db->selectOne(
                'SELECT id, email, nombre, rol, activo FROM usuarios WHERE id = ? AND activo = TRUE',
                [$payload['user_id']]
            );
            
            if ($user) {
                $GLOBALS['current_user'] = $user;
                return $user;
            }
            
        } catch (Exception $e) {
            // Token inválido, pero no fallar
        }
        
        return null;
    }
    
    /**
     * Obtener usuario actual (debe llamarse después de authenticate)
     */
    public static function getCurrentUser() {
        return $GLOBALS['current_user'] ?? null;
    }
}
?>
