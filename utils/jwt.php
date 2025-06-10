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
        
        return null;
    }
    
    /**
     * Obtener el header Authorization
     */
    private static function getAuthorizationHeader() {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }
}
?>
