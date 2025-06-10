# Instalación y Configuración de Todo Camisetas API

## Requisitos previos

1. **MAMP/XAMPP** con PHP 7.4+ y MySQL 5.7+
2. **Composer** (opcional, para futuras dependencias)

## Pasos de instalación

### 1. Preparar la base de datos

1. Iniciar MAMP/XAMPP
2. Abrir phpMyAdmin (http://localhost/phpMyAdmin)
3. Ejecutar el script `database/schema.sql` para crear la base de datos y las tablas

### 2. Configurar la aplicación

1. Copiar `.env.example` a `.env`
2. Editar `.env` con la configuración de tu base de datos:
   ```
   DB_HOST=localhost
   DB_NAME=todo_camisetas
   DB_USER=root
   DB_PASS=tu_password_mysql
   ```

### 3. Configurar permisos

Asegurar que la carpeta `uploads/` tenga permisos de escritura:

```bash
chmod 755 uploads/
```

### 4. Probar la instalación

1. Abrir navegador en: `http://localhost/todo-camisetas/`
2. Deberías ver la página de bienvenida de la API
3. Probar la documentación en: `http://localhost/todo-camisetas/swagger/`

## Datos de prueba

La base de datos incluye:

- Usuario administrador: `admin@todocamisetas.com` / `admin123`
- 5 categorías de ejemplo
- 7 marcas de ejemplo
- 10 camisetas de ejemplo

## Endpoints principales

### Autenticación

- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Iniciar sesión

### Camisetas (requiere autenticación para crear/editar/eliminar)

- `GET /api/camisetas` - Listar camisetas
- `GET /api/camisetas/{id}` - Obtener camiseta específica
- `POST /api/camisetas` - Crear camiseta (admin)
- `PUT /api/camisetas/{id}` - Actualizar camiseta (admin)
- `DELETE /api/camisetas/{id}` - Eliminar camiseta (admin)

### Categorías (requiere autenticación de admin para crear/editar/eliminar)

- `GET /api/categorias` - Listar categorías
- `POST /api/categorias` - Crear categoría
- `PUT /api/categorias/{id}` - Actualizar categoría
- `DELETE /api/categorias/{id}` - Eliminar categoría

### Marcas (requiere autenticación de admin para crear/editar/eliminar)

- `GET /api/marcas` - Listar marcas
- `POST /api/marcas` - Crear marca
- `PUT /api/marcas/{id}` - Actualizar marca
- `DELETE /api/marcas/{id}` - Eliminar marca

## Características implementadas

✅ **API REST completa** con arquitectura MVC  
✅ **Autenticación JWT** sin librerías externas  
✅ **Validación de datos** robusta  
✅ **Subida de imágenes** para camisetas  
✅ **CORS habilitado** para frontend  
✅ **Documentación Swagger** integrada  
✅ **Paginación** en listados  
✅ **Filtros y búsqueda** avanzada  
✅ **Manejo de errores** centralizado  
✅ **Soft delete** para mantener integridad  
✅ **Validación de referencias** entre entidades

## Estructura del proyecto

```
├── index.php              # Router principal
├── config/                # Configuraciones
│   ├── config.php         # Config general
│   ├── database.php       # Conexión BD
│   └── cors.php           # CORS
├── models/                # Modelos de datos
│   ├── User.php           # Modelo Usuario
│   ├── Camiseta.php       # Modelo Camiseta
│   ├── Categoria.php      # Modelo Categoría
│   └── Marca.php          # Modelo Marca
├── routes/                # Rutas de la API
│   ├── auth.php           # Autenticación
│   ├── camisetas.php      # CRUD Camisetas
│   ├── categorias.php     # CRUD Categorías
│   └── marcas.php         # CRUD Marcas
├── middleware/            # Middlewares
│   └── auth.php           # Autenticación JWT
├── utils/                 # Utilidades
│   ├── response.php       # Respuestas JSON
│   ├── validator.php      # Validación
│   ├── jwt.php            # JWT sin librerías
│   └── upload.php         # Subida archivos
├── database/              # Scripts de BD
│   └── schema.sql         # Esquema completo
├── views/                 # Vistas
│   └── swagger.php        # Documentación
└── uploads/               # Archivos subidos
```

## Ejemplo de uso con cURL

### Registrar usuario

```bash
curl -X POST http://localhost/todo-camisetas/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123","nombre":"Usuario Test"}'
```

### Login

```bash
curl -X POST http://localhost/todo-camisetas/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@todocamisetas.com","password":"admin123"}'
```

### Listar camisetas

```bash
curl http://localhost/todo-camisetas/api/camisetas
```

¡El proyecto está listo para usar! 🎉
