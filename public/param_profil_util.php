<?php
declare(strict_types=1);

require_once __DIR__ . '/call_bdd.php';

session_start();

// TODO: mettre l'id du user connecté dans la session lors du login uniquement (retirer ?? 1).
$userId = $_SESSION['user_id'] ?? 1; // fallback pour tes tests

$success = null;
$errors = [];

// Charger l'utilisateur (pour pré-remplir le formulaire)
$user = db_one('SELECT id, company_id, name, email, passwd FROM public.users WHERE id = :id', [':id' => $userId]);
if (!$user) {
    http_response_code(404);
    exit('Utilisateur introuvable');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $entreprise = trim($_POST['entreprise'] ?? '');

        $newPass = (string)($_POST['new_password'] ?? '');
        $newPassConfirm = (string)($_POST['new_password_confirm'] ?? '');

        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($prenom === '') $errors[] = "Le prénom est obligatoire.";
        if ($entreprise === '') $errors[] = "L'entreprise est obligatoire.";

        // Construire le champ "name" selon ton schéma actuel
        $fullName = trim($nom . ' ' . $prenom);

        // Gestion mot de passe (optionnel)
        $updatePassword = false;
        $hashed = null;

        if ($newPass !== '' || $newPassConfirm !== '') {
            if ($newPass !== $newPassConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            } elseif (mb_strlen($newPass) < 6) {
                $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
            } else {
                $updatePassword = true;
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            }
        }

        if (!$errors) {
            if ($updatePassword) {
                db_exec(
                        'UPDATE public.users SET name = :name, passwd = :passwd WHERE id = :id',
                        [':name' => $fullName, ':passwd' => $hashed, ':id' => $userId]
                );
            } else {
                db_exec(
                        'UPDATE public.users SET name = :name WHERE id = :id',
                        [':name' => $fullName, ':id' => $userId]
                );
            }

            $success = "Profil mis à jour.";
            // Recharger pour affichage à jour
            $user = db_one('SELECT id, company_id, name, email, passwd FROM public.users WHERE id = :id', [':id' => $userId]);
        }
    }

    if ($action === 'delete') {
        db_exec('DELETE FROM public.users WHERE id = :id', [':id' => $userId]);

        // Si tu as une session, on la détruit
        session_destroy();

        $success = "Compte supprimé.";
        // Option: rediriger vers une page d'accueil
        // header('Location: /');
        // exit;
    }
}

// Pré-remplissage : on essaye de couper "name" en 2 parties (simple pour tests)
$nameParts = preg_split('/\s+/', trim((string)$user['name']), 2);
$prefNom = $nameParts[0] ?? '';
$prefPrenom = $nameParts[1] ?? '';
$prefEntreprise = ''; // pas en BDD dans ton schéma actuel
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres profil</title>
    <link rel="stylesheet" href="assets/css/param_profil_util.css">
</head>

<body>
<main class="ppu-page">
    <section class="ppu-card" aria-label="Paramètres du compte">

        <?php if ($success): ?>
            <div class="ppu-alert ppu-alert--success" role="status">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="ppu-alert ppu-alert--error" role="alert">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="ppu-form" method="post" autocomplete="off">
            <div class="ppu-grid">
                <div class="ppu-field">
                    <label for="nom">Nom :</label>
                    <input id="nom" name="nom" type="text" value="<?= htmlspecialchars($prefNom) ?>" required>
                </div>

                <div class="ppu-field">
                    <label for="new_password">Nouveau mot de passe :</label>
                    <input
                            id="new_password"
                            name="new_password"
                            type="password"
                            placeholder="nouveau mot de passe"
                            autocomplete="new-password"
                    >
                </div>

                <div class="ppu-field">
                    <label for="prenom">Prénom :</label>
                    <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($prefPrenom) ?>" required>
                </div>

                <div class="ppu-field">
                    <label for="new_password_confirm">Confirmation du nouveau mot de passe :</label>
                    <input
                            id="new_password_confirm"
                            name="new_password_confirm"
                            type="password"
                            placeholder="confirmer le mot de passe"
                            autocomplete="new-password"
                    >
                </div>

                <div class="ppu-field ppu-field--center">
                    <label for="entreprise">Entreprise :</label>
                    <input id="entreprise" name="entreprise" type="text" value="<?= htmlspecialchars($prefEntreprise) ?>" required>
                </div>
            </div>

            <div class="ppu-actions">
                <button class="ppu-btn" type="submit" name="action" value="update">Submit</button>

                <button
                        class="ppu-btn ppu-btn--delete"
                        type="submit"
                        name="action"
                        value="delete"
                        onclick="return confirm('Confirmer la suppression du compte ?');"
                >
                    Supprimer le compte
                </button>
            </div>
        </form>
    </section>
</main>
</body>
</html>
