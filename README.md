# 🎽 Todo Camisetas API

**API REST completa para gestión de camisetas - Examen Transversal Final**

## ✨ Características Principales

- ✅ **API REST completa** con operaciones CRUD
- ✅ **Autenticación JWT** sin librerías externas
- ✅ **Sistema de validación** personalizado
- ✅ **Subida de archivos** para imágenes de camisetas
- ✅ **Documentación Swagger** interactiva
- ✅ **Panel de pruebas** web integrado
- ✅ **Base de datos MySQL** con datos de ejemplo
- ✅ **Soft Delete** para mantener integridad
- ✅ **Manejo de errores** centralizado
- ✅ **CORS** configurado
- ✅ **PHP puro** sin frameworks

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.0+
- **Base de Datos:** MySQL 8.0+
- **Autenticación:** JWT (implementación personalizada)
- **Documentación:** Swagger UI
- **Servidor:** Apache (MAMP)

## Instalación

1. Clonar el proyecto en la carpeta htdocs de MAMP
2. Configurar la base de datos:
   ```sql
   -- Ejecutar el script database/schema.sql
   ```
3. Configurar las variables de entorno en `config/config.php`
4. Instalar dependencias:
   ```bash
   composer install
   ```

## Configuración

Editar el archivo `config/config.php` con la configuración de tu base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_camisetas');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Uso

1. Iniciar MAMP/XAMPP
2. Acceder a: `http://localhost/todo-camisetas/`
3. Documentación API: `http://localhost/todo-camisetas/swagger/`

## Endpoints Principales

### Autenticación

- `POST /api/auth/register.php` - Registrar usuario
- `POST /api/auth/login.php` - Iniciar sesión

### Camisetas

- `GET /api/camisetas/` - Listar todas las camisetas
- `GET /api/camisetas/{id}` - Obtener camiseta por ID
- `POST /api/camisetas/` - Crear nueva camiseta
- `PUT /api/camisetas/{id}` - Actualizar camiseta
- `DELETE /api/camisetas/{id}` - Eliminar camiseta

### Categorías

- `GET /api/categorias/` - Listar categorías
- `POST /api/categorias/` - Crear categoría

### Marcas

- `GET /api/marcas/` - Listar marcas
- `POST /api/marcas/` - Crear marca

## 📁 Estructura del Proyecto

```
├── api.php               # Router principal de la API
├── index.php             # Página de bienvenida
├── panel_pruebas.html    # Panel de pruebas interactivo
├── .env                  # Variables de entorno (no incluido en git)
├── .env.example          # Ejemplo de configuración
├── config/
│   ├── config.php        # Configuración general
│   ├── database.php      # Configuración de BD
│   └── cors.php          # Configuración CORS
├── models/               # Modelos de datos (User, Camiseta, etc.)
├── middleware/           # Middleware de autenticación
├── routes/               # Definición de rutas por módulo
├── utils/                # Utilidades (JWT, validación, etc.)
├── database/             # Schema SQL y datos de ejemplo
├── uploads/              # Directorio para archivos subidos
├── views/                # Vistas (Swagger UI)
└── public/               # Archivos estáticos (CSS, JS)
```

## 🔧 Tecnologías

- **PHP 8.0+** (sin frameworks, implementación pura)
- **MySQL 8.0+** con PDO
- **JWT** para autenticación (implementación personalizada)
- **Swagger UI** para documentación interactiva
- **Arquitectura MVC REST**
- **Variables de entorno** para configuración

## 📚 Documentación Adicional

- 📋 **[INSTALACION.md](INSTALACION.md)** - Guía completa de instalación paso a paso
- ⚡ **[QUICK_START.md](QUICK_START.md)** - Inicio rápido y primeros pasos
- 🚀 **[PRODUCTION.md](PRODUCTION.md)** - Guía de despliegue en producción

## ✅ Estado del Proyecto

✅ **Proyecto completamente funcional y listo para producción**

- Todas las funcionalidades implementadas
- Código limpio y optimizado
- Documentación completa
- Sin archivos de debug o test
- Configuración flexible via variables de entorno
- Manejo de errores apropiado para desarrollo y producción
