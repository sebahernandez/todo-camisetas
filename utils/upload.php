<?php
/**
 * Utilidades para manejo de subida de archivos
 */

class Upload {
    
    /**
     * Manejar subida de imagen
     */
    public static function handleImageUpload($file) {
        // Verificar errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception(self::getUploadErrorMessage($file['error']));
        }
        
        // Verificar tamaño
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('El archivo es demasiado grande. Máximo ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB permitido');
        }
        
        // Verificar tipo de archivo
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        
        if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
            throw new Exception('Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', ALLOWED_IMAGE_TYPES));
        }
        
        // Verificar que sea realmente una imagen
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('El archivo no es una imagen válida');
        }
        
        // Generar nombre único
        $fileName = self::generateUniqueFileName($extension);
        $uploadPath = UPLOAD_PATH . $fileName;
        
        // Crear directorio si no existe
        if (!is_dir(UPLOAD_PATH)) {
            if (!mkdir(UPLOAD_PATH, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de uploads');
            }
        }
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Error al subir el archivo');
        }
        
        // Retornar ruta relativa para guardar en BD
        return 'uploads/' . $fileName;
    }
    
    /**
     * Generar nombre único para archivo
     */
    private static function generateUniqueFileName($extension) {
        return 'camiseta_' . time() . '_' . uniqid() . '.' . $extension;
    }
    
    /**
     * Obtener mensaje de error de subida
     */
    private static function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es demasiado grande';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se subió ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta el directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo en el disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Subida de archivo detenida por extensión';
            default:
                return 'Error desconocido al subir archivo';
        }
    }
    
    /**
     * Eliminar archivo
     */
    public static function deleteFile($filePath) {
        if (!empty($filePath) && file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }
    
    /**
     * Obtener URL completa del archivo
     */
    public static function getFileUrl($filePath) {
        if (empty($filePath)) {
            return null;
        }
        
        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $baseUrl .= $_SERVER['HTTP_HOST'];
        $baseUrl .= str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        
        return $baseUrl . '/' . ltrim($filePath, '/');
    }
}
?>
