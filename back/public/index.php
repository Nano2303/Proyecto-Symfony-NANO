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

    // Ajustar el dominio de la cookie para permitir múltiples TLDs y IP pública
    if (filter_var($domain, FILTER_VALIDATE_IP)) {
        $cookieDomain = ''; // No se especifica dominio para la IP pública
    } else {
        // Ajustar el dominio para permitir cookies en todos los subdominios del TLD
        $cookieDomain = '.' . implode('.', array_slice(explode('.', $domain), -2));
    }

    ini_set('session.cookie_domain', $cookieDomain);
}

// Asegurarse de que las cookies se envíen solo a través de HTTPS si está habilitado
ini_set('session.cookie_secure', '0'); // '1' para HTTPS, '0' para HTTP
ini_set('session.cookie_samesite', 'None'); // Permitir envío entre dominios

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
