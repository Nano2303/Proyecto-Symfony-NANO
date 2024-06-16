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

// Configuración CORS dinámica
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT, PATCH");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Set-Cookie");
    // Depuración para verificar el origen permitido
    error_log("Allowed Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header("Access-Control-Allow-Origin: null");
    header("Access-Control-Allow-Credentials: false");
    // Depuración para verificar el origen no permitido
    error_log("Blocked Origin: {$_SERVER['HTTP_ORIGIN']}");
}

// Manejar solicitudes OPTIONS (preflight requests)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Configuración de sesiones
ini_set('session.cookie_samesite', 'None');  // Permitir el uso de cookies en contextos CORS
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');  // Usar cookies seguras si se accede por HTTPS
ini_set('session.cookie_domain', '.synonym-shop.com');  // Permitir la cookie en todos los subdominios

// Iniciar la sesión
session_start();

// Cargar el autoload y el kernel de la aplicación
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
