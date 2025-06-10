# 🚀 Quick Start Guide - Todo Camisetas API

## Acceso Rápido

### 🌐 URLs Principales

- **API Base:** `http://localhost:8888/todo-camisetas/api.php`
- **Panel de Pruebas:** `http://localhost:8888/todo-camisetas/panel_pruebas.html`
- **Documentación:** `http://localhost:8888/todo-camisetas/views/swagger.php`

### ⚡ Prueba Rápida (5 minutos)

1. **Verificar API Status:**

```bash
curl "http://localhost:8888/todo-camisetas/api.php?path=status"
```

2. **Registrar Usuario:**

```bash
curl -X POST "http://localhost:8888/todo-camisetas/api.php?path=auth/register" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"Demo123","nombre":"Usuario Demo"}'
```

3. **Iniciar Sesión:**

```bash
curl -X POST "http://localhost:8888/todo-camisetas/api.php?path=auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"Demo123"}'
```

4. **Listar Camisetas (usar el token del paso 3):**

```bash
curl -X GET "http://localhost:8888/todo-camisetas/api.php?path=camisetas" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 🎯 Características Clave

- ✅ **API REST Completa** - CRUD para camisetas, categorías y marcas
- ✅ **Autenticación JWT** - Sistema seguro sin librerías externas
- ✅ **Datos de Ejemplo** - 10 camisetas, 5 categorías, 7 marcas preconfiguradas
- ✅ **Validaciones** - Sistema robusto de validación de entrada
- ✅ **Documentación** - Swagger UI completamente funcional
- ✅ **Panel de Pruebas** - Interfaz web para testing interactivo

### 📱 Uso del Panel Web

1. Abrir: `http://localhost:8888/todo-camisetas/panel_pruebas.html`
2. Probar "Verificar Estado" para confirmar que la API funciona
3. Registrar un nuevo usuario en la sección de Autenticación
4. Usar el token devuelto para probar los demás endpoints

### 🗄️ Base de Datos

La base de datos `todo_camisetas` se crea automáticamente con datos de ejemplo:

**Usuarios Predeterminados:**

- Admin: `admin@example.com` / `Admin123`
- Los usuarios que registres tú

**Datos de Ejemplo:**

- 10 camisetas de diferentes marcas y categorías
- Precios desde $19.990 hasta $89.990
- Variedad de tallas (S, M, L, XL) y colores

### 🔧 Resolución de Problemas

**Error de Conexión BD:**

- Verificar que MAMP esté ejecutándose
- Confirmar puerto MySQL (8889)
- Ejecutar setup si es necesario

**Error 500:**

- Verificar logs de Apache en MAMP
- Confirmar que no hay archivos .htaccess problemáticos

**Token Expirado:**

- Los tokens JWT expiran en 24 horas
- Hacer login nuevamente para obtener nuevo token

### 📋 Lista de Verificación

- [ ] MAMP ejecutándose
- [ ] Base de datos creada
- [ ] API status devuelve success: true
- [ ] Panel de pruebas carga correctamente
- [ ] Swagger documentation accesible
- [ ] Registro de usuario funciona
- [ ] Login devuelve token válido
- [ ] Endpoints protegidos responden con token

---

¡Todo listo para usar! 🎉
