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
} else {
    header("Access-Control-Allow-Origin: null");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
