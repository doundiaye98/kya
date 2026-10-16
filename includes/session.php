<?php
require_once __DIR__ . '/db.php';

class KyaSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private $pdo;
    private $lockName;
    private $locked = false;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        if ($this->locked && $this->lockName) {
            kya_unlock($this->pdo, $this->lockName);
            $this->locked = false;
            $this->lockName = null;
        }
        return true;
    }

    public function read($id)
    {
        $this->lockName = 'kya_s_' . substr(hash('sha256', (string) $id), 0, 40);
        $this->locked = kya_lock($this->pdo, $this->lockName, 5);
        try {
            $stmt = $this->pdo->prepare('SELECT data FROM app_sessions WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ? $row['data'] : '';
        } catch (PDOException $e) {
            kya_log($e->getMessage(), 'error', 'session.read');
            return '';
        }
    }

    public function write($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO app_sessions (id, data, last_activity)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE data = VALUES(data), last_activity = VALUES(last_activity)'
            );
            return $stmt->execute([$id, $data, time()]);
        } catch (PDOException $e) {
            kya_log($e->getMessage(), 'error', 'session.write');
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM app_sessions WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function gc($maxlifetime)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM app_sessions WHERE last_activity < ?');
            $stmt->execute([time() - (int) $maxlifetime]);
            $this->pdo->exec('DELETE FROM error_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $this->pdo->exec('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 2 DAY)');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function validateId($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT 1 FROM app_sessions WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateTimestamp($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE app_sessions SET last_activity = ? WHERE id = ?');
            return $stmt->execute([time(), $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

function kya_session_start()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('KYASESSID');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.lazy_write', '1');
    ini_set('session.gc_maxlifetime', '7200');
    ini_set('session.gc_probability', '1');
    ini_set('session.gc_divisor', '200');
    session_set_cookie_params(7200, KYA_BASE . '/', '', $secure, true);

    $pdo = db();
    if ($pdo) {
        session_set_save_handler(new KyaSessionHandler($pdo), true);
    } else {
        $dir = kya_tmp_dir() . DIRECTORY_SEPARATOR . 'sessions';
        kya_mkdir_p($dir);
        session_save_path($dir);
    }

    session_start();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
}

function kya_session_close()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
}
