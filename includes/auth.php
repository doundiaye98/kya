<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';
kya_session_start();

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check($token)
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $token);
}

function flash($message, $type = 'ok')
{
    $_SESSION['flash'] = ['msg' => $message, 'type' => $type];
}

function take_flash()
{
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    return $flash;
}

function current_user()
{
    if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }
    return $_SESSION['user'];
}

function is_logged_in()
{
    return current_user() !== null;
}

function user_role()
{
    $user = current_user();
    return $user ? $user['role'] : null;
}

function has_role($role)
{
    return user_role() === $role;
}

function apply_user_session(array $row)
{
    $_SESSION['user'] = [
        'id' => (int) $row['id'],
        'nom' => $row['nom'],
        'email' => $row['email'],
        'telephone' => $row['telephone'],
        'role' => $row['role_slug'],
        'role_label' => $row['role_label'],
        'checked_at' => time(),
    ];
}

function login_user(array $row)
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    apply_user_session($row);
}

function logout_user()
{
    unset($_SESSION['user']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

function redirect($path)
{
    kya_session_close();
    header('Location: ' . $path);
    exit;
}

function kya_client_ip()
{
    return isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : '0.0.0.0';
}

function login_is_throttled($email)
{
    $pdo = db();
    if (!$pdo) {
        return false;
    }
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE attempted_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)
           AND (ip = ? OR email = ?)'
    );
    $stmt->execute([kya_client_ip(), strtolower($email)]);
    return (int) $stmt->fetchColumn() >= 12;
}

function login_record_attempt($email)
{
    $pdo = db();
    if (!$pdo) {
        return;
    }
    $pdo->prepare('INSERT INTO login_attempts (ip, email) VALUES (?, ?)')->execute([kya_client_ip(), strtolower($email)]);
}

function login_clear_attempts($email)
{
    $pdo = db();
    if (!$pdo) {
        return;
    }
    $pdo->prepare('DELETE FROM login_attempts WHERE ip = ? OR email = ?')->execute([kya_client_ip(), strtolower($email)]);
}

function require_login()
{
    if (!is_logged_in()) {
        flash('Connectez-vous pour accéder à votre espace.', 'error');
        redirect('/kya/login.php');
    }
    sync_current_user();
    if (!is_logged_in()) {
        flash('Votre session n’est plus valide. Reconnectez-vous.', 'error');
        redirect('/kya/login.php');
    }
}

function sync_current_user()
{
    $user = current_user();
    if (!$user) {
        return;
    }
    $last = isset($user['checked_at']) ? (int) $user['checked_at'] : 0;
    if (time() - $last < 45) {
        return;
    }
    $pdo = db();
    if (!$pdo) {
        return;
    }
    try {
        $stmt = $pdo->prepare(
            'SELECT u.*, r.slug AS role_slug, r.label AS role_label
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? LIMIT 1'
        );
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();
        if (!$row || $row['statut'] !== 'actif') {
            logout_user();
            kya_session_start();
            return;
        }
        apply_user_session($row);
    } catch (PDOException $e) {
        kya_log($e->getMessage(), 'error', 'auth.sync');
    }
}

function require_role($role)
{
    require_login();
    if (!has_role($role)) {
        if (has_role('admin')) {
            redirect('/kya/admin/index.php');
        }
        redirect('/kya/espace-client/index.php');
    }
}

function find_user_by_email($email)
{
    $pdo = db();
    if (!$pdo) {
        return null;
    }
    $stmt = $pdo->prepare(
        'SELECT u.*, r.slug AS role_slug, r.label AS role_label
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE u.email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    return $row ? $row : null;
}

function refresh_session_user()
{
    $user = current_user();
    if (!$user) {
        return;
    }
    $pdo = db();
    if (!$pdo) {
        return;
    }
    $stmt = $pdo->prepare(
        'SELECT u.*, r.slug AS role_slug, r.label AS role_label
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE u.id = ? LIMIT 1'
    );
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch();
    if ($row && $row['statut'] === 'actif') {
        apply_user_session($row);
    } else {
        logout_user();
    }
}

function home_for_role($role)
{
    if ($role === 'admin') {
        return '/kya/admin/index.php';
    }
    return '/kya/espace-client/index.php';
}

function vehicle_catalog()
{
    require_once __DIR__ . '/products.php';
    $names = [];
    foreach (products_all(true) as $row) {
        $names[] = $row['name'];
    }
    return $names ? $names : ['Véhicule sur demande'];
}
