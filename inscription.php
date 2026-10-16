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
        $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
        $email = strtolower(trim(isset($_POST['email']) ? $_POST['email'] : ''));
        $telephone = trim(isset($_POST['telephone']) ? $_POST['telephone'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';

        if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $error = 'Nom, email valide et mot de passe (8 caractères min.) sont requis.';
        } elseif ($password !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (find_user_by_email($email)) {
            $error = 'Un compte existe déjà avec cet email.';
        } else {
            $pdo = db();
            if (!$pdo) {
                $error = 'Base de données indisponible.';
            } else {
                try {
                    db_transaction(function ($pdo) use ($nom, $email, $telephone, $password) {
                        $pdo->prepare(
                            'INSERT INTO users (role_id, nom, email, telephone, password_hash, statut)
                             VALUES (2, ?, ?, ?, ?, ?)'
                        )->execute([$nom, $email, $telephone !== '' ? $telephone : null, password_hash($password, PASSWORD_DEFAULT), 'actif']);
                    });
                    $row = find_user_by_email($email);
                    if (!$row) {
                        $error = 'Compte créé mais connexion impossible. Connectez-vous manuellement.';
                    } else {
                        login_user($row);
                        flash('Bienvenue dans votre espace KYA CARS.');
                        redirect('/kya/espace-client/index.php');
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'Un compte existe déjà avec cet email.';
                    } else {
                        kya_log($e->getMessage(), 'error', 'inscription');
                        $error = 'Inscription momentanément impossible. Réessayez.';
                    }
                }
            }
        }
    }
}

$current = 'login';
$pageTitle = 'Inscription client | KYA CARS';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=1800&q=80" alt="">
  <div class="container">
    <p class="eyebrow">Espace client</p>
    <h1 class="gold-text">Créer un compte</h1>
  </div>
</section>

<section class="section">
  <div class="container auth-wrap">
    <form class="form" method="post" action="inscription.php">
      <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
      <?php endif; ?>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="field">
        <label>Nom complet</label>
        <input type="text" name="nom" required value="<?= e(isset($_POST['nom']) ? $_POST['nom'] : '') ?>">
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required value="<?= e(isset($_POST['email']) ? $_POST['email'] : '') ?>">
      </div>
      <div class="field">
        <label>Téléphone</label>
        <input type="tel" name="telephone" value="<?= e(isset($_POST['telephone']) ? $_POST['telephone'] : '') ?>">
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="password" required minlength="8">
      </div>
      <div class="field">
        <label>Confirmer le mot de passe</label>
        <input type="password" name="confirm" required minlength="8">
      </div>
      <button class="btn btn-gold" type="submit">Créer mon espace</button>
      <p class="auth-alt">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
