<?php
/**
 * Modelo de Usuario
 */

require_once __DIR__ . '/../config/database.php';

class User {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (email, password, nombre, rol) VALUES (?, ?, ?, ?)";
        $params = [
            $data['email'],
            $hashedPassword,
            $data['nombre'],
            $data['rol'] ?? 'user'
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = TRUE";
        return $this->db->selectOne($sql, [$email]);
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $sql = "SELECT id, email, nombre, rol, activo, created_at FROM usuarios WHERE id = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Verificar contraseña
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Actualizar último login
     */
    public function updateLastLogin($id) {
        $sql = "UPDATE usuarios SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$id]);
    }
    
    /**
     * Verificar si email existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Listar usuarios (solo para admin)
     */
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE activo = TRUE";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (nombre LIKE ? OR email LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) as total FROM usuarios $whereClause";
        $total = $this->db->selectOne($countSql, $params)['total'];
        
        // Obtener usuarios
        $sql = "SELECT id, email, nombre, rol, created_at 
                FROM usuarios 
                $whereClause 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $users = $this->db->select($sql, $params);
        
        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $params[] = $data['nombre'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($data['rol'])) {
            $fields[] = "rol = ?";
            $params[] = $data['rol'];
        }
        
        if (isset($data['activo'])) {
            $fields[] = "activo = ?";
            $params[] = $data['activo'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->update($sql, $params);
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE usuarios SET activo = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$id]);
    }
}
?>
