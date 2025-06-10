<?php
/**
 * Modelo de Categoría
 */

require_once __DIR__ . '/../config/database.php';

class Categoria {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear una nueva categoría
     */
    public function create($data) {
        $sql = "INSERT INTO categorias (nombre, descripcion, activo) VALUES (?, ?, ?)";
        $params = [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['activo'] ?? true
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getAll($page = 1, $limit = 50, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE c.activo = TRUE";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (c.nombre LIKE ? OR c.descripcion LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) as total FROM categorias c $whereClause";
        $total = $this->db->selectOne($countSql, $params)['total'];
        
        // Obtener categorías con conteo de camisetas
        $sql = "SELECT c.*, 
                       COUNT(cam.id) as total_camisetas
                FROM categorias c
                LEFT JOIN camisetas cam ON c.id = cam.categoria_id AND cam.activo = TRUE
                $whereClause
                GROUP BY c.id
                ORDER BY c.nombre ASC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $categorias = $this->db->select($sql, $params);
        
        return [
            'categorias' => $categorias,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Buscar categoría por ID
     */
    public function findById($id) {
        $sql = "SELECT c.*, 
                       COUNT(cam.id) as total_camisetas
                FROM categorias c
                LEFT JOIN camisetas cam ON c.id = cam.categoria_id AND cam.activo = TRUE
                WHERE c.id = ? AND c.activo = TRUE
                GROUP BY c.id";
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Actualizar categoría
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $params[] = $data['nombre'];
        }
        
        if (isset($data['descripcion'])) {
            $fields[] = "descripcion = ?";
            $params[] = $data['descripcion'];
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
        
        $sql = "UPDATE categorias SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->update($sql, $params);
    }
    
    /**
     * Eliminar categoría (soft delete)
     */
    public function delete($id) {
        // Verificar si tiene camisetas asociadas
        $camisetas = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM camisetas WHERE categoria_id = ? AND activo = TRUE",
            [$id]
        );
        
        if ($camisetas['count'] > 0) {
            throw new Exception("No se puede eliminar la categoría porque tiene {$camisetas['count']} camisetas asociadas");
        }
        
        $sql = "UPDATE categorias SET activo = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$id]);
    }
    
    /**
     * Verificar si existe una categoría con el mismo nombre
     */
    public function nameExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM categorias WHERE nombre = ? AND activo = TRUE";
        $params = [$nombre];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Obtener categorías para select (formato simple)
     */
    public function getForSelect() {
        $sql = "SELECT id, nombre FROM categorias WHERE activo = TRUE ORDER BY nombre ASC";
        return $this->db->select($sql);
    }
}
?>
