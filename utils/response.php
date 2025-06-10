<?php
/**
 * Clase para manejar respuestas JSON de la API
 */

class Response {
    
    /**
     * Enviar respuesta de éxito
     */
    public static function success($data = null, $message = 'Operación exitosa', $code = 200) {
        http_response_code($code);
        
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Enviar respuesta de error
     */
    public static function error($message = 'Error interno del servidor', $code = 500, $errors = null) {
        http_response_code($code);
        
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        // Agregar stack trace en desarrollo
        if (!PRODUCTION && $code >= 500) {
            $response['debug'] = [
                'file' => debug_backtrace()[0]['file'] ?? '',
                'line' => debug_backtrace()[0]['line'] ?? '',
                'trace' => debug_backtrace()
            ];
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Enviar respuesta JSON genérica
     */
    public static function json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Respuesta de validación fallida
     */
    public static function validation($errors, $message = 'Error de validación') {
        self::error($message, 400, $errors);
    }
    
    /**
     * Respuesta de no autorizado
     */
    public static function unauthorized($message = 'No autorizado') {
        self::error($message, 401);
    }
    
    /**
     * Respuesta de prohibido
     */
    public static function forbidden($message = 'Acceso prohibido') {
        self::error($message, 403);
    }
    
    /**
     * Respuesta de no encontrado
     */
    public static function notFound($message = 'Recurso no encontrado') {
        self::error($message, 404);
    }
    
    /**
     * Respuesta de conflicto (recurso ya existe)
     */
    public static function conflict($message = 'El recurso ya existe') {
        self::error($message, 409);
    }
    
    /**
     * Respuesta de datos creados
     */
    public static function created($data = null, $message = 'Recurso creado exitosamente') {
        self::success($data, $message, 201);
    }
    
    /**
     * Respuesta de no contenido (para DELETE)
     */
    public static function noContent() {
        http_response_code(204);
        exit();
    }
}
?>
