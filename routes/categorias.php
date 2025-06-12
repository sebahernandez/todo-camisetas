<?php
/**
 * Rutas de categorías
 */

require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../utils/validator.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/auth.php';

/**
 * @swagger
 * /api/categorias:
 *   get:
 *     tags:
 *       - Categorías
 *     summary: Listar categorías
 *     parameters:
 *       - name: page
 *         in: query
 *         schema:
 *           type: integer
 *           default: 1
 *       - name: limit
 *         in: query
 *         schema:
 *           type: integer
 *           default: 50
 *       - name: search
 *         in: query
 *         schema:
 *           type: string
 *     responses:
 *       200:
 *         description: Lista de categorías
 */
function handleGetCategorias() {
    try {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(100, intval($_GET['limit'] ?? 50)));
        $search = trim($_GET['search'] ?? '');
        
        $categoriaModel = new Categoria();
        $result = $categoriaModel->getAll($page, $limit, $search);
        
        Response::success($result, 'Categorías obtenidas exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener categorías: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/categorias/{id}:
 *   get:
 *     tags:
 *       - Categorías
 *     summary: Obtener categoría por ID
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Categoría encontrada
 *       404:
 *         description: Categoría no encontrada
 */
function handleGetCategoria() {
    try {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de categoría inválido', 400);
        }
        
        $categoriaModel = new Categoria();
        $categoria = $categoriaModel->findById($id);
        
        if (!$categoria) {
            Response::notFound('Categoría no encontrada');
        }
        
        Response::success($categoria, 'Categoría obtenida exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener categoría: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/categorias:
 *   post:
 *     tags:
 *       - Categorías
 *     summary: Crear nueva categoría
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             required:
 *               - nombre
 *             properties:
 *               nombre:
 *                 type: string
 *               descripcion:
 *                 type: string
 *               activo:
 *                 type: boolean
 *                 default: true
 *     responses:
 *       201:
 *         description: Categoría creada exitosamente
 *       400:
 *         description: Error de validación
 *       401:
 *         description: No autorizado
 */
function handleCreateCategoria() {
    try {
        // Solo administradores pueden crear categorías
        AuthMiddleware::requireAdmin();
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error('Datos JSON inválidos', 400);
        }
        
        // Validar datos
        $validator = Validator::make($input);
        
        $validator
            ->required('nombre', 'El nombre es requerido')
            ->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
            ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres');
        
        if (isset($input['descripcion'])) {
            $validator->maxLength('descripcion', 500, 'La descripción no puede exceder 500 caracteres');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        $categoriaModel = new Categoria();
        
        // Verificar si ya existe una categoría con el mismo nombre
        if ($categoriaModel->nameExists($input['nombre'])) {
            Response::conflict('Ya existe una categoría con ese nombre');
        }
        
        // Crear categoría
        $categoriaId = $categoriaModel->create([
            'nombre' => trim($input['nombre']),
            'descripcion' => trim($input['descripcion'] ?? ''),
            'activo' => $input['activo'] ?? true
        ]);
        
        if (!$categoriaId) {
            Response::error('Error al crear la categoría', 500);
        }
        
        // Obtener la categoría creada
        $categoria = $categoriaModel->findById($categoriaId);
        
        Response::created($categoria, 'Categoría creada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al crear categoría: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/categorias/{id}:
 *   put:
 *     tags:
 *       - Categorías
 *     summary: Actualizar categoría
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Categoría actualizada exitosamente
 *       404:
 *         description: Categoría no encontrada
 */
function handleUpdateCategoria() {
    try {
        // Solo administradores pueden actualizar categorías
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de categoría inválido', 400);
        }
        
        // Intentar obtener datos de diferentes fuentes (JSON o form-data)
        $input = [];
        
        // Opción 1: Intentar leer JSON del body
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        if ($jsonInput) {
            $input = $jsonInput;
        }
        // Opción 2: Si no hay JSON válido, intentar leer form-data
        else if (!empty($_POST)) {
            $input = $_POST;
        }
        // Opción 3: Si no hay datos, intentar leer PUT vars manualmente
        else {
            parse_str(file_get_contents("php://input"), $putVars);
            if (!empty($putVars)) {
                $input = $putVars;
            }
        }
        
        if (empty($input)) {
            Response::error('No se recibieron datos para actualizar (soporta JSON o form-data)', 400);
        }
        
        $categoriaModel = new Categoria();
        
        // Verificar que la categoría existe
        $existingCategoria = $categoriaModel->findById($id);
        if (!$existingCategoria) {
            Response::notFound('Categoría no encontrada');
        }
        
        // Validar datos (solo los campos presentes)
        $validator = Validator::make($input);
        
        if (isset($input['nombre'])) {
            $validator->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
                     ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres');
            
            // Verificar nombre único
            if ($categoriaModel->nameExists($input['nombre'], $id)) {
                Response::conflict('Ya existe otra categoría con ese nombre');
            }
        }
        
        if (isset($input['descripcion'])) {
            $validator->maxLength('descripcion', 500, 'La descripción no puede exceder 500 caracteres');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        // Actualizar categoría
        $updated = $categoriaModel->update($id, $input);
        
        if (!$updated) {
            Response::error('No se realizaron cambios', 400);
        }
        
        // Obtener la categoría actualizada
        $categoria = $categoriaModel->findById($id);
        
        Response::success($categoria, 'Categoría actualizada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al actualizar categoría: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/categorias/{id}:
 *   delete:
 *     tags:
 *       - Categorías
 *     summary: Eliminar categoría
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *     responses:
 *       204:
 *         description: Categoría eliminada exitosamente
 *       400:
 *         description: No se puede eliminar (tiene camisetas asociadas)
 *       404:
 *         description: Categoría no encontrada
 */
function handleDeleteCategoria() {
    try {
        // Solo administradores pueden eliminar categorías
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de categoría inválido', 400);
        }
        
        $categoriaModel = new Categoria();
        
        // Verificar que la categoría existe y obtener sus datos antes de eliminar
        $categoria = $categoriaModel->findById($id);
        if (!$categoria) {
            Response::notFound('Categoría no encontrada');
        }
        
        // Eliminar categoría (verificará si tiene camisetas asociadas)
        $deleted = $categoriaModel->delete($id);
        
        if (!$deleted) {
            Response::error('Error al eliminar la categoría', 500);
        }
        
        // Respuesta informativa con los datos de la categoría eliminada
        Response::success(
            [
                'categoria_eliminada' => [
                    'id' => $categoria['id'],
                    'nombre' => $categoria['nombre'],
                    'descripcion' => $categoria['descripcion'],
                    'total_camisetas' => $categoria['total_camisetas'] ?? 0
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ],
            "Categoría '{$categoria['nombre']}' eliminada exitosamente"
        );
        
    } catch (Exception $e) {
        Response::error('Error al eliminar categoría: ' . $e->getMessage(), 400);
    }
}
?>
