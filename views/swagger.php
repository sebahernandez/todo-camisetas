<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Camisetas API - Documentación</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin:0;
            background: #fafafa;
        }
        .swagger-ui .topbar {
            background-color: #1f8c5a;
        }
        .swagger-ui .topbar .download-url-wrapper {
            display: none;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-standalone-preset.js"></script>
    <script>
        const swaggerSpec = {
            "openapi": "3.0.0",
            "info": {
                "title": "Todo Camisetas API",
                "version": "1.0.0",
                "description": "API REST para gestión de camisetas - Examen Transversal Final\n\n## 🔐 Autenticación\n\n### Pasos para autenticarte:\n\n1. **Haz clic en el botón 'Authorize' 🔒** (arriba a la derecha)\n2. **Registra un usuario** usando el endpoint `/auth/register` o usa las credenciales de prueba\n3. **Inicia sesión** usando el endpoint `/auth/login` para obtener tu token JWT\n4. **En el modal de autorización**, pega tu token en el campo 'Value' (sin agregar 'Bearer')\n5. **Haz clic en 'Authorize'** y luego 'Close'\n6. Ahora puedes usar todos los endpoints protegidos ✅\n\n## ⚠️ Problema Común: Headers Authorization\n\n**Si tienes problemas con autenticación en MAMP/XAMPP:**\n\nEl header `Authorization: Bearer` puede no funcionar. **Usa alternativas:**\n\n### 🔧 Solución A: Header X-API-Token\n```bash\ncurl -H \"X-API-Token: tu_jwt_token\" ...\n```\n\n### 🔧 Solución B: Query Parameter\n```bash\ncurl \"...?token=tu_jwt_token\"\n```\n\n### 🔧 Solución C: JavaScript\n```javascript\nfetch(url, {\n  headers: { 'X-API-Token': token }\n})\n```\n\n## 🎯 Usuario Admin de Prueba\n\n- **Email:** `demo@swagger.com`\n- **Password:** `Demo123`\n- **Token directo:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjozLCJlbWFpbCI6ImRlbW9Ac3dhZ2dlci5jb20iLCJyb2wiOiJhZG1pbiIsImV4cCI6MTc0OTY1OTUzMH0.XJW8-HOG-Lv4GHYaqnWNn5sbA65Xt6RubgJ2d12yDlk`\n\n## 📝 Notas Importantes\n\n- ✅ **Los endpoints GET** (listar) son públicos\n- 🔒 **Los endpoints POST, PUT, DELETE** requieren autenticación\n- 👮 **Solo los administradores** pueden crear/editar/eliminar recursos\n- ⏰ **El token de ejemplo expira en 24 horas**\n- 🔑 **Usa el botón 'Authorize' nativo** para mejor experiencia\n- 📋 **Ver SOLUCION_AUTH.md** para más detalles sobre autenticación",
                "contact": {
                    "name": "Estudiante",
                    "email": "estudiante@example.com"
                }
            },
            "servers": [
                {
                    "url": "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/todo-camisetas/swagger-wrapper.php'; ?>",
                    "description": "Servidor de desarrollo"
                }
            ],
            "components": {
                "securitySchemes": {
                    "bearerAuth": {
                        "type": "http",
                        "scheme": "bearer",
                        "bearerFormat": "JWT"
                    },
                    "apiKeyAuth": {
                        "type": "apiKey",
                        "in": "header",
                        "name": "X-API-Token",
                        "description": "Usar este header si Authorization: Bearer no funciona en MAMP/XAMPP"
                    }
                },
                "schemas": {
                    "User": {
                        "type": "object",
                        "properties": {
                            "id": {"type": "integer"},
                            "email": {"type": "string", "format": "email"},
                            "nombre": {"type": "string"},
                            "rol": {"type": "string", "enum": ["admin", "user"]},
                            "activo": {"type": "boolean"},
                            "created_at": {"type": "string", "format": "date-time"}
                        }
                    },
                    "Camiseta": {
                        "type": "object",
                        "properties": {
                            "id": {"type": "integer"},
                            "nombre": {"type": "string"},
                            "descripcion": {"type": "string"},
                            "precio": {"type": "number", "format": "decimal"},
                            "talla": {"type": "string", "enum": ["XS", "S", "M", "L", "XL", "XXL"]},
                            "color": {"type": "string"},
                            "stock": {"type": "integer"},
                            "imagen": {"type": "string"},
                            "categoria_id": {"type": "integer"},
                            "marca_id": {"type": "integer"},
                            "categoria_nombre": {"type": "string"},
                            "marca_nombre": {"type": "string"},
                            "activo": {"type": "boolean"},
                            "created_at": {"type": "string", "format": "date-time"}
                        }
                    },
                    "Categoria": {
                        "type": "object",
                        "properties": {
                            "id": {"type": "integer"},
                            "nombre": {"type": "string"},
                            "descripcion": {"type": "string"},
                            "total_camisetas": {"type": "integer"},
                            "activo": {"type": "boolean"},
                            "created_at": {"type": "string", "format": "date-time"}
                        }
                    },
                    "Marca": {
                        "type": "object",
                        "properties": {
                            "id": {"type": "integer"},
                            "nombre": {"type": "string"},
                            "descripcion": {"type": "string"},
                            "total_camisetas": {"type": "integer"},
                            "activo": {"type": "boolean"},
                            "created_at": {"type": "string", "format": "date-time"}
                        }
                    },
                    "Error": {
                        "type": "object",
                        "properties": {
                            "success": {"type": "boolean", "example": false},
                            "message": {"type": "string"},
                            "errors": {"type": "array", "items": {"type": "string"}}
                        }
                    },
                    "Success": {
                        "type": "object",
                        "properties": {
                            "success": {"type": "boolean", "example": true},
                            "message": {"type": "string"},
                            "data": {"type": "object"}
                        }
                    }
                }
            },
            "paths": {
                "/auth/register": {
                    "post": {
                        "tags": ["Autenticación"],
                        "summary": "Registrar nuevo usuario",
                        "requestBody": {
                            "required": true,
                            "content": {
                                "application/json": {
                                    "schema": {
                                        "type": "object",
                                        "required": ["email", "password", "nombre"],
                                        "properties": {
                                            "email": {"type": "string", "format": "email"},
                                            "password": {"type": "string", "minLength": 6},
                                            "nombre": {"type": "string"},
                                            "rol": {"type": "string", "enum": ["user", "admin"], "default": "user"}
                                        }
                                    }
                                }
                            }
                        },
                        "responses": {
                            "201": {"description": "Usuario registrado exitosamente"},
                            "400": {"description": "Error de validación"},
                            "409": {"description": "El email ya está registrado"}
                        }
                    }
                },
                "/auth/login": {
                    "post": {
                        "tags": ["Autenticación"],
                        "summary": "Iniciar sesión",
                        "requestBody": {
                            "required": true,
                            "content": {
                                "application/json": {
                                    "schema": {
                                        "type": "object",
                                        "required": ["email", "password"],
                                        "properties": {
                                            "email": {"type": "string", "format": "email"},
                                            "password": {"type": "string"}
                                        }
                                    }
                                }
                            }
                        },
                        "responses": {
                            "200": {"description": "Login exitoso"},
                            "401": {"description": "Credenciales inválidas"}
                        }
                    }
                },
                "/camisetas": {
                    "get": {
                        "tags": ["Camisetas"],
                        "summary": "Listar camisetas",
                        "parameters": [
                            {"name": "page", "in": "query", "schema": {"type": "integer", "default": 1}},
                            {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 10}},
                            {"name": "search", "in": "query", "schema": {"type": "string"}},
                            {"name": "categoria_id", "in": "query", "schema": {"type": "integer"}},
                            {"name": "marca_id", "in": "query", "schema": {"type": "integer"}},
                            {"name": "talla", "in": "query", "schema": {"type": "string"}},
                            {"name": "color", "in": "query", "schema": {"type": "string"}}
                        ],
                        "responses": {
                            "200": {"description": "Lista de camisetas"}
                        }
                    },
                    "post": {
                        "tags": ["Camisetas"],
                        "summary": "Crear nueva camiseta",
                        "security": [{"bearerAuth": []}, {"apiKeyAuth": []}],
                        "requestBody": {
                            "required": true,
                            "content": {
                                "multipart/form-data": {
                                    "schema": {
                                        "type": "object",
                                        "required": ["nombre", "precio", "talla", "categoria_id", "marca_id"],
                                        "properties": {
                                            "nombre": {"type": "string"},
                                            "descripcion": {"type": "string"},
                                            "precio": {"type": "number"},
                                            "talla": {"type": "string", "enum": ["XS", "S", "M", "L", "XL", "XXL"]},
                                            "color": {"type": "string"},
                                            "stock": {"type": "integer"},
                                            "categoria_id": {"type": "integer"},
                                            "marca_id": {"type": "integer"},
                                            "imagen": {"type": "string", "format": "binary"}
                                        }
                                    }
                                }
                            }
                        },
                        "responses": {
                            "201": {"description": "Camiseta creada exitosamente"},
                            "401": {"description": "No autorizado"}
                        }
                    }
                },
                "/camisetas/{id}": {
                    "get": {
                        "tags": ["Camisetas"],
                        "summary": "Obtener camiseta por ID",
                        "parameters": [
                            {"name": "id", "in": "path", "required": true, "schema": {"type": "integer"}}
                        ],
                        "responses": {
                            "200": {"description": "Camiseta encontrada"},
                            "404": {"description": "Camiseta no encontrada"}
                        }
                    },
                    "put": {
                        "tags": ["Camisetas"],
                        "summary": "Actualizar camiseta",
                        "security": [{"bearerAuth": []}, {"apiKeyAuth": []}],
                        "parameters": [
                            {"name": "id", "in": "path", "required": true, "schema": {"type": "integer"}}
                        ],
                        "responses": {
                            "200": {"description": "Camiseta actualizada"},
                            "404": {"description": "Camiseta no encontrada"}
                        }
                    },
                    "delete": {
                        "tags": ["Camisetas"],
                        "summary": "Eliminar camiseta",
                        "security": [{"bearerAuth": []}, {"apiKeyAuth": []}],
                        "parameters": [
                            {"name": "id", "in": "path", "required": true, "schema": {"type": "integer"}}
                        ],
                        "responses": {
                            "204": {"description": "Camiseta eliminada"},
                            "404": {"description": "Camiseta no encontrada"}
                        }
                    }
                }
            }
        };

        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: null,
                spec: swaggerSpec,
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                onComplete: function() {
                    console.log('Swagger UI cargado correctamente');
                }
            });
        };
    </script>
</body>
</html>
