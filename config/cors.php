<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Rutas que aceptarán solicitudes CORS
    'allowed_methods' => ['*'], // Métodos HTTP permitidos
    'allowed_origins' => ['http://127.0.0.1:8001'], // Cambiar con la URL de tu frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Cabeceras permitidas
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Importante si estás usando cookies/session
];
