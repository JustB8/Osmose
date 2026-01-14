<?php
declare(strict_types=1);




session_start();
require_once __DIR__ . '/call_bdd.php';
$_SESSION['user']['id'] = 1;
$_SESSION['user']['name'] = "root";
$isLogged = isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id']);
if (!$isLogged) {
    header('Location: /connexion.php?form=login');
    exit;
}

$userId = (int)$_SESSION['user']['id'];

$success = null;
$errors = [];
$companyId = null;

// Charge user + entreprise (on récupère passwd pour vérifier l'ancien mdp si besoin)
$user = db_one(
        'SELECT u.id, u.company_id, u.name, u.email, u.passwd,
            c.name AS company_name
     FROM public.users u
     LEFT JOIN public.company c ON c.id = u.company_id
     WHERE u.id = :id',
        ['id' => $userId]
);

if (!$user) {
    http_response_code(404);
    exit('Utilisateur introuvable');
}

// Liste entreprises (select)
$companies = db_all('SELECT id, name FROM public.company ORDER BY name ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update') {
        $nom = trim((string)($_POST['nom'] ?? ''));
        $prenom = trim((string)($_POST['prenom'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));

        // Entreprise : select + ajout
        $selectedCompanyId = trim((string)($_POST['company_id'] ?? '')); // '' ou id
        $companyNew = trim((string)($_POST['company_new'] ?? ''));

        $currentPass = (string)($_POST['current_password'] ?? '');
        $newPass = (string)($_POST['new_password'] ?? '');
        $newPassConfirm = (string)($_POST['new_password_confirm'] ?? '');

        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($prenom === '') $errors[] = "Le prénom est obligatoire.";

        if ($email === '') {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide.";
        }

        // Entreprise : il faut soit choisir, soit ajouter
        if ($selectedCompanyId === '' && $companyNew === '') {
            $errors[] = "Merci de sélectionner une entreprise ou d’en ajouter une.";
        }

        $fullName = trim($nom . ' ' . $prenom);

        // Si email changé -> demander l'ancien mot de passe + vérifier unicité
        $emailChanged = (mb_strtolower($email) !== mb_strtolower((string)$user['email']));
        if ($emailChanged) {
            if ($currentPass === '') {
                $errors[] = "Veuillez saisir votre ancien mot de passe pour modifier l'email.";
            } elseif (!password_verify($currentPass, (string)$user['passwd'])) {
                $errors[] = "Ancien mot de passe incorrect.";
            } else {
                $exists = db_one(
                        'SELECT id FROM public.users WHERE lower(email) = lower(:email) AND id <> :id LIMIT 1',
                        ['email' => $email, 'id' => $userId]
                );
                if ($exists) {
                    $errors[] = "Cet email est déjà utilisé.";
                }
            }
        }

        // Mot de passe optionnel (mais si on change, on demande l'ancien)
        $updatePassword = false;
        $hashed = null;

        if ($newPass !== '' || $newPassConfirm !== '') {
            if ($currentPass === '') {
                $errors[] = "Veuillez saisir votre ancien mot de passe pour le modifier.";
            } elseif (!password_verify($currentPass, (string)$user['passwd'])) {
                $errors[] = "Ancien mot de passe incorrect.";
            } elseif ($newPass !== $newPassConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            } elseif (mb_strlen($newPass) < 6) {
                $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
            } else {
                $updatePassword = true;
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            }
        }

        // Résolution / création entreprise
        if (!$errors) {
            if ($companyNew !== '') {
                // Si l'utilisateur saisit une nouvelle entreprise, elle est prioritaire
                $companyNameNorm = preg_replace('/\s+/', ' ', $companyNew);
                $companyNameNorm = trim((string)$companyNameNorm);

                $companyRow = db_one(
                        'SELECT id FROM public.company WHERE lower(name) = lower(:n) LIMIT 1',
                        ['n' => $companyNameNorm]
                );

                if ($companyRow) {
                    $companyId = (int)$companyRow['id'];
                } else {
                    try {
                        $created = db_one(
                                'INSERT INTO public.company (name) VALUES (:n) RETURNING id',
                                ['n' => $companyNameNorm]
                        );
                        $companyId = (int)$created['id'];
                    } catch (\Throwable $e) {
                        $companyRow = db_one(
                                'SELECT id FROM public.company WHERE lower(name) = lower(:n) LIMIT 1',
                                ['n' => $companyNameNorm]
                        );
                        if (!$companyRow) {
                            $errors[] = "Impossible de créer / retrouver l'entreprise.";
                        } else {
                            $companyId = (int)$companyRow['id'];
                        }
                    }
                }
            } else {
                // Sinon, utiliser le select
                $companyId = (int)$selectedCompanyId;
                $exists = db_one('SELECT id FROM public.company WHERE id = :id', ['id' => $companyId]);
                if (!$exists) {
                    $errors[] = "Entreprise sélectionnée invalide.";
                }
            }
        }

        // Update user
        if (!$errors) {
            if ($updatePassword) {
                db_exec(
                        'UPDATE public.users
                     SET name = :name, email = :email, company_id = :company_id, passwd = :passwd
                     WHERE id = :id',
                        [
                                'name' => $fullName,
                                'email' => $email,
                                'company_id' => $companyId,
                                'passwd' => $hashed,
                                'id' => $userId,
                        ]
                );
            } else {
                db_exec(
                        'UPDATE public.users
                     SET name = :name, email = :email, company_id = :company_id
                     WHERE id = :id',
                        [
                                'name' => $fullName,
                                'email' => $email,
                                'company_id' => $companyId,
                                'id' => $userId,
                        ]
                );
            }

            // Met à jour la session
            $_SESSION['user']['name'] = $fullName;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['company_id'] = $companyId;

            $success = "Profil mis à jour.";

            // Reload user + liste entreprises
            $user = db_one(
                    'SELECT u.id, u.company_id, u.name, u.email, u.passwd,
                        c.name AS company_name
                 FROM public.users u
                 LEFT JOIN public.company c ON c.id = u.company_id
                 WHERE u.id = :id',
                    ['id' => $userId]
            );

            $companies = db_all('SELECT id, name FROM public.company ORDER BY name ASC');
        }
    }

    if ($action === 'delete') {
        db_exec('DELETE FROM public.users WHERE id = :id', ['id' => $userId]);
        $_SESSION = [];
        session_destroy();
        header('Location: /index.php');
        exit;
    }
}

