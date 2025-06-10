# Guía de Despliegue en Producción

## Configuración para Producción

### 1. Variables de Entorno

Edita el archivo `.env` para configuración de producción:

```bash
# Configuración de Base de Datos (Producción)
DB_HOST=localhost
DB_NAME=todo_camisetas_prod
DB_USER=tu_usuario_prod
DB_PASS=tu_password_seguro

# Configuración JWT (CAMBIAR OBLIGATORIAMENTE)
JWT_SECRET=UnSecretoSuperSeguroYLargoParaProduccion2025

# Configuración General
PRODUCTION=true
DEBUG_MODE=false
```

### 2. Configuración del Servidor Web

#### Apache

- Asegúrate de que `mod_rewrite` esté habilitado
- Si no está disponible, usa `api.php` como punto de entrada

#### Nginx

Configuración básica:

```nginx
location /api/ {
    try_files $uri $uri/ /api.php?path=$uri&$query_string;
}
```

### 3. Seguridad

#### Permisos de Archivos

```bash
# Configurar permisos seguros
chmod 755 /path/to/project
chmod 644 config/*.php
chmod 755 uploads/
chmod 644 uploads/.gitkeep
```

#### Ocultación de Archivos Sensibles

- El archivo `.env` NO debe ser accesible vía web
- Configurar el servidor para denegar acceso a archivos `.env`

### 4. Base de Datos

```sql
-- Crear base de datos de producción
CREATE DATABASE todo_camisetas_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar schema
mysql -u root -p todo_camisetas_prod < database/schema.sql
```

### 5. Optimizaciones

#### PHP

```ini
; php.ini optimizaciones
display_errors = Off
log_errors = On
error_log = /path/to/logs/php_errors.log
```

#### Base de Datos

- Configurar índices apropiados
- Optimizar consultas SQL
- Configurar backups automáticos

### 6. Monitoreo

#### Logs de Errores

- Configurar rotación de logs
- Monitorear archivos de error de Apache/Nginx
- Revisar logs de PHP regularmente

#### Salud de la API

Endpoint de verificación:

```
GET /api/auth/verify
```

### 7. Checklist de Despliegue

- [ ] Variables de entorno configuradas
- [ ] JWT_SECRET cambiado por uno seguro
- [ ] PRODUCTION=true en .env
- [ ] DEBUG_MODE=false en .env
- [ ] Base de datos de producción creada
- [ ] Schema importado
- [ ] Permisos de archivos configurados
- [ ] Servidor web configurado
- [ ] SSL/HTTPS habilitado
- [ ] Logs configurados
- [ ] Backup programado

### 8. Mantenimiento

#### Backups

```bash
# Backup diario automático
mysqldump -u user -p todo_camisetas_prod > backup_$(date +%Y%m%d).sql
```

#### Actualización de Dependencias

- Revisar y actualizar configuraciones de seguridad
- Mantener PHP actualizado
- Aplicar parches de seguridad del servidor

### 9. Contacto y Soporte

Para soporte técnico, contactar al desarrollador con los logs relevantes y descripción detallada del problema.
