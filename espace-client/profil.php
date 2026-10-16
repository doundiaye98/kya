<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('client');

$pdo = db();
$user = current_user();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (!csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Session expirée.';
    } else {
        $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
        $telephone = trim(isset($_POST['telephone']) ? $_POST['telephone'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if ($nom === '') {
            $error = 'Le nom est obligatoire.';
        } else {
            if ($password !== '') {
                if (strlen($password) < 8) {
                    $error = 'Le mot de passe doit contenir au moins 8 caractères.';
                } else {
                    try {
                        db_transaction(function ($pdo) use ($nom, $telephone, $password, $user) {
                            $pdo->prepare('UPDATE users SET nom = ?, telephone = ?, password_hash = ? WHERE id = ?')
                                ->execute([$nom, $telephone !== '' ? $telephone : null, password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                        });
                    } catch (Exception $e) {
                        $error = 'Enregistrement impossible pour le moment. Réessayez.';
                    }
                }
            } else {
                try {
                    db_transaction(function ($pdo) use ($nom, $telephone, $user) {
                        $pdo->prepare('UPDATE users SET nom = ?, telephone = ? WHERE id = ?')
                            ->execute([$nom, $telephone !== '' ? $telephone : null, $user['id']]);
                    });
                } catch (Exception $e) {
                    $error = 'Enregistrement impossible pour le moment. Réessayez.';
                }
            }
            if ($error === '') {
                refresh_session_user();
                flash('Profil mis à jour.');
                redirect('/kya/espace-client/profil.php');
            }
        }
    }
}

$appTitle = 'Mon profil';
$appNav = 'profil';
require dirname(__DIR__) . '/includes/header-app.php';
$user = current_user();
?>

<h1 class="gold-text">Mon profil</h1>
<p class="muted">Vos informations restent confidentielles et servent uniquement à vos dossiers KYA.</p>

<?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>

<form class="form" method="post">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <div class="field">
    <label>Nom</label>
    <input type="text" name="nom" required value="<?= e($user['nom']) ?>">
  </div>
  <div class="field">
    <label>Email</label>
    <input type="email" value="<?= e($user['email']) ?>" disabled>
  </div>
  <div class="field">
    <label>Téléphone</label>
    <input type="tel" name="telephone" value="<?= e($user['telephone']) ?>">
  </div>
  <div class="field">
    <label>Nouveau mot de passe (optionnel)</label>
    <input type="password" name="password" minlength="8" placeholder="Laisser vide pour ne pas changer">
  </div>
  <button class="btn btn-gold" type="submit">Enregistrer</button>
</form>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
