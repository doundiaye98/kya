<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('client');

$pdo = db();
$uid = current_user()['id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (!csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Session expirée.';
    } else {
        $vehicule = trim(isset($_POST['vehicule']) ? $_POST['vehicule'] : '');
        $type = isset($_POST['type_offre']) && $_POST['type_offre'] === 'vente' ? 'vente' : 'location';
        $debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : null;
        $fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : null;
        $message = trim(isset($_POST['message']) ? $_POST['message'] : '');
        if ($vehicule === '') {
            $error = 'Choisissez un véhicule.';
        } else {
            try {
                db_transaction(function ($pdo) use ($uid, $vehicule, $type, $debut, $fin, $message) {
                    $pdo->prepare(
                        'INSERT INTO reservations (user_id, vehicule, type_offre, date_debut, date_fin, message, statut)
                         VALUES (?, ?, ?, ?, ?, ?, ?)'
                    )->execute([
                        $uid,
                        $vehicule,
                        $type,
                        $debut !== '' ? $debut : null,
                        $fin !== '' ? $fin : null,
                        $message !== '' ? $message : null,
                        'en_attente',
                    ]);
                });
                flash('Votre demande a été envoyée. Un conseiller KYA vous recontacte.');
                redirect('/kya/espace-client/reservations.php');
            } catch (Exception $e) {
                $error = 'La demande n’a pas pu être enregistrée. Réessayez.';
            }
        }
    }
}

$list = [];
if ($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$uid]);
    $list = $stmt->fetchAll();
}

$appTitle = 'Mes réservations';
$appNav = 'reservations';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text">Mes réservations</h1>
<p class="muted">Location ou achat : déposez une demande, nous confirmons rapidement.</p>

<?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>

<form class="form two" method="post">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <div class="field">
    <label>Véhicule</label>
    <select name="vehicule" required>
      <option value="">Choisir</option>
      <?php foreach (vehicle_catalog() as $name): ?>
        <option value="<?= e($name) ?>"><?= e($name) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field">
    <label>Offre</label>
    <select name="type_offre">
      <option value="location">Location</option>
      <option value="vente">Achat</option>
    </select>
  </div>
  <div class="field">
    <label>Date de début</label>
    <input type="date" name="date_debut">
  </div>
  <div class="field">
    <label>Date de fin</label>
    <input type="date" name="date_fin">
  </div>
  <div class="field full">
    <label>Message</label>
    <textarea name="message" rows="3" placeholder="Besoins particuliers, chauffeur, livraison…"></textarea>
  </div>
  <div class="full">
    <button class="btn btn-gold" type="submit">Envoyer la demande</button>
  </div>
</form>

<h2>Historique</h2>
<?php if (!$list): ?>
  <p class="muted">Aucune demande enregistrée.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Véhicule</th><th>Offre</th><th>Période</th><th>Statut</th><th>Créée le</th></tr>
      </thead>
      <tbody>
        <?php foreach ($list as $r): ?>
          <tr>
            <td><?= e($r['vehicule']) ?></td>
            <td><?= e($r['type_offre']) ?></td>
            <td><?= e(($r['date_debut'] ? $r['date_debut'] : '—') . ' → ' . ($r['date_fin'] ? $r['date_fin'] : '—')) ?></td>
            <td><span class="pill pill-<?= e($r['statut']) ?>"><?= e(str_replace('_', ' ', $r['statut'])) ?></span></td>
            <td><?= e($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
