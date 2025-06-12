# 🎽 Todo Camisetas API

**API REST completa para gestión de camisetas - Examen Transversal Final**

## 🚀 Inicio Rápido

### 📋 Requisitos

- MAMP/XAMPP con PHP 7.4+
- MySQL 5.7+
- Puerto Apache: 8888 (MAMP default)

### ⚡ Configuración (5 minutos)

1. **Configurar base de datos:**

   ```bash
   # Importar en phpMyAdmin o MySQL:
   database/schema.sql
   ```

2. **Variables de entorno:**

   ```bash
   cp .env.example .env
   # Editar .env con tus credenciales de BD
   ```

   > ⚠️ **Seguridad**: El archivo `.env` contiene credenciales y está excluido del control de versiones.
   > Nunca subas este archivo a GitHub o expongas sus valores en el código.

3. **Iniciar MAMP:** Apache y MySQL

### 🌐 URLs de Acceso

- **API:** http://localhost:8888/todo-camisetas/api.php
- **Swagger UI:** http://localhost:8888/todo-camisetas/swagger/
- **Swagger Alternativa:** http://localhost:8888/todo-camisetas/swagger.php
- **Panel de Pruebas:** http://localhost:8888/todo-camisetas/panel_pruebas.html

### 👤 Credenciales de Prueba

**Admin preconfigurado:**

- Email: `demo@swagger.com`
- Password: `Demo123`

## 🧪 Pruebas Rápidas

### 1. Verificar Estado de la API

```bash
curl "http://localhost:8888/todo-camisetas/api.php?path=status"
```

### 2. Login y Obtener Token

```bash
curl -X POST "http://localhost:8888/todo-camisetas/api.php?path=auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@swagger.com","password":"Demo123"}'
```

### 3. Listar Camisetas (público)

```bash
curl "http://localhost:8888/todo-camisetas/api.php?path=camisetas"
```

### 4. Crear Camiseta (requiere auth)

```bash
# Usar el token obtenido en paso 2
curl -X POST "http://localhost:8888/todo-camisetas/api.php?path=camisetas" \
  -H "X-API-Token: TU_JWT_TOKEN_AQUI" \
  -F "nombre=Test Camiseta" \
  -F "precio=29990" \
  -F "talla=M" \
  -F "categoria_id=1" \
  -F "marca_id=1"
```

## 🔐 Autenticación

### Problema Común en MAMP/XAMPP

El header `Authorization: Bearer` puede no funcionar. **Solución:**

**✅ Usar header alternativo:**

```bash
-H "X-API-Token: tu_jwt_token"
```

**✅ O query parameter:**

```bash
"?token=tu_jwt_token"
```

### Endpoints de Autenticación

- `POST /auth/register` - Registrar usuario
- `POST /auth/login` - Iniciar sesión
- Solo usuarios `admin` pueden crear/editar/eliminar

## 🗂️ Endpoints Principales

### Públicos (GET)

- `GET /camisetas` - Listar camisetas
- `GET /camisetas/{id}` - Obtener camiseta por ID
- `GET /categorias` - Listar categorías
- `GET /marcas` - Listar marcas

### Protegidos (requieren auth)

- `POST /camisetas` - Crear camiseta (admin)
- `PUT /camisetas/{id}` - Actualizar camiseta (admin)
- `DELETE /camisetas/{id}` - Eliminar camiseta (admin)
- Similar para categorías y marcas

## 📋 Swagger UI

**Documentación interactiva completa en:**
http://localhost:8888/todo-camisetas/swagger/

### Uso de Swagger:

1. Haz clic en **"Authorize"** 🔒
2. Inicia sesión con: `demo@swagger.com` / `Demo123`
3. Pega el token JWT obtenido
4. Prueba cualquier endpoint interactivamente

## 📮 Guía Completa de Postman

### 🔧 Configuración Inicial

1. **Crear Environment "Todo Camisetas":**

   - `base_url`: `http://localhost:8888/todo-camisetas/api.php`
   - `token`: (se llenará automáticamente)

2. **Script Post-Request para Login:**

```javascript
if (pm.response.code === 200) {
  const response = pm.response.json();
  if (response.success && response.data.token) {
    pm.environment.set("token", response.data.token);
  }
}
```

### 🔐 Autenticación

#### 1. Registrar Usuario

- **Method:** `POST`
- **URL:** `{{base_url}}?path=auth/register`
- **Headers:**
  ```
  Content-Type: application/json
  ```
- **Body (JSON):**
  ```json
  {
    "email": "nuevo@usuario.com",
    "password": "Password123",
    "nombre": "Usuario Nuevo",
    "rol": "user"
  }
  ```

#### 2. Iniciar Sesión

