<?php
require_once __DIR__ . '/config.php';

define('KYA_SCHEMA_VERSION', 3);

function kya_tmp_dir()
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
}

function kya_mkdir_p($dir)
{
    if (is_dir($dir)) {
        return true;
    }
    if (@mkdir($dir, 0755, true) || is_dir($dir)) {
        return true;
    }
    return false;
}

function kya_cache_dir()
{
    $dir = kya_tmp_dir() . DIRECTORY_SEPARATOR . 'cache';
    kya_mkdir_p($dir);
    return $dir;
}

function kya_cache_write($file, $contents)
{
    kya_mkdir_p(dirname($file));
    $tmp = dirname($file) . DIRECTORY_SEPARATOR . '.' . basename($file) . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (@file_put_contents($tmp, $contents, LOCK_EX) === false) {
        return false;
    }
    if (!@rename($tmp, $file)) {
        @copy($tmp, $file);
        @unlink($tmp);
    }
    return true;
}

function kya_log($message, $level = 'error', $context = '')
{
    $line = date('Y-m-d H:i:s') . "\t" . $level . "\t" . $context . "\t" . str_replace(["\r", "\n"], ' ', $message) . PHP_EOL;
    $dir = kya_tmp_dir();
    kya_mkdir_p($dir);
    @file_put_contents($dir . '/kya.log', $line, FILE_APPEND | LOCK_EX);

    try {
        $pdo = isset($GLOBALS['_kya_pdo']) ? $GLOBALS['_kya_pdo'] : null;
        if ($pdo instanceof PDO) {
            $stmt = $pdo->prepare('INSERT INTO error_logs (level, message, context, ip) VALUES (?, ?, ?, ?)');
            $ip = isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : '';
            $stmt->execute([$level, substr($message, 0, 4000), substr($context, 0, 190), $ip]);
        }
    } catch (Exception $e) {
        // ne pas boucler
    }
}

function kya_lock(PDO $pdo, $name, $timeout = 8)
{
    try {
        $stmt = $pdo->prepare('SELECT GET_LOCK(?, ?)');
        $stmt->execute([substr($name, 0, 64), (int) $timeout]);
        return (int) $stmt->fetchColumn() === 1;
    } catch (PDOException $e) {
        return false;
    }
}

function kya_unlock(PDO $pdo, $name)
{
    try {
        $stmt = $pdo->prepare('SELECT RELEASE_LOCK(?)');
        $stmt->execute([substr($name, 0, 64)]);
    } catch (Exception $e) {
    }
}

function kya_pdo_errno(PDOException $e)
{
    return isset($e->errorInfo[1]) ? (int) $e->errorInfo[1] : 0;
}

function kya_is_retryable(PDOException $e)
{
    $errno = kya_pdo_errno($e);
    if (in_array($errno, [1213, 1205, 2006, 2013, 1040, 1203], true)) {
        return true;
    }
    $msg = $e->getMessage();
    return stripos($msg, 'Deadlock') !== false
        || stripos($msg, 'Lock wait timeout') !== false
        || stripos($msg, 'server has gone away') !== false
        || stripos($msg, 'Too many connections') !== false;
}

function db($reconnect = false)
{
    static $pdo = null;
    static $tried = false;
    if ($reconnect) {
        $pdo = null;
        $tried = false;
        unset($GLOBALS['_kya_pdo']);
    }
    if ($tried) {
        return $pdo;
    }
    $tried = true;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_PERSISTENT => false,
    ];
    if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
        $opts[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci';
    }
    if (defined('PDO::MYSQL_ATTR_CONNECT_TIMEOUT')) {
        $opts[PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = 5;
    }
    if (defined('PDO::MYSQL_ATTR_FOUND_ROWS')) {
        $opts[PDO::MYSQL_ATTR_FOUND_ROWS] = true;
    }

    $last = null;
    for ($i = 0; $i < 3; $i++) {
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
            try {
                $pdo->exec('SET SESSION innodb_lock_wait_timeout = 8');
            } catch (PDOException $e) {
            }
            $GLOBALS['_kya_pdo'] = $pdo;
            kya_ensure_runtime_tables($pdo);
            return $pdo;
        } catch (PDOException $e) {
            $last = $e;
            $pdo = null;
            if (!kya_is_retryable($e) && $i > 0) {
                break;
            }
            usleep(120000 * ($i + 1));
        }
    }
    if ($last) {
        kya_log($last->getMessage(), 'error', 'db.connect');
    }
    return null;
}

