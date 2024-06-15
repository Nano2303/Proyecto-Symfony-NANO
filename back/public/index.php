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
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
} else {
    header("Access-Control-Allow-Origin: null");
}

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Configuración de cookies de sesión
if (isset($_SERVER['HTTP_HOST'])) {
    $domain = $_SERVER['HTTP_HOST'];

    // Detectar si es una IP o un dominio
    if (filter_var($domain, FILTER_VALIDATE_IP)) {
        $cookieDomain = ''; // No se especifica dominio para la IP pública
    } else {
        // Ajustar el dominio para permitir cookies en todos los subdominios del TLD
        // Ejemplo: si el dominio es 'www.synonym-shop.com', el resultado será '.synonym-shop.com'
        $parts = explode('.', $domain);
        $cookieDomain = (count($parts) > 2) ? '.' . implode('.', array_slice($parts, -2)) : '.' . $domain;
    }

    // Configurar la cookie de sesión
    ini_set('session.cookie_domain', $cookieDomain);
}

// Configurar la cookie de sesión
ini_set('session.cookie_secure', '0'); // Ajustar a '1' si usas HTTPS, '0' si usas HTTP
ini_set('session.cookie_samesite', 'None'); // Permitir envío entre dominios

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
