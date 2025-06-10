<?php
/**
 * Utilidades para validación de datos
 */

class Validator {
    
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * Validar que un campo sea requerido
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field][] = $message ?? "El campo $field es requerido";
        }
        return $this;
    }
    
    /**
     * Validar email
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message ?? "El campo $field debe ser un email válido";
        }
        return $this;
    }
    
    /**
     * Validar longitud mínima
     */
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = $message ?? "El campo $field debe tener al menos $min caracteres";
        }
        return $this;
    }
    
    /**
     * Validar longitud máxima
     */
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = $message ?? "El campo $field no puede tener más de $max caracteres";
        }
        return $this;
    }
    
    /**
     * Validar que sea numérico
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = $message ?? "El campo $field debe ser numérico";
        }
        return $this;
    }
    
    /**
     * Validar que sea un entero
     */
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = $message ?? "El campo $field debe ser un número entero";
        }
        return $this;
    }
    
    /**
     * Validar valor mínimo
     */
    public function min($field, $min, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field]) && $this->data[$field] < $min) {
            $this->errors[$field][] = $message ?? "El campo $field debe ser mayor o igual a $min";
        }
        return $this;
    }
    
    /**
     * Validar valor máximo
     */
    public function max($field, $max, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field]) && $this->data[$field] > $max) {
            $this->errors[$field][] = $message ?? "El campo $field debe ser menor o igual a $max";
        }
        return $this;
    }
    
    /**
     * Validar que esté en una lista de valores
     */
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $valuesList = implode(', ', $values);
            $this->errors[$field][] = $message ?? "El campo $field debe ser uno de: $valuesList";
        }
        return $this;
    }
    
    /**
     * Validar formato de contraseña
     */
    public function password($field, $message = null) {
        if (isset($this->data[$field])) {
            $password = $this->data[$field];
            if (strlen($password) < 6) {
                $this->errors[$field][] = "La contraseña debe tener al menos 6 caracteres";
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $this->errors[$field][] = "La contraseña debe contener al menos una mayúscula";
            }
            if (!preg_match('/[a-z]/', $password)) {
                $this->errors[$field][] = "La contraseña debe contener al menos una minúscula";
            }
            if (!preg_match('/[0-9]/', $password)) {
                $this->errors[$field][] = "La contraseña debe contener al menos un número";
            }
        }
        return $this;
    }
    
    /**
     * Verificar si hay errores
     */
    public function fails() {
        return !empty($this->errors);
    }
    
    /**
     * Obtener errores
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Obtener datos validados (sin campos no validados)
     */
    public function validated($fields = null) {
        if ($fields === null) {
            return $this->data;
        }
        
        $validated = [];
        foreach ($fields as $field) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }
        
        return $validated;
    }
    
    /**
     * Crear una nueva instancia de validador
     */
    public static function make($data) {
        return new self($data);
    }
}
?>
