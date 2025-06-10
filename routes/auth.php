<?php
/**
 * Rutas de autenticación
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/validator.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/jwt.php';

/**
 * @swagger
 * /api/auth/register:
 *   post:
 *     tags:
 *       - Autenticación
 *     summary: Registrar nuevo usuario
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             required:
 *               - email
 *               - password
 *               - nombre
 *             properties:
 *               email:
 *                 type: string
 *                 format: email
 *               password:
 *                 type: string
 *                 minLength: 6
 *               nombre:
 *                 type: string
 *               rol:
 *                 type: string
 *                 enum: [user, admin]
 *                 default: user
 *     responses:
 *       201:
 *         description: Usuario registrado exitosamente
 *       400:
 *         description: Error de validación
 *       409:
 *         description: El email ya está registrado
 */
function handleRegister() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error('Datos JSON inválidos', 400);
        }
        
        // Validar datos
        $validator = Validator::make($input);
        
        $validator
            ->required('email', 'El email es requerido')
            ->email('email', 'El email debe ser válido')
            ->required('password', 'La contraseña es requerida')
            ->password('password')
            ->required('nombre', 'El nombre es requerido')
            ->minLength('nombre', 2, 'El nombre debe tener al menos 2 caracteres')
            ->maxLength('nombre', 255, 'El nombre no puede exceder 255 caracteres');
        
        if (isset($input['rol'])) {
            $validator->in('rol', ['user', 'admin'], 'El rol debe ser user o admin');
        }
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        $userModel = new User();
        
        // Verificar si el email ya existe
        if ($userModel->emailExists($input['email'])) {
            Response::conflict('El email ya está registrado');
        }
        
        // Crear usuario
        $userId = $userModel->create([
            'email' => trim($input['email']),
            'password' => $input['password'],
            'nombre' => trim($input['nombre']),
            'rol' => $input['rol'] ?? 'user'
        ]);
        
        if (!$userId) {
            Response::error('Error al crear el usuario', 500);
        }
        
        // Obtener datos del usuario creado
        $user = $userModel->findById($userId);
        
        // Generar token JWT
        $token = JWT::encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'rol' => $user['rol']
        ]);
        
        Response::created([
            'user' => $user,
            'token' => $token
        ], 'Usuario registrado exitosamente');
        
    } catch (Exception $e) {
        Response::error('Error en el registro: ' . $e->getMessage(), 500);
    }
}

/**
 * @swagger
 * /api/auth/login:
 *   post:
 *     tags:
 *       - Autenticación
 *     summary: Iniciar sesión
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             required:
 *               - email
 *               - password
 *             properties:
 *               email:
 *                 type: string
 *                 format: email
 *               password:
 *                 type: string
 *     responses:
 *       200:
 *         description: Login exitoso
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 success:
 *                   type: boolean
 *                 message:
 *                   type: string
 *                 data:
 *                   type: object
 *                   properties:
 *                     user:
 *                       $ref: '#/components/schemas/User'
 *                     token:
 *                       type: string
 *       401:
 *         description: Credenciales inválidas
 */
function handleLogin() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            Response::error('Datos JSON inválidos', 400);
        }
        
        // Validar datos
        $validator = Validator::make($input);
        
        $validator
            ->required('email', 'El email es requerido')
            ->email('email', 'El email debe ser válido')
            ->required('password', 'La contraseña es requerida');
        
        if ($validator->fails()) {
            Response::validation($validator->getErrors());
        }
        
        $userModel = new User();
        
        // Buscar usuario por email
        $user = $userModel->findByEmail($input['email']);
        
        if (!$user) {
            Response::unauthorized('Credenciales inválidas');
        }
        
        // Verificar contraseña
        if (!$userModel->verifyPassword($input['password'], $user['password'])) {
            Response::unauthorized('Credenciales inválidas');
        }
        
        // Actualizar último login
        $userModel->updateLastLogin($user['id']);
        
        // Remover password del objeto usuario
        unset($user['password']);
        
        // Generar token JWT
        $token = JWT::encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'rol' => $user['rol']
        ]);
        
        Response::success([
            'user' => $user,
            'token' => $token
        ], 'Login exitoso');
        
    } catch (Exception $e) {
        Response::error('Error en el login: ' . $e->getMessage(), 500);
    }
}
?>
