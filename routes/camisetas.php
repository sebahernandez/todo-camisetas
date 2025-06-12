<?php
/**
 * Rutas de camisetas
 */

require_once __DIR__ . '/../models/Camiseta.php';
require_once __DIR__ . '/../utils/validator.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../utils/upload.php';

/**
 * @swagger
 * /api/camisetas:
 *   get:
 *     tags:
 *       - Camisetas
 *     summary: Listar camisetas
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
 *           default: 10
 *       - name: search
 *         in: query
 *         schema:
 *           type: string
 *       - name: categoria_id
 *         in: query
 *         schema:
 *           type: integer
 *       - name: marca_id
 *         in: query
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Lista de camisetas
 */
function handleGetCamisetas() {
    try {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(100, intval($_GET['limit'] ?? 10)));
        
        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = trim($_GET['search']);
        }
        if (!empty($_GET['categoria_id'])) {
            $filters['categoria_id'] = intval($_GET['categoria_id']);
        }
        if (!empty($_GET['marca_id'])) {
            $filters['marca_id'] = intval($_GET['marca_id']);
        }
        if (!empty($_GET['talla'])) {
            $filters['talla'] = $_GET['talla'];
        }
        if (!empty($_GET['color'])) {
            $filters['color'] = $_GET['color'];
        }
        if (!empty($_GET['precio_min'])) {
            $filters['precio_min'] = floatval($_GET['precio_min']);
        }
        if (!empty($_GET['precio_max'])) {
            $filters['precio_max'] = floatval($_GET['precio_max']);
        }
        if (isset($_GET['en_stock'])) {
            $filters['en_stock'] = filter_var($_GET['en_stock'], FILTER_VALIDATE_BOOLEAN);
        }
        
        $camisetaModel = new Camiseta();
        $result = $camisetaModel->getAll($page, $limit, $filters);
        
        Response::success($result, 'Camisetas obtenidas exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener camisetas: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/camisetas/{id}:
 *   get:
 *     tags:
 *       - Camisetas
 *     summary: Obtener camiseta por ID
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Camiseta encontrada
 *       404:
 *         description: Camiseta no encontrada
 */
function handleGetCamiseta() {
    try {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de camiseta inválido', 400);
        }
        
        $camisetaModel = new Camiseta();
        $camiseta = $camisetaModel->findById($id);
        
        if (!$camiseta) {
            Response::notFound('Camiseta no encontrada');
        }
        
        Response::success($camiseta, 'Camiseta obtenida exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al obtener camiseta: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/camisetas:
 *   post:
 *     tags:
 *       - Camisetas
 *     summary: Crear nueva camiseta
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         multipart/form-data:
 *           schema:
 *             type: object
 *             required:
 *               - nombre
 *               - precio
 *               - talla
 *               - categoria_id
 *               - marca_id
 *             properties:
 *               nombre:
 *                 type: string
 *               descripcion:
 *                 type: string
 *               precio:
 *                 type: number
 *               talla:
 *                 type: string
 *                 enum: [XS, S, M, L, XL, XXL]
 *               color:
 *                 type: string
 *               stock:
 *                 type: integer
 *               categoria_id:
 *                 type: integer
 *               marca_id:
 *                 type: integer
 *               imagen:
 *                 type: string
 *                 format: binary
 *     responses:
 *       201:
 *         description: Camiseta creada exitosamente
 *       400:
 *         description: Error de validación
 *       401:
 *         description: No autorizado
 */
function handleCreateCamiseta() {
    try {
        // Solo administradores pueden crear camisetas
        AuthMiddleware::requireAdmin();
        
        $data = $_POST;
        
        // Validar datos
        $validator = Validator::make($data);
        
        $validator
            ->required('nombre', 'El nombre es requerido')
            ->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
            ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres')
            ->required('precio', 'El precio es requerido')
            ->numeric('precio', 'El precio debe ser numérico')
            ->min('precio', 0.01, 'El precio debe ser mayor a 0')
            ->required('talla', 'La talla es requerida')
            ->in('talla', ['XS', 'S', 'M', 'L', 'XL', 'XXL'], 'Talla inválida')
            ->required('categoria_id', 'La categoría es requerida')
            ->integer('categoria_id', 'ID de categoría inválido')
            ->required('marca_id', 'La marca es requerida')
            ->integer('marca_id', 'ID de marca inválido');
        
        if (isset($data['stock'])) {
            $validator->integer('stock', 'El stock debe ser un número entero')
                     ->min('stock', 0, 'El stock no puede ser negativo');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        $camisetaModel = new Camiseta();
        
        // Verificar si ya existe una camiseta con el mismo nombre
        if ($camisetaModel->nameExists($data['nombre'])) {
            Response::conflict('Ya existe una camiseta con ese nombre');
        }
        
        // Verificar referencias
        $validation = $camisetaModel->validateReferences($data['categoria_id'], $data['marca_id']);
        if (isset($validation['error'])) {
            Response::error($validation['error'], 400);
        }
        
        // Manejar subida de imagen si existe
        $imagePath = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagePath = Upload::handleImageUpload($_FILES['imagen']);
        }
        
        // Preparar datos para crear
        $createData = [
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'precio' => floatval($data['precio']),
            'talla' => $data['talla'],
            'color' => trim($data['color'] ?? ''),
            'stock' => intval($data['stock'] ?? 0),
            'categoria_id' => intval($data['categoria_id']),
            'marca_id' => intval($data['marca_id']),
            'imagen' => $imagePath
        ];
        
        $camisetaId = $camisetaModel->create($createData);
        
        if (!$camisetaId) {
            Response::error('Error al crear la camiseta', 500);
        }
        
        // Obtener la camiseta creada con sus relaciones
        $camiseta = $camisetaModel->findById($camisetaId);
        
        Response::created($camiseta, 'Camiseta creada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al crear camiseta: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/camisetas/{id}:
 *   put:
 *     tags:
 *       - Camisetas
 *     summary: Actualizar camiseta
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
 *         description: Camiseta actualizada exitosamente
 *       404:
 *         description: Camiseta no encontrada
 */
function handleUpdateCamiseta() {
    try {
        // Solo administradores pueden actualizar camisetas
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de camiseta inválido', 400);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error('Datos JSON inválidos', 400);
        }
        
        $camisetaModel = new Camiseta();
        
        // Verificar que la camiseta existe
        $existingCamiseta = $camisetaModel->findById($id);
        if (!$existingCamiseta) {
            Response::notFound('Camiseta no encontrada');
        }
        
        // Validar datos (solo los campos presentes)
        $validator = Validator::make($input);
        
        if (isset($input['nombre'])) {
            $validator->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
                     ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres');
            
            // Verificar nombre único
            if ($camisetaModel->nameExists($input['nombre'], $id)) {
                Response::conflict('Ya existe otra camiseta con ese nombre');
            }
        }
        
        if (isset($input['precio'])) {
            $validator->numeric('precio', 'El precio debe ser numérico')
                     ->min('precio', 0.01, 'El precio debe ser mayor a 0');
        }
        
        if (isset($input['talla'])) {
            $validator->in('talla', ['XS', 'S', 'M', 'L', 'XL', 'XXL'], 'Talla inválida');
        }
        
        if (isset($input['stock'])) {
            $validator->integer('stock', 'El stock debe ser un número entero')
                     ->min('stock', 0, 'El stock no puede ser negativo');
        }
        
        if (isset($input['categoria_id'])) {
            $validator->integer('categoria_id', 'ID de categoría inválido');
        }
        
        if (isset($input['marca_id'])) {
            $validator->integer('marca_id', 'ID de marca inválido');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        // Verificar referencias si se están actualizando
        if (isset($input['categoria_id']) || isset($input['marca_id'])) {
            $categoriaId = $input['categoria_id'] ?? $existingCamiseta['categoria_id'];
            $marcaId = $input['marca_id'] ?? $existingCamiseta['marca_id'];
            
            $validation = $camisetaModel->validateReferences($categoriaId, $marcaId);
            if (isset($validation['error'])) {
                Response::error($validation['error'], 400);
            }
        }
        
        // Actualizar camiseta
        $updated = $camisetaModel->update($id, $input);
        
        if (!$updated) {
            Response::error('No se realizaron cambios', 400);
        }
        
        // Obtener la camiseta actualizada
        $camiseta = $camisetaModel->findById($id);
        
        Response::success($camiseta, 'Camiseta actualizada exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error al actualizar camiseta: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/camisetas/{id}:
 *   delete:
 *     tags:
 *       - Camisetas
 *     summary: Eliminar camiseta
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
 *         description: Camiseta eliminada exitosamente
 *       404:
 *         description: Camiseta no encontrada
 */
function handleDeleteCamiseta() {
    try {
        // Solo administradores pueden eliminar camisetas
        AuthMiddleware::requireAdmin();
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            Response::error('ID de camiseta inválido', 400);
        }
        
        $camisetaModel = new Camiseta();
        
        // Verificar que la camiseta existe y obtener sus datos antes de eliminar
        $camiseta = $camisetaModel->findById($id);
        if (!$camiseta) {
            Response::notFound('Camiseta no encontrada');
        }
        
        // Eliminar camiseta (soft delete)
        $deleted = $camisetaModel->delete($id);
        
        if (!$deleted) {
            Response::error('Error al eliminar la camiseta', 500);
        }
        
        // Respuesta informativa con los datos de la camiseta eliminada
        Response::success(
            [
                'camiseta_eliminada' => [
                    'id' => $camiseta['id'],
                    'nombre' => $camiseta['nombre'],
                    'precio' => $camiseta['precio'],
                    'talla' => $camiseta['talla'],
                    'color' => $camiseta['color'],
                    'categoria_nombre' => $camiseta['categoria_nombre'],
                    'marca_nombre' => $camiseta['marca_nombre']
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ],
            "Camiseta '{$camiseta['nombre']}' eliminada exitosamente"
        );
        
    } catch (Exception $e) {
        Response::error('Error al eliminar camiseta: ' . $e->getMessage(), 500);
    }
}
?>
