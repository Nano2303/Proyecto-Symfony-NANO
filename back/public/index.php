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

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Código para depuración adicional
error_log("Origen recibido: $origin");
error_log("Allow-Credentials: " . (in_array($origin, $allowedOrigins) ? "true" : "false"));

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
