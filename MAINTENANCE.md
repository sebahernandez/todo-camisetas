# 🔧 Guía de Mantenimiento - Todo Camisetas API

## 📝 Notas de Desarrollo

### Archivos Principales del Sistema

#### Core de la API

- **`api.php`** - Router principal que maneja todas las rutas con parámetro `?path=`
- **`swagger-wrapper.php`** - Proxy que convierte rutas REST de Swagger al formato de nuestra API
- **`index.php`** - Página de bienvenida y estado de la API

#### Configuración

- **`.env`** - Variables de entorno (NO incluir en git)
- **`config/`** - Configuraciones del sistema (BD, CORS, etc.)

#### Lógica de Negocio

- **`models/`** - Modelos de datos (User, Camiseta, Categoria, Marca)
- **`routes/`** - Controladores de endpoints por módulo
- **`middleware/`** - Autenticación JWT

#### Utilidades

- **`utils/`** - Funciones auxiliares (JWT, validación, respuestas, uploads)

### ⚙️ Configuración de Producción

#### Variables de Entorno Críticas

```bash
PRODUCTION=true
DEBUG_MODE=false
JWT_SECRET=cambiar_por_secreto_seguro_en_produccion
```

#### Base de Datos

- Ejecutar `database/schema.sql` en el servidor de producción
- Cambiar credenciales en `.env`
- Verificar permisos del directorio `uploads/`

### 🚀 Despliegue

#### Checklist Pre-Despliegue

- [ ] Configurar `.env` para producción
- [ ] Cambiar `JWT_SECRET` por uno seguro
- [ ] Importar base de datos
- [ ] Verificar permisos de `uploads/`
- [ ] Probar todos los endpoints
- [ ] Verificar Swagger UI funcional

#### URLs en Producción

Actualizar estas URLs en `swagger-wrapper.php` si cambia el dominio:

```php
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/todo-camisetas/api.php';
```

### 🔍 Debugging

#### Logs de Errores

- Activar `DEBUG_MODE=true` en `.env` para debugging
- Revisar logs de Apache/PHP para errores
- Verificar conectividad de base de datos

#### Problemas Comunes

1. **"Ruta no encontrada"** - Verificar configuración del servidor en Swagger
2. **Error de BD** - Revisar credenciales en `.env`
3. **Token expirado** - Los JWT expiran en 24h, renovar con `/auth/login`

### 📊 Monitoreo

#### Health Check

- `GET /` - Estado general de la API
- `GET /?path=status` - Estado detallado del sistema

#### Endpoints Críticos

- `/auth/login` - Autenticación
- `/camisetas` - Funcionalidad principal
- Swagger UI - Documentación

### 🛠️ Modificaciones Futuras

#### Agregar Nuevos Endpoints

1. Crear función en `routes/[modulo].php`
2. Agregar case en `api.php`
3. Documentar en `views/swagger.php`
4. Actualizar `swagger-wrapper.php` si es necesario

#### Cambios de BD

1. Actualizar modelo correspondiente
2. Modificar `database/schema.sql`
3. Actualizar documentación Swagger

---

**📅 Última actualización:** 10 de junio de 2025  
**👨‍💻 Mantenido por:** Equipo Todo Camisetas API
