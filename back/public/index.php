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

// Obtener el origen de la solicitud
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

// Manejar solicitudes OPTIONS (preflight requests)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Depuración: Verificar el origen y las cookies
error_log("HTTP_ORIGIN: $origin");
error_log("Cookies de solicitud: " . json_encode($_COOKIE));

// Configuración de cookies de sesión
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '.synonym-shop.com', // Cambia esto para que sea adecuado a tu dominio principal
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'None' // Asegúrate de que esto sea 'None' para permitir cross-origin
]);

// Iniciar la sesión si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Depuración: Verificar la sesión
error_log("Session ID: " . session_id());
error_log("Session Data: " . json_encode($_SESSION));

// Cargar el autoload y el kernel de la aplicación
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

?>
