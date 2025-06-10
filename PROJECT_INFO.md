# 🎽 Todo Camisetas API - Proyecto Final

## ✅ Estado: PRODUCCIÓN

Este proyecto implementa una API REST completa para la gestión de camisetas usando PHP puro y MySQL.

## 📁 Estructura Final del Proyecto

```
├── api.php                    # Router principal de la API
├── swagger-wrapper.php        # Wrapper para compatibilidad con Swagger UI
├── index.php                  # Página de bienvenida
├── panel_pruebas.html         # Panel de pruebas interactivo (opcional)
├── .env                       # Variables de entorno (configurar antes del despliegue)
├── .env.example              # Plantilla de configuración
├── config/                   # Configuraciones del sistema
│   ├── config.php            # Configuración general
│   ├── database.php          # Conexión a base de datos
│   └── cors.php              # Configuración CORS
├── models/                   # Modelos de datos
│   ├── User.php              # Modelo de Usuario
│   ├── Camiseta.php          # Modelo de Camiseta
│   ├── Categoria.php         # Modelo de Categoría
│   └── Marca.php             # Modelo de Marca
├── routes/                   # Definición de rutas por módulo
│   ├── auth.php              # Rutas de autenticación
│   ├── camisetas.php         # CRUD de camisetas
│   ├── categorias.php        # CRUD de categorías
│   └── marcas.php            # CRUD de marcas
├── middleware/               # Middleware de autenticación
│   └── auth.php              # Middleware JWT
├── utils/                    # Utilidades del sistema
│   ├── env.php               # Carga de variables de entorno
│   ├── jwt.php               # Manejo de JWT (sin librerías externas)
│   ├── response.php          # Respuestas JSON estandarizadas
│   ├── validator.php         # Sistema de validación
│   └── upload.php            # Manejo de archivos
├── database/                 # Scripts de base de datos
│   └── schema.sql            # Esquema completo con datos de ejemplo
├── views/                    # Vistas del sistema
│   └── swagger.php           # Documentación Swagger UI
├── uploads/                  # Directorio para archivos subidos
└── documentation/            # Documentación del proyecto
    ├── README.md             # Documentación principal
    ├── INSTALACION.md        # Guía de instalación
    ├── QUICK_START.md        # Inicio rápido
    └── PRODUCTION.md         # Guía de despliegue en producción
```

## 🚀 Características Implementadas

### ✅ API REST Completa

- CRUD completo para camisetas, categorías y marcas
- Autenticación JWT sin librerías externas
- Validación robusta de datos
- Manejo de errores centralizado
- Respuestas JSON estandarizadas

### ✅ Sistema de Seguridad

- Autenticación JWT personalizada
- Middleware de autorización
- Validación de roles (admin/user)
- Soft delete para integridad de datos
- Protección CORS configurada

### ✅ Documentación y Testing

- Swagger UI completamente funcional
- Panel de pruebas interactivo
- Documentación completa
- Guías de instalación y despliegue

### ✅ Funcionalidades Avanzadas

- Subida de imágenes para camisetas
- Paginación en listados
- Filtros de búsqueda
- Sistema de variables de entorno
- Configuración flexible desarrollo/producción

## 🌐 URLs de Acceso

- **API Principal:** `http://localhost:8888/todo-camisetas/`
- **Documentación Swagger:** `http://localhost:8888/todo-camisetas/views/swagger.php`
- **Panel de Pruebas:** `http://localhost:8888/todo-camisetas/panel_pruebas.html`

## 🔐 Credenciales de Prueba

- **Admin:** `demo@swagger.com` / `Demo123`
- **Usuarios adicionales:** Se pueden crear mediante `/auth/register`

## 📊 Estado del Sistema

- ✅ **API funcionando correctamente**
- ✅ **Base de datos configurada**
- ✅ **Swagger UI operativo**
- ✅ **Autenticación implementada**
- ✅ **Documentación completa**
- ✅ **Listo para producción**

---

**🏆 Proyecto completado exitosamente para el Examen Transversal Final**

_Desarrollado con PHP puro, MySQL y arquitectura MVC REST_
