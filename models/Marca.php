<?php
/**
 * Modelo de Marca
 */

require_once __DIR__ . '/../config/database.php';

class Marca {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear una nueva marca
     */
    public function create($data) {
        $sql = "INSERT INTO marcas (nombre, descripcion, activo) VALUES (?, ?, ?)";
        $params = [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['activo'] ?? true
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Obtener todas las marcas
     */
    public function getAll($page = 1, $limit = 50, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE m.activo = TRUE";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (m.nombre LIKE ? OR m.descripcion LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) as total FROM marcas m $whereClause";
        $total = $this->db->selectOne($countSql, $params)['total'];
        
        // Obtener marcas con conteo de camisetas
        $sql = "SELECT m.*, 
                       COUNT(cam.id) as total_camisetas
                FROM marcas m
                LEFT JOIN camisetas cam ON m.id = cam.marca_id AND cam.activo = TRUE
                $whereClause
                GROUP BY m.id
                ORDER BY m.nombre ASC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $marcas = $this->db->select($sql, $params);
        
        return [
            'marcas' => $marcas,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Buscar marca por ID
     */
    public function findById($id) {
        $sql = "SELECT m.*, 
                       COUNT(cam.id) as total_camisetas
                FROM marcas m
                LEFT JOIN camisetas cam ON m.id = cam.marca_id AND cam.activo = TRUE
                WHERE m.id = ? AND m.activo = TRUE
                GROUP BY m.id";
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Actualizar marca
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
        
        $sql = "UPDATE marcas SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->update($sql, $params);
    }
    
    /**
     * Eliminar marca (soft delete)
     */
    public function delete($id) {
        // Verificar si tiene camisetas asociadas
        $camisetas = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM camisetas WHERE marca_id = ? AND activo = TRUE",
            [$id]
        );
        
        if ($camisetas['count'] > 0) {
            throw new Exception("No se puede eliminar la marca porque tiene {$camisetas['count']} camisetas asociadas");
        }
        
        $sql = "UPDATE marcas SET activo = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$id]);
    }
    
    /**
     * Verificar si existe una marca con el mismo nombre
     */
    public function nameExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM marcas WHERE nombre = ? AND activo = TRUE";
        $params = [$nombre];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Obtener marcas para select (formato simple)
     */
    public function getForSelect() {
        $sql = "SELECT id, nombre FROM marcas WHERE activo = TRUE ORDER BY nombre ASC";
        return $this->db->select($sql);
    }
}
?>
