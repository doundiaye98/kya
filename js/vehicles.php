<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/products.php';

header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: public, max-age=20');

$js = products_vehicles_js();
$etag = '"' . md5($js) . '"';
header('ETag: ' . $etag);
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}
echo $js;
