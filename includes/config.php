<?php
define('KYA_NAME', 'KYA CARS');
define('KYA_PHONE', '+221 77 310 17 44');
define('KYA_PHONE_TEL', '+221773101744');
define('KYA_WHATSAPP', '221773101744');
define('KYA_EMAIL', 'contact@kyacars.sn');
define('KYA_ADDRESS', 'Route des Almadies, Dakar, Sénégal');
define('KYA_HOURS', 'Lun — Sam 08:00 – 18:00');
define('KYA_TAGLINE', 'Location & vente de voitures');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'kya_cars');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('KYA_BASE', '/kya');

@ini_set('display_errors', '0');
@ini_set('log_errors', '1');
error_reporting(E_ALL);

if (!function_exists('kya_fail_page')) {
    function kya_fail_page()
    {
        if (!headers_sent()) {
            http_response_code(503);
            header('Retry-After: 2');
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>KYA CARS</title></head><body style="margin:0;background:#050505;color:#e8d08a;font-family:Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:24px"><div><h1 style="letter-spacing:.2em">KYA CARS</h1><p style="color:#cfc6b8;max-width:28em">Le site est très sollicité pour le moment. Patientez quelques secondes puis rechargez la page.</p></div></body></html>';
    }
}

set_exception_handler(function ($e) {
    if (function_exists('kya_log')) {
        kya_log($e->getMessage(), 'error', 'uncaught');
    }
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, 'KYA: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
    kya_fail_page();
    exit;
});
?>
