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
                "description": "API REST para gestión de camisetas - Examen Transversal Final",
                "contact": {
                    "name": "Estudiante",
                    "email": "estudiante@example.com"
                }
            },
            "servers": [
                {
                    "url": "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/swagger/', '', $_SERVER['REQUEST_URI']); ?>",
                    "description": "Servidor de desarrollo"
                }
            ],
            "components": {
                "securitySchemes": {
                    "bearerAuth": {
                        "type": "http",
                        "scheme": "bearer",
                        "bearerFormat": "JWT"
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
                "/api/auth/register": {
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
                "/api/auth/login": {
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
                "/api/camisetas": {
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
                        "security": [{"bearerAuth": []}],
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
                "/api/camisetas/{id}": {
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
                        "security": [{"bearerAuth": []}],
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
                        "security": [{"bearerAuth": []}],
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
            SwaggerUIBundle({
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
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>
</html>