- **Method:** `POST`
- **URL:** `{{base_url}}?path=auth/login`
- **Headers:**
  ```
  Content-Type: application/json
  ```
- **Body (JSON):**
  ```json
  {
    "email": "demo@swagger.com",
    "password": "Demo123"
  }
  ```

### 👕 Gestión de Camisetas

#### 3. Listar Camisetas (Público)

- **Method:** `GET`
- **URL:** `{{base_url}}?path=camisetas`
- **Query Parameters (opcionales):**
  ```
  page: 1
  limit: 10
  search: nike
  categoria_id: 1
  marca_id: 1
  talla: M
  color: Negro
  ```

#### 4. Obtener Camiseta por ID

- **Method:** `GET`
- **URL:** `{{base_url}}?path=camisetas/1`

#### 5. Crear Camiseta (Admin)

- **Method:** `POST`
- **URL:** `{{base_url}}?path=camisetas`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Body (form-data):**
  ```
  nombre: Camiseta Nueva
  descripcion: Descripción de la camiseta
  precio: 39990
  talla: L
  color: Azul
  stock: 25
  categoria_id: 1
  marca_id: 1
  imagen: [archivo de imagen]
  ```

#### 6. Actualizar Camiseta (Admin)

- **Method:** `PUT`
- **URL:** `{{base_url}}?path=camisetas/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  Content-Type: application/x-www-form-urlencoded  # o application/json
  ```
- **Body (x-www-form-urlencoded):**
  ```
  nombre: Camiseta Actualizada
  precio: 45990
  stock: 30
  ```
- **O Body (JSON):**
  ```json
  {
    "nombre": "Camiseta Actualizada",
    "precio": 45990,
    "stock": 30
  }
  ```

#### 7. Eliminar Camiseta (Admin)

- **Method:** `DELETE`
- **URL:** `{{base_url}}?path=camisetas/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Response exitosa:**
  ```json
  {
    "success": true,
    "message": "Camiseta 'Camiseta Premium 2025' eliminada exitosamente",
    "data": {
      "camiseta_eliminada": {
        "id": 13,
        "nombre": "Camiseta Premium 2025",
        "precio": "49990.00",
        "talla": "L",
        "color": "Negro",
        "categoria_nombre": "Deportivas",
        "marca_nombre": "Nike"
      },
      "timestamp": "2025-06-12 12:56:32"
    }
  }
  ```

### 🏷️ Gestión de Categorías

#### 8. Listar Categorías

- **Method:** `GET`
- **URL:** `{{base_url}}?path=categorias`

#### 9. Crear Categoría (Admin)

- **Method:** `POST`
- **URL:** `{{base_url}}?path=categorias`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Body (form-data):**
  ```
  nombre: Nueva Categoría
  descripcion: Descripción de la categoría
  ```

#### 10. Actualizar Categoría (Admin)

- **Method:** `PUT`
- **URL:** `{{base_url}}?path=categorias/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  Content-Type: application/x-www-form-urlencoded  # o application/json
  ```
- **Body (x-www-form-urlencoded):**
  ```
  nombre: Categoría Actualizada
  descripcion: Nueva descripción
  ```
- **O Body (JSON):**
  ```json
  {
    "nombre": "Categoría Actualizada",
    "descripcion": "Nueva descripción"
  }
  ```

#### 11. Eliminar Categoría (Admin)

- **Method:** `DELETE`
- **URL:** `{{base_url}}?path=categorias/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Response exitosa:**
  ```json
  {
    "success": true,
    "message": "Categoría 'Test Categoria' eliminada exitosamente",
    "data": {
      "categoria_eliminada": {
        "id": 6,
        "nombre": "Test Categoria",
        "descripcion": "Categoria de prueba para eliminar",
        "total_camisetas": 0
      },
      "timestamp": "2025-06-12 12:57:45"
    }
  }
  ```

### 🏢 Gestión de Marcas

#### 11. Listar Marcas

- **Method:** `GET`
- **URL:** `{{base_url}}?path=marcas`

#### 12. Crear Marca (Admin)

- **Method:** `POST`
- **URL:** `{{base_url}}?path=marcas`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Body (form-data):**
  ```
  nombre: Nueva Marca
  descripcion: Descripción de la marca
  ```

#### 13. Actualizar Marca (Admin)

- **Method:** `PUT`
- **URL:** `{{base_url}}?path=marcas/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  Content-Type: application/x-www-form-urlencoded  # o application/json
  ```
- **Body (x-www-form-urlencoded):**
  ```
  nombre: Marca Actualizada
  descripcion: Nueva descripción de la marca
  ```
- **O Body (JSON):**
  ```json
  {
    "nombre": "Marca Actualizada",
    "descripcion": "Nueva descripción de la marca"
  }
  ```

