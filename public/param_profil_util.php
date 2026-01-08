<?php
declare(strict_types=1);

require_once __DIR__ . '/call_bdd.php';

session_start();
$isLogged = isset($_SESSION['user']);

$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    http_response_code(401);
    exit('Non connecté');
}

$success = null;
$errors = [];
$companyId = null;

// Charge user + entreprise
$user = db_one(
    'SELECT u.id, u.company_id, u.name, u.email,
            c.name AS company_name
     FROM public.users u
     LEFT JOIN public.company c ON c.id = u.company_id
     WHERE u.id = :id',
    [':id' => $userId]
);

if (!$user) {
    http_response_code(404);
    exit('Utilisateur introuvable');
}

// Liste entreprises (datalist)
$companies = db_all('SELECT name FROM public.company ORDER BY name ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update') {
        $nom = trim((string)($_POST['nom'] ?? ''));
        $prenom = trim((string)($_POST['prenom'] ?? ''));
        $companyName = trim((string)($_POST['company_name'] ?? ''));

        $newPass = (string)($_POST['new_password'] ?? '');
        $newPassConfirm = (string)($_POST['new_password_confirm'] ?? '');

        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($prenom === '') $errors[] = "Le prénom est obligatoire.";
        if ($companyName === '') $errors[] = "L'entreprise est obligatoire.";

        $fullName = trim($nom . ' ' . $prenom);

        // Mot de passe optionnel
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

        // Résolution / création entreprise (obligatoire)
        if (!$errors) {
            $companyNameNorm = preg_replace('/\s+/', ' ', $companyName);
            $companyNameNorm = trim((string)$companyNameNorm);

            $companyRow = db_one(
                'SELECT id FROM public.company WHERE lower(name) = lower(:n) LIMIT 1',
                [':n' => $companyNameNorm]
            );

            if ($companyRow) {
                $companyId = (int)$companyRow['id'];
            } else {
                try {
                    db_exec(
                        'INSERT INTO public.company (name) VALUES (:n)',
                        [':n' => $companyNameNorm]
                    );
                } catch (\Throwable $e) {
                    // ignore : si UNIQUE violation, elle a été créée en parallèle
                }

                $companyRow = db_one(
                    'SELECT id FROM public.company WHERE lower(name) = lower(:n) LIMIT 1',
                    [':n' => $companyNameNorm]
                );

                if (!$companyRow) {
                    $errors[] = "Impossible de créer / retrouver l'entreprise.";
                } else {
                    $companyId = (int)$companyRow['id'];
                }
            }
        }

        // Update user
        if (!$errors) {
            if ($updatePassword) {
                db_exec(
                    'UPDATE public.users
                     SET name = :name, company_id = :company_id, passwd = :passwd
                     WHERE id = :id',
                    [
                        ':name' => $fullName,
                        ':company_id' => $companyId,
                        ':passwd' => $hashed,
                        ':id' => $userId,
                    ]
                );
            } else {
                db_exec(
                    'UPDATE public.users
                     SET name = :name, company_id = :company_id
                     WHERE id = :id',
                    [
                        ':name' => $fullName,
                        ':company_id' => $companyId,
                        ':id' => $userId,
                    ]
                );
            }

            $success = "Profil mis à jour.";

            // Reload user
            $user = db_one(
                'SELECT u.id, u.company_id, u.name, u.email,
                        c.name AS company_name
                 FROM public.users u
                 LEFT JOIN public.company c ON c.id = u.company_id
                 WHERE u.id = :id',
                [':id' => $userId]
            );

            // Reload companies (si ajout possible)
            $companies = db_all('SELECT name FROM public.company ORDER BY name ASC');
        }
    }

    if ($action === 'delete') {
        db_exec('DELETE FROM public.users WHERE id = :id', [':id' => $userId]);
        session_destroy();
        $success = "Compte supprimé.";
        // header('Location: /'); exit;
    }
}

// Prefill nom/prenom depuis users.name
$nameParts = preg_split('/\s+/', trim((string)$user['name']), 2);
$prefNom = $nameParts[0] ?? '';
$prefPrenom = $nameParts[1] ?? '';
$prefCompanyName = (string)($user['company_name'] ?? '');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres profil</title>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="assets/css/param_profil_util.css">
    <script>
        window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
</head>

<body>
<main-header></main-header>
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
                    <input id="new_password" name="new_password" type="password"
                           placeholder="nouveau mot de passe" autocomplete="new-password">
                </div>

                <div class="ppu-field">
                    <label for="prenom">Prénom :</label>
                    <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($prefPrenom) ?>" required>
                </div>

                <div class="ppu-field">
                    <label for="new_password_confirm">Confirmation du nouveau mot de passe :</label>
                    <input id="new_password_confirm" name="new_password_confirm" type="password"
                           placeholder="confirmer le mot de passe" autocomplete="new-password">
                </div>

                <div class="ppu-field ppu-field--center">
                    <label for="company_name">Entreprise :</label>
                    <input
                            id="company_name"
                            name="company_name"
                            type="text"
                            list="companies"
                            value="<?= htmlspecialchars($prefCompanyName) ?>"
                            required
                            placeholder="Commence à taper…"
                            autocomplete="off"
                    >
                    <datalist id="companies">
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= htmlspecialchars((string)$c['name']) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="ppu-actions">
                <button class="ppu-btn" type="submit" name="action" value="update">Submit</button>

                <button class="ppu-btn ppu-btn--delete" type="submit" name="action" value="delete"
                        onclick="return confirm('Confirmer la suppression du compte ?');">
                    Supprimer le compte
                </button>
            </div>
        </form>

        <p style="margin-top: 1rem;">
            Email : <strong><?= htmlspecialchars((string)$user['email']) ?></strong>
        </p>

    </section>
</main>
<main-footer></main-footer>
</body>
</html>
