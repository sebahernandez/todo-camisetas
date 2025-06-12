<?php
/**
 * Manejo de JSON Web Tokens (JWT) sin librerías externas
 */

class JWT {
    
    /**
     * Generar un token JWT
     */
    public static function encode($payload, $secret = null) {
        $secret = $secret ?? JWT_SECRET;
        
        // Header
        $header = json_encode(['typ' => 'JWT', 'alg' => JWT_ALGORITHM]);
        $headerEncoded = self::base64UrlEncode($header);
        
        // Payload con expiración
        $payload['exp'] = time() + JWT_EXPIRES_IN;
        $payloadJson = json_encode($payload);
        $payloadEncoded = self::base64UrlEncode($payloadJson);
        
        // Signature
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Decodificar y verificar un token JWT
     */
    public static function decode($token, $secret = null) {
        $secret = $secret ?? JWT_SECRET;
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new Exception('Token JWT inválido');
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verificar signature
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            throw new Exception('Firma del token JWT inválida');
        }
        
        // Decodificar payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload) {
            throw new Exception('Payload del token JWT inválido');
        }
        
        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token JWT expirado');
        }
        
        return $payload;
    }
    
    /**
     * Codificar en base64url
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodificar de base64url
     */
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Extraer token del header Authorization
     */
    public static function getBearerToken() {
        $headers = self::getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        // Fallback: buscar token en query parameter (para debugging/testing)
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        // Fallback: buscar en X-API-Token header
        if (isset($_SERVER['HTTP_X_API_TOKEN'])) {
            return $_SERVER['HTTP_X_API_TOKEN'];
        }
        
        return null;
    }
    
    /**
     * Obtener el header Authorization
     */
    private static function getAuthorizationHeader() {
        $headers = null;
        
        // Intento 1: $_SERVER directo
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } 
        // Intento 2: HTTP_AUTHORIZATION
        elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        // Intento 3: REDIRECT_HTTP_AUTHORIZATION (para algunos servidores Apache)
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }
        // Intento 4: apache_request_headers()
        elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            
            // Buscar Authorization con diferentes casos
            foreach (['Authorization', 'authorization', 'AUTHORIZATION'] as $key) {
                if (isset($requestHeaders[$key])) {
                    $headers = trim($requestHeaders[$key]);
                    break;
                }
            }
        }
        
        // Debug: Log todos los headers para diagnóstico
        if (empty($headers) && defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('JWT Debug - $_SERVER keys: ' . implode(', ', array_keys($_SERVER)));
            if (function_exists('apache_request_headers')) {
                error_log('JWT Debug - Apache headers: ' . json_encode(apache_request_headers()));
            }
        }
        
        return $headers;
    }
}
?>
