<?php
/**
 * Modelo de Camiseta
 */

require_once __DIR__ . '/../config/database.php';

class Camiseta {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear una nueva camiseta
     */
    public function create($data) {
        $sql = "INSERT INTO camisetas (nombre, descripcion, precio, talla, color, stock, imagen, categoria_id, marca_id, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['precio'],
            $data['talla'],
            $data['color'] ?? null,
            $data['stock'] ?? 0,
            $data['imagen'] ?? null,
            $data['categoria_id'],
            $data['marca_id'],
            $data['activo'] ?? true
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Obtener todas las camisetas con paginación
     */
    public function getAll($page = 1, $limit = 10, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE c.activo = TRUE";
        $params = [];
        
        // Filtros
        if (!empty($filters['search'])) {
            $whereClause .= " AND (c.nombre LIKE ? OR c.descripcion LIKE ?)";
            $searchParam = "%{$filters['search']}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if (!empty($filters['categoria_id'])) {
            $whereClause .= " AND c.categoria_id = ?";
            $params[] = $filters['categoria_id'];
        }
        
        if (!empty($filters['marca_id'])) {
            $whereClause .= " AND c.marca_id = ?";
            $params[] = $filters['marca_id'];
        }
        
        if (!empty($filters['talla'])) {
            $whereClause .= " AND c.talla = ?";
            $params[] = $filters['talla'];
        }
        
        if (!empty($filters['color'])) {
            $whereClause .= " AND c.color LIKE ?";
            $params[] = "%{$filters['color']}%";
        }
        
        if (isset($filters['precio_min'])) {
            $whereClause .= " AND c.precio >= ?";
            $params[] = $filters['precio_min'];
        }
        
        if (isset($filters['precio_max'])) {
            $whereClause .= " AND c.precio <= ?";
            $params[] = $filters['precio_max'];
        }
        
        if (isset($filters['en_stock']) && $filters['en_stock']) {
            $whereClause .= " AND c.stock > 0";
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) as total 
                     FROM camisetas c 
                     INNER JOIN categorias cat ON c.categoria_id = cat.id 
                     INNER JOIN marcas m ON c.marca_id = m.id 
                     $whereClause";
        
        $total = $this->db->selectOne($countSql, $params)['total'];
        
        // Obtener camisetas con relaciones
        $sql = "SELECT c.*, 
                       cat.nombre as categoria_nombre,
                       m.nombre as marca_nombre
                FROM camisetas c
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                INNER JOIN marcas m ON c.marca_id = m.id
                $whereClause
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $camisetas = $this->db->select($sql, $params);
        
        return [
            'camisetas' => $camisetas,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Buscar camiseta por ID
     */
    public function findById($id) {
        $sql = "SELECT c.*, 
                       cat.nombre as categoria_nombre,
                       m.nombre as marca_nombre
                FROM camisetas c
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                INNER JOIN marcas m ON c.marca_id = m.id
                WHERE c.id = ? AND c.activo = TRUE";
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Actualizar camiseta
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = ['nombre', 'descripcion', 'precio', 'talla', 'color', 'stock', 'imagen', 'categoria_id', 'marca_id', 'activo'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE camisetas SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->update($sql, $params);
    }
    
    /**
     * Eliminar camiseta (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE camisetas SET activo = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$id]);
    }
    
    /**
     * Verificar si existe una camiseta con el mismo nombre
     */
    public function nameExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM camisetas WHERE nombre = ? AND activo = TRUE";
        $params = [$nombre];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Verificar si existen las referencias (categoría y marca)
     */
    public function validateReferences($categoria_id, $marca_id) {
        // Verificar categoría
        $categoria = $this->db->selectOne(
            "SELECT id FROM categorias WHERE id = ? AND activo = TRUE", 
            [$categoria_id]
        );
        
        if (!$categoria) {
            return ['error' => 'La categoría especificada no existe o está inactiva'];
        }
        
        // Verificar marca
        $marca = $this->db->selectOne(
            "SELECT id FROM marcas WHERE id = ? AND activo = TRUE", 
            [$marca_id]
        );
        
        if (!$marca) {
            return ['error' => 'La marca especificada no existe o está inactiva'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Actualizar stock
     */
    public function updateStock($id, $cantidad) {
        $sql = "UPDATE camisetas SET stock = stock + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$cantidad, $id]);
    }
    
    /**
     * Obtener estadísticas
     */
    public function getStats() {
        $stats = [];
        
        // Total de camisetas activas
        $total = $this->db->selectOne("SELECT COUNT(*) as count FROM camisetas WHERE activo = TRUE");
        $stats['total_camisetas'] = $total['count'];
        
        // Total por categoría
        $stats['por_categoria'] = $this->db->select(
            "SELECT cat.nombre, COUNT(c.id) as cantidad 
             FROM categorias cat 
             LEFT JOIN camisetas c ON cat.id = c.categoria_id AND c.activo = TRUE 
             WHERE cat.activo = TRUE 
             GROUP BY cat.id, cat.nombre"
        );
        
        // Total por marca
        $stats['por_marca'] = $this->db->select(
            "SELECT m.nombre, COUNT(c.id) as cantidad 
             FROM marcas m 
             LEFT JOIN camisetas c ON m.id = c.marca_id AND c.activo = TRUE 
             WHERE m.activo = TRUE 
             GROUP BY m.id, m.nombre"
        );
        
        // Camisetas con stock bajo (menos de 5)
        $stats['stock_bajo'] = $this->db->select(
            "SELECT nombre, stock FROM camisetas WHERE stock < 5 AND activo = TRUE ORDER BY stock ASC"
        );
        
        return $stats;
    }
}
?>
