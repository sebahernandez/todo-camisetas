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

## 📦 Instalación Rápida

Ver documentación completa en [INSTALACION.md](INSTALACION.md)

1. Clonar el proyecto en la carpeta htdocs de MAMP
2. Importar la base de datos desde `database/schema.sql`
3. Configurar variables de entorno copiando `.env.example` a `.env`
4. Iniciar MAMP con Apache y MySQL

## ⚙️ Configuración

Configurar el archivo `.env` con los datos de tu base de datos MAMP:

```bash
DB_HOST=localhost:8889
DB_NAME=todo_camisetas
DB_USER=root
DB_PASS=root
JWT_SECRET=TuSecretoSeguro
PRODUCTION=false
DEBUG_MODE=true
```

## 🚀 Uso

1. Iniciar MAMP
2. **API Principal:** `http://localhost:8888/todo-camisetas/`
3. **Documentación Swagger:** `http://localhost:8888/todo-camisetas/views/swagger.php`
4. **Panel de Pruebas:** `http://localhost:8888/todo-camisetas/panel_pruebas.html`

## 🔗 Endpoints de la API

Todos los endpoints usan la ruta base: `http://localhost:8888/todo-camisetas/api.php?path=`

### 🔐 Autenticación

- `POST api.php?path=auth/register` - Registrar usuario
- `POST api.php?path=auth/login` - Iniciar sesión
- `GET api.php?path=auth/verify` - Verificar token

### 👕 Camisetas

- `GET api.php?path=camisetas` - Listar todas las camisetas
- `GET api.php?path=camisetas/{id}` - Obtener camiseta por ID
- `POST api.php?path=camisetas` - Crear nueva camiseta
- `PUT api.php?path=camisetas/{id}` - Actualizar camiseta
- `DELETE api.php?path=camisetas/{id}` - Eliminar camiseta

### 🏷️ Categorías

- `GET api.php?path=categorias` - Listar categorías
- `POST api.php?path=categorias` - Crear categoría
- `PUT api.php?path=categorias/{id}` - Actualizar categoría
- `DELETE api.php?path=categorias/{id}` - Eliminar categoría

### 🏢 Marcas

- `GET api.php?path=marcas` - Listar marcas
- `POST api.php?path=marcas` - Crear marca
- `PUT api.php?path=marcas/{id}` - Actualizar marca
- `DELETE api.php?path=marcas/{id}` - Eliminar marca

### 📊 Ejemplos de URLs Completas

```bash
# Listar camisetas
http://localhost:8888/todo-camisetas/api.php?path=camisetas

# Obtener categorías
http://localhost:8888/todo-camisetas/api.php?path=categorias

# Login de usuario
http://localhost:8888/todo-camisetas/api.php?path=auth/login
```

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