#### 14. Eliminar Marca (Admin)

- **Method:** `DELETE`
- **URL:** `{{base_url}}?path=marcas/1`
- **Headers:**
  ```
  X-API-Token: {{token}}
  ```
- **Response exitosa:**
  ```json
  {
    "success": true,
    "message": "Marca 'Test Marca' eliminada exitosamente",
    "data": {
      "marca_eliminada": {
        "id": 8,
        "nombre": "Test Marca",
        "descripcion": "Marca de prueba para eliminar",
        "activo": 1
      },
      "timestamp": "2025-06-12 12:57:20"
    }
  }
  ```

### ⚙️ Endpoints de Sistema

#### 15. Estado de la API

- **Method:** `GET`
- **URL:** `{{base_url}}?path=status`

#### 16. Test Endpoint

- **Method:** `GET`
- **URL:** `{{base_url}}?path=test`

### 🚨 Solución de Problemas en Postman

#### Error "Token de acceso requerido"

**Solución:** Usar header `X-API-Token` en lugar de `Authorization: Bearer`

#### Error de validación

**Verificar:** Content-Type correcto y campos requeridos

#### Error 404

**Verificar:** URL correcta con parámetro `path`

### 💡 Tips para Postman

1. **Usar Variables:** `{{base_url}}` y `{{token}}`
2. **Scripts de Post-Request:** Para guardar tokens automáticamente
3. **Tests:** Agregar validaciones de respuesta
4. **Collections:** Organizar endpoints por funcionalidad
5. **Environments:** Separar desarrollo, testing y producción

### 📦 Archivos de Importación

**Para importar rápidamente en Postman:**

1. **Collection:** `postman_collection.json` - Todos los endpoints configurados
2. **Environment:** `postman_environment.json` - Variables pre-configuradas

**Pasos de importación:**

1. Abrir Postman
2. Import → Subir archivos `postman_collection.json` y `postman_environment.json`
3. Seleccionar environment "Todo Camisetas Environment"
4. Ejecutar "Iniciar Sesión" para obtener token automáticamente
5. ¡Listo para usar todos los endpoints!

## 🏗️ Estructura del Proyecto

```
todo-camisetas/
├── api.php              # Router principal
├── swagger.php          # Acceso a Swagger UI
├── config/              # Configuración
├── database/            # Schema SQL
├── models/              # Modelos de datos
├── routes/              # Definición de rutas
├── middleware/          # Autenticación
├── utils/              # Utilidades (JWT, validación)
└── views/              # Swagger UI
```

## ✅ Estado del Proyecto

**🎯 COMPLETADO Y FUNCIONAL**

- ✅ API REST completa con CRUD
- ✅ Autenticación JWT robusta
- ✅ Swagger UI operativo
- ✅ Base de datos con datos de prueba
- ✅ Manejo de archivos/imágenes
- ✅ Validación completa de datos
- ✅ Paginación y filtros
- ✅ Manejo de errores

## 🐛 Solución de Problemas

### Error "Token de acceso requerido"

➡️ Usar `X-API-Token` en lugar de `Authorization: Bearer`

### Error 500 en Swagger

➡️ Swagger UI está en `/swagger/` (con slash final)

### Base de datos no conecta

➡️ Verificar credenciales en `.env`

## 🔒 Seguridad

### Credenciales y Variables Sensibles

- **Archivo `.env`**: Contiene todas las credenciales y nunca debe subirse a repositorios. Está incluido en `.gitignore`.
- **JWT Secret**: Genera un valor aleatorio y seguro en el archivo `.env`.
- **Contraseñas**: Los hashes de contraseñas nunca deben estar hardcodeados en el código fuente.

### Configuración Para Producción

Antes de desplegar en producción:

1. **Generar un JWT Secret fuerte**: Mínimo 32 caracteres aleatorios.
2. **Crear usuarios manualmente**: No usar usuarios por defecto.
3. **Definir `PRODUCTION=true`**: Activa las protecciones adicionales.
4. **Contraseñas de BD**: Usar usuarios de BD con privilegios limitados.
5. **HTTPS**: Configurar siempre conexiones seguras.

### Usuario Administrador

```sql
-- Ejecutar después de importar schema.sql para crear un usuario admin
INSERT INTO usuarios (email, password, nombre, rol) VALUES
('tu_email@ejemplo.com', 'HASH_DE_PASSWORD_SEGURO', 'Administrador', 'admin');

-- Para generar el hash seguro desde PHP:
-- $hash = password_hash("ContraseñaSegura123", PASSWORD_DEFAULT);
```

---

**🏆 Proyecto Final - Desarrollo Full Stack PHP/MySQL**  
**Fecha:** Junio 2025
