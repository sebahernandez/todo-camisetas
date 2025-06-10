<?php
/**
 * Configuración de la base de datos
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Ejecutar una consulta SELECT
     */
    public function select($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta SELECT: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecutar una consulta SELECT que retorna un solo registro
     */
    public function selectOne($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta SELECT: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecutar una consulta INSERT
     */
    public function insert($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta INSERT: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecutar una consulta UPDATE
     */
    public function update($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta UPDATE: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecutar una consulta DELETE
     */
    public function delete($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta DELETE: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecutar múltiples consultas en una transacción
     */
    public function transaction($queries) {
        try {
            $this->connection->beginTransaction();
            
            $results = [];
            foreach ($queries as $query) {
                $stmt = $this->connection->prepare($query['sql']);
                $stmt->execute($query['params'] ?? []);
                $results[] = $stmt->rowCount();
            }
            
            $this->connection->commit();
            return $results;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw new Exception("Error en transacción: " . $e->getMessage());
        }
    }
}
?>