function db_try(callable $fn, $retries = 2)
{
    $last = null;
    for ($i = 0; $i <= $retries; $i++) {
        $pdo = db($i > 0);
        if (!$pdo) {
            usleep(100000 * ($i + 1));
            continue;
        }
        try {
            return $fn($pdo);
        } catch (PDOException $e) {
            $last = $e;
            if (!kya_is_retryable($e)) {
                kya_log($e->getMessage(), 'error', 'db.try');
                throw $e;
            }
            if (in_array(kya_pdo_errno($e), [2006, 2013, 1040, 1203], true)) {
                db(true);
            }
            usleep(100000 * ($i + 1));
        }
    }
    if ($last) {
        kya_log($last->getMessage(), 'error', 'db.try.exhausted');
        throw $last;
    }
    throw new RuntimeException('Base de données indisponible.');
}

function kya_schema_flag()
{
    return kya_tmp_dir() . DIRECTORY_SEPARATOR . 'schema.ok';
}

function kya_schema_is_current()
{
    $flag = kya_schema_flag();
    if (!is_file($flag)) {
        return false;
    }
    return (int) @file_get_contents($flag) >= KYA_SCHEMA_VERSION;
}

function kya_schema_mark_current()
{
    kya_mkdir_p(kya_tmp_dir());
    @file_put_contents(kya_schema_flag(), (string) KYA_SCHEMA_VERSION, LOCK_EX);
}

function kya_ensure_runtime_tables(PDO $pdo)
{
    static $done = false;
    if ($done) {
        return;
    }
    if (kya_schema_is_current()) {
        $done = true;
        return;
    }

    $locked = kya_lock($pdo, 'kya_schema_init', 10);
    try {
        if (kya_schema_is_current()) {
            $done = true;
            return;
        }
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS app_sessions (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                data MEDIUMBLOB NOT NULL,
                last_activity INT UNSIGNED NOT NULL,
                INDEX idx_activity (last_activity)
            ) ENGINE=InnoDB'
        );
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS error_logs (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                level VARCHAR(20) NOT NULL DEFAULT \'error\',
                message TEXT NOT NULL,
                context VARCHAR(190) DEFAULT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB'
        );
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS login_attempts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ip VARCHAR(45) NOT NULL,
                email VARCHAR(190) NOT NULL,
                attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ip_time (ip, attempted_at),
                INDEX idx_email_time (email, attempted_at)
            ) ENGINE=InnoDB'
        );
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS products (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(160) NOT NULL,
                brand VARCHAR(80) NOT NULL,
                type VARCHAR(80) NOT NULL,
                year SMALLINT UNSIGNED DEFAULT NULL,
                seats TINYINT UNSIGNED DEFAULT 5,
                transmission VARCHAR(40) DEFAULT \'Automatique\',
                fuel VARCHAR(40) DEFAULT \'Essence\',
                offer_location TINYINT(1) NOT NULL DEFAULT 1,
                offer_vente TINYINT(1) NOT NULL DEFAULT 0,
                price_day INT UNSIGNED DEFAULT NULL,
                price_sale INT UNSIGNED DEFAULT NULL,
                featured TINYINT(1) NOT NULL DEFAULT 0,
                image VARCHAR(500) DEFAULT NULL,
                statut ENUM(\'actif\',\'inactif\') NOT NULL DEFAULT \'actif\',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_products_visible (statut, featured, name)
            ) ENGINE=InnoDB'
        );
        try {
            $pdo->exec('CREATE INDEX idx_products_visible ON products (statut, featured, name)');
        } catch (PDOException $e) {
            // index déjà présent
        }
        try {
            $pdo->exec('CREATE INDEX idx_reservations_user ON reservations (user_id, created_at)');
        } catch (PDOException $e) {
        }
        try {
            $pdo->exec('CREATE INDEX idx_reservations_statut ON reservations (statut, created_at)');
        } catch (PDOException $e) {
        }
        kya_schema_mark_current();
    } catch (PDOException $e) {
        kya_log($e->getMessage(), 'error', 'db.schema');
    } finally {
        if ($locked) {
            kya_unlock($pdo, 'kya_schema_init');
        }
    }
    $done = true;
}

function db_transaction(callable $fn)
{
    return db_try(function (PDO $pdo) use ($fn) {
        $pdo->beginTransaction();
        try {
            $result = $fn($pdo);
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
            return $result;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($e instanceof PDOException && kya_is_retryable($e)) {
                throw $e;
            }
            kya_log($e->getMessage(), 'error', 'db.transaction');
            throw $e;
        }
    });
}