// Prefill nom/prenom depuis users.name
$name = trim((string)$user['name']);
$parts = preg_split('/\s+/', $name);
$prefNom = $parts[0] ?? '';
$prefPrenom = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

$prefEmail = (string)$user['email'];
$prefCompanyId = (int)($user['company_id'] ?? 0);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Paramètres profil</title>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
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
                    <label for="prenom">Prénom :</label>
                    <input id="prenom" name="prenom" type="text" value="<?= htmlspecialchars($prefPrenom) ?>" required>
                </div>

                <div class="ppu-field">
                    <label for="email">Email :</label>
                    <input id="email" name="email" type="email" value="<?= htmlspecialchars($prefEmail) ?>" required>
                </div>

                <div class="ppu-field">
                    <label for="current_password">Ancien mot de passe :</label>
                    <input id="current_password" name="current_password" type="password"
                           placeholder="obligatoire si email / mot de passe changé" autocomplete="current-password">
                </div>

                <div class="ppu-field">
                    <label for="new_password">Nouveau mot de passe :</label>
                    <input id="new_password" name="new_password" type="password"
                           placeholder="nouveau mot de passe" autocomplete="new-password">
                </div>

                <div class="ppu-field">
                    <label for="new_password_confirm">Confirmation du nouveau mot de passe :</label>
                    <input id="new_password_confirm" name="new_password_confirm" type="password"
                           placeholder="confirmer le mot de passe" autocomplete="new-password">
                </div>

                <!-- ENTREPRISE -->
                <div class="ppu-field ppu-field--center">
                    <label for="company_id">Entreprise :</label>

                    <select id="company_id" name="company_id">
                        <option value="">-- Choisir une entreprise --</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= ((int)$c['id'] === $prefCompanyId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string)$c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <small style="margin-top:6px; opacity:.75;">
                        Ou ajoutez-en une nouvelle :
                    </small>

                    <input
                            id="company_new"
                            name="company_new"
                            type="text"
                            placeholder="Nom de la nouvelle entreprise"
                            autocomplete="off"
                            style="margin-top:6px;"
                    >
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

    </section>
</main>

<main-footer></main-footer>
</body>
</html>
