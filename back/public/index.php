<?php

use App\Kernel;

// Definir orígenes permitidos
$allowedOrigins = [
    'http://localhost:4200',
    'http://194.164.170.132',
    'http://www.synonym-shop.es',
    'http://synonym-shop.es',
    'http://www.synonym-shop.com',
    'http://synonym-shop.com',
    'http://www.synonym-shop.eu',
    'http://synonym-shop.eu',
];

// Obtener el dominio desde el origen
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Configuración CORS dinámica
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT, PATCH");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
} else {
    header("Access-Control-Allow-Origin: null");
    header("Access-Control-Allow-Credentials: false");
}

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Iniciar sesión solo si el origen es permitido
if (in_array($origin, $allowedOrigins)) {
    // Asegurarse de que las sesiones se configuren con el dominio adecuado
    session_set_cookie_params([
        'lifetime' => 0, // La duración de la sesión, 0 para hasta que se cierre el navegador
        'path' => '/', // Ruta válida para la cookie de sesión
        'domain' => parse_url($origin, PHP_URL_HOST), // Dominio para la cookie de sesión
        'secure' => isset($_SERVER['HTTPS']), // Solo permitir la cookie en conexiones HTTPS
        'httponly' => true, // Solo accesible por HTTP, no JavaScript
        'samesite' => 'Lax' // Política de SameSite
    ]);
    session_start();
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
