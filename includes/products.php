<?php
require_once __DIR__ . '/db.php';

function product_types()
{
    return ['4x4 & SUV', 'BERLINE', 'CITADINE', 'FAMILLE', 'Luxe', 'VAN', 'Poids lourd', 'MINIBUS'];
}

function product_image_src($path)
{
    if (!$path) {
        return KYA_BASE . '/img/logo.jpg';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return KYA_BASE . '/' . ltrim(str_replace('\\', '/', $path), '/');
}

function products_cache_bust()
{
    $dir = kya_cache_dir();
    foreach (glob($dir . DIRECTORY_SEPARATOR . 'products-*.json') ?: [] as $file) {
        @unlink($file);
    }
    @unlink($dir . DIRECTORY_SEPARATOR . 'vehicles.js');
}

function ensure_products_table()
{
    static $ok = false;
    if ($ok) {
        return true;
    }
    $pdo = db();
    if (!$pdo) {
        return false;
    }

    try {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    } catch (PDOException $e) {
        kya_ensure_runtime_tables($pdo);
        try {
            $count = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        } catch (PDOException $e2) {
            kya_log($e2->getMessage(), 'error', 'products.table');
            return false;
        }
    }

    if ($count > 0) {
        $ok = true;
        return true;
    }

    if (!kya_lock($pdo, 'kya_products_init', 8)) {
        usleep(250000);
        $ok = true;
        return true;
    }
    try {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        if ($count === 0) {
            seed_default_products($pdo);
            products_cache_bust();
        }
    } catch (PDOException $e) {
        kya_log($e->getMessage(), 'error', 'products.seed');
    } finally {
        kya_unlock($pdo, 'kya_products_init');
    }
    $ok = true;
    return true;
}

function seed_default_products(PDO $pdo)
{
    $rows = [
        ['Mercedes-Benz GLE 350', 'MERCEDES', '4x4 & SUV', 2024, 5, 'Automatique', 'Essence', 1, 1, 120000, 42500000, 1, 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=1400&q=80'],
        ['BMW X5 xDrive40i', 'BMW', '4x4 & SUV', 2023, 5, 'Automatique', 'Essence', 1, 1, 110000, 39800000, 1, 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1400&q=80'],
        ['Range Rover Sport', 'RANGE ROVER', 'Luxe', 2024, 5, 'Automatique', 'Essence', 1, 1, 150000, 52000000, 1, 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1400&q=80'],
        ['Toyota Land Cruiser Prado', 'TOYOTA', '4x4 & SUV', 2023, 7, 'Automatique', 'Diesel', 1, 1, 95000, 33500000, 1, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1400&q=80'],
        ['Mercedes-Benz Classe E', 'MERCEDES', 'BERLINE', 2023, 5, 'Automatique', 'Essence', 1, 1, 85000, 28900000, 0, 'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?auto=format&fit=crop&w=1400&q=80'],
        ['Jeep Wrangler Unlimited', 'JEEP', '4x4 & SUV', 2022, 5, 'Automatique', 'Essence', 1, 1, 80000, 24800000, 0, 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1400&q=80'],
        ['Hyundai Tucson', 'HYUNDAI', 'FAMILLE', 2024, 5, 'Automatique', 'Essence', 1, 1, 45000, 18500000, 0, 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1400&q=80'],
        ['Kia Sportage', 'KIA', 'FAMILLE', 2023, 5, 'Automatique', 'Essence', 1, 1, 42000, 16800000, 0, 'https://images.unsplash.com/photo-1619682817481-e994891cd1f5?auto=format&fit=crop&w=1400&q=80'],
        ['Porsche 911 Carrera', 'PORSCHE', 'Luxe', 2023, 2, 'Automatique', 'Essence', 1, 0, 220000, null, 1, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1400&q=80'],
        ['Toyota Hilux Double Cabine', 'TOYOTA', 'Poids lourd', 2023, 5, 'Manuelle', 'Diesel', 1, 1, 70000, 22600000, 0, 'https://images.unsplash.com/photo-1559416523-140ddc3d238c?auto=format&fit=crop&w=1400&q=80'],
        ['Mercedes-Benz Vito', 'MERCEDES', 'VAN', 2022, 8, 'Automatique', 'Diesel', 1, 0, 90000, null, 0, 'https://images.unsplash.com/photo-1544620341-11cb2cd7c6d5?auto=format&fit=crop&w=1400&q=80'],
        ['Peugeot 308', 'PEUGEOT', 'CITADINE', 2023, 5, 'Manuelle', 'Essence', 1, 1, 28000, 9800000, 0, 'https://images.unsplash.com/photo-1609521263047-f8f205d90c8b?auto=format&fit=crop&w=1400&q=80'],
        ['Lexus RX 350', 'LEXUS', 'Luxe', 2023, 5, 'Automatique', 'Essence', 0, 1, null, 36500000, 1, 'https://images.unsplash.com/photo-1549317661-bd32c8ce16cd?auto=format&fit=crop&w=1400&q=80'],
        ['Ford Ranger Wildtrak', 'FORD', '4x4 & SUV', 2024, 5, 'Automatique', 'Diesel', 1, 1, 75000, 24200000, 0, 'https://images.unsplash.com/photo-1551830820-330a71b99659?auto=format&fit=crop&w=1400&q=80'],
        ['BMW Série 5', 'BMW', 'BERLINE', 2022, 5, 'Automatique', 'Essence', 1, 1, 78000, 25500000, 0, 'https://images.unsplash.com/photo-1523983388277-336a66bf9bcd?auto=format&fit=crop&w=1400&q=80'],
    ];
    $sql = 'INSERT INTO products (name, brand, type, year, seats, transmission, fuel, offer_location, offer_vente, price_day, price_sale, featured, image, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'actif\')';
    $stmt = $pdo->prepare($sql);
    foreach ($rows as $row) {
        $stmt->execute($row);
    }
}

function products_all($activeOnly = false)
{
    static $memo = [];
    $key = $activeOnly ? 'active' : 'all';
    if (isset($memo[$key])) {
        return $memo[$key];
    }

    $cacheFile = kya_cache_dir() . DIRECTORY_SEPARATOR . 'products-' . $key . '.json';
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 20) {
        $cached = json_decode(@file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            return $memo[$key] = $cached;
        }
    }

    try {
        $rows = db_try(function (PDO $pdo) use ($activeOnly) {
            if (!ensure_products_table()) {
                return [];
            }
            $sql = 'SELECT * FROM products';
            if ($activeOnly) {
                $sql .= " WHERE statut = 'actif'";
            }
            $sql .= ' ORDER BY featured DESC, name ASC';
            return $pdo->query($sql)->fetchAll();
        });
    } catch (Exception $e) {
        if (is_file($cacheFile)) {
            $cached = json_decode(@file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                return $memo[$key] = $cached;
            }
        }
        return [];
    }

    $memo[$key] = is_array($rows) ? $rows : [];
    kya_cache_write($cacheFile, json_encode($memo[$key]));
    return $memo[$key];
}

function products_vehicles_js()
{
    $file = kya_cache_dir() . DIRECTORY_SEPARATOR . 'vehicles.js';
    if (is_file($file) && (time() - filemtime($file)) < 20) {
        return @file_get_contents($file);
    }
    $list = [];
    foreach (products_all(true) as $row) {
        $list[] = product_to_js($row);
    }
    $js = 'const VEHICLES = ' . json_encode($list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';';
    kya_cache_write($file, $js);
    return $js;
}

function product_find($id)
{
    $pdo = db();
    if (!$pdo) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $id]);
    $row = $stmt->fetch();
    return $row ? $row : null;
}

function product_to_js(array $row)
{
    $mode = [];
    if (!empty($row['offer_location'])) {
        $mode[] = 'location';
    }
    if (!empty($row['offer_vente'])) {
        $mode[] = 'vente';
    }
    return [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'brand' => $row['brand'],
        'type' => $row['type'],
        'year' => $row['year'] ? (int) $row['year'] : null,
        'seats' => (int) $row['seats'],
        'transmission' => $row['transmission'],
        'fuel' => $row['fuel'],
        'mode' => $mode,
        'priceDay' => $row['price_day'] !== null ? (int) $row['price_day'] : null,
        'priceSale' => $row['price_sale'] !== null ? (int) $row['price_sale'] : null,
        'featured' => !empty($row['featured']),
        'image' => product_image_src($row['image']),
    ];
}

function product_delete_image_file($path)
{
    if (!$path || preg_match('#^https?://#i', $path)) {
        return;
    }
    $full = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $root = realpath(dirname(__DIR__) . '/img/products');
    $real = file_exists($full) ? realpath($full) : false;
    if ($root && $real && strpos($real, $root) === 0) {
        @unlink($real);
    }
}

function product_handle_upload($field, $current = null)
{
    if (empty($_FILES[$field]) || empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $current;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$field]['tmp_name']);
        finfo_close($finfo);
    } else {
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        $map = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        $mime = isset($map[$ext]) ? $map[$ext] : '';
    }
    if (!isset($allowed[$mime]) || $_FILES[$field]['size'] > 4 * 1024 * 1024) {
        return false;
    }
    $dir = dirname(__DIR__) . '/img/products';
    if (!kya_mkdir_p($dir)) {
        return false;
    }
    $name = 'p-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        return false;
    }
    if ($current) {
        product_delete_image_file($current);
    }
    return 'img/products/' . $name;
}
