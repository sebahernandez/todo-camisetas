<?php
/**
 * Rutas de marcas
 */

require_once __DIR__ . '/../models/Marca.php';
require_once __DIR__ . '/../utils/validator.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/auth.php';

/**
 * @swagger
 * /api/marcas:
 *   get:
 *     tags:
 *       - Marcas
 *     summary: Listar marcas
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
 *         description: Lista de marcas
 */
function handleGetMarcas() {
    try {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(100, intval($_GET['limit'] ?? 50)));
        $search = trim($_GET['search'] ?? '');
        
        $marcaModel = new Marca();
        $result = $marcaModel->getAll($page, $limit, $search);
        
        Response::success($result, 'Marcas obtenidas exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener marcas: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/marcas/{id}:
 *   get:
 *     tags:
 *       - Marcas
 *     summary: Obtener marca por ID
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Marca encontrada
 *       404:
 *         description: Marca no encontrada
 */
function handleGetMarca() {
    try {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de marca inválido', 400);
        }
        
        $marcaModel = new Marca();
        $marca = $marcaModel->findById($id);
        
        if (!$marca) {
            Response::notFound('Marca no encontrada');
        }
        
        Response::success($marca, 'Marca obtenida exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener marca: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/marcas:
 *   post:
 *     tags:
 *       - Marcas
 *     summary: Crear nueva marca
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
 *         description: Marca creada exitosamente
 *       400:
 *         description: Error de validación
 *       401:
 *         description: No autorizado
 */
function handleCreateMarca() {
    try {
        // Solo administradores pueden crear marcas
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
        
        $marcaModel = new Marca();
        
        // Verificar si ya existe una marca con el mismo nombre
        if ($marcaModel->nameExists($input['nombre'])) {
            Response::conflict('Ya existe una marca con ese nombre');
        }
        
        // Crear marca
        $marcaId = $marcaModel->create([
            'nombre' => trim($input['nombre']),
            'descripcion' => trim($input['descripcion'] ?? ''),
            'activo' => $input['activo'] ?? true
        ]);
        
        if (!$marcaId) {
            Response::error('Error al crear la marca', 500);
        }
        
        // Obtener la marca creada
        $marca = $marcaModel->findById($marcaId);
        
        Response::created($marca, 'Marca creada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al crear marca: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/marcas/{id}:
 *   put:
 *     tags:
 *       - Marcas
 *     summary: Actualizar marca
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
 *         description: Marca actualizada exitosamente
 *       404:
 *         description: Marca no encontrada
 */
function handleUpdateMarca() {
    try {
        // Solo administradores pueden actualizar marcas
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de marca inválido', 400);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error('Datos JSON inválidos', 400);
        }
        
        $marcaModel = new Marca();
        
        // Verificar que la marca existe
        $existingMarca = $marcaModel->findById($id);
        if (!$existingMarca) {
            Response::notFound('Marca no encontrada');
        }
        
        // Validar datos (solo los campos presentes)
        $validator = Validator::make($input);
        
        if (isset($input['nombre'])) {
            $validator->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
                     ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres');
            
            // Verificar nombre único
            if ($marcaModel->nameExists($input['nombre'], $id)) {
                Response::conflict('Ya existe otra marca con ese nombre');
            }
        }
        
        if (isset($input['descripcion'])) {
            $validator->maxLength('descripcion', 500, 'La descripción no puede exceder 500 caracteres');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        // Actualizar marca
        $updated = $marcaModel->update($id, $input);
        
        if (!$updated) {
            Response::error('No se realizaron cambios', 400);
        }
        
        // Obtener la marca actualizada
        $marca = $marcaModel->findById($id);
        
        Response::success($marca, 'Marca actualizada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al actualizar marca: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/marcas/{id}:
 *   delete:
 *     tags:
 *       - Marcas
 *     summary: Eliminar marca
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
 *         description: Marca eliminada exitosamente
 *       400:
 *         description: No se puede eliminar (tiene camisetas asociadas)
 *       404:
 *         description: Marca no encontrada
 */
function handleDeleteMarca() {
    try {
        // Solo administradores pueden eliminar marcas
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de marca inválido', 400);
        }
        
        $marcaModel = new Marca();
        
        // Verificar que la marca existe y obtener sus datos antes de eliminar
        $marca = $marcaModel->findById($id);
        if (!$marca) {
            Response::notFound('Marca no encontrada');
        }
        
        // Eliminar marca (verificará si tiene camisetas asociadas)
        $deleted = $marcaModel->delete($id);
        
        if (!$deleted) {
            Response::error('Error al eliminar la marca', 500);
        }
        
        // Respuesta informativa con los datos de la marca eliminada
        Response::success(
            [
                'marca_eliminada' => [
                    'id' => $marca['id'],
                    'nombre' => $marca['nombre'],
                    'descripcion' => $marca['descripcion'],
                    'activo' => $marca['activo']
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ],
            "Marca '{$marca['nombre']}' eliminada exitosamente"
        );
        
    } catch (Exception $e) {
        Response::error('Error al eliminar marca: ' . $e->getMessage(), 400);
    }
}
?>
