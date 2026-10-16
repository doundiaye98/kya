<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(home_for_role(user_role()));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Session expirée. Veuillez réessayer.';
    } else {
        $email = strtolower(trim(isset($_POST['email']) ? $_POST['email'] : ''));
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (!db()) {
            $error = 'Base de données temporairement indisponible. Réessayez dans un instant.';
        } elseif (login_is_throttled($email)) {
            $error = 'Trop de tentatives. Attendez quelques minutes avant de réessayer.';
        } else {
            $row = find_user_by_email($email);
            if (!$row || !password_verify($password, $row['password_hash'])) {
                login_record_attempt($email);
                $error = 'Identifiants incorrects.';
            } elseif ($row['statut'] !== 'actif') {
                $error = 'Ce compte est désactivé. Contactez KYA CARS.';
            } else {
                login_clear_attempts($email);
                login_user($row);
                try {
                    db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$row['id']]);
                } catch (PDOException $e) {
                    kya_log($e->getMessage(), 'warning', 'login.last_login');
                }
                redirect(home_for_role($row['role_slug']));
            }
        }
    }
}

$current = 'login';
$pageTitle = 'Connexion | KYA CARS';
$flash = take_flash();
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=1800&q=80" alt="">
  <div class="container">
    <p class="eyebrow">Espace</p>
    <h1 class="gold-text">Connexion</h1>
  </div>
</section>

<section class="section">
  <div class="container auth-wrap">
    <form class="form" method="post" action="login.php">
      <?php if ($flash): ?>
        <p class="form-success show"><?= e($flash['msg']) ?></p>
      <?php endif; ?>
      <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
      <?php endif; ?>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required value="<?= e(isset($_POST['email']) ? $_POST['email'] : '') ?>">
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn btn-gold" type="submit">Se connecter</button>
      <p class="auth-alt">Pas encore de compte ? <a href="inscription.php">Créer un espace client</a></p>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
