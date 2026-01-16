<?php
session_start();
require_once __DIR__ . '/call_bdd.php';

$form = isset($_GET['form']) ? $_GET['form'] : 'login';
$error = null;

/**
 * Liste des entreprises (pour le select)
 * (On la charge à chaque affichage)
 */
$companies = db_all('SELECT id, name FROM company ORDER BY name');

/**
 * TRAITEMENT CONNEXION
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['co_email'], $_POST['co_password'])) {
    $email = trim($_POST['co_email']);
    $password = $_POST['co_password'];

    $user = db_one(
            'SELECT id, company_id, name, email, passwd, level, experience
         FROM users
         WHERE email = :email',
            ['email' => $email]
    );

    if ($user && password_verify($password, $user['passwd'])) {
        $_SESSION['user'] = [
                'id'         => (int)$user['id'],
                'company_id' => $user['company_id'] !== null ? (int)$user['company_id'] : null,
                'name'       => $user['name'],
                'email'      => $user['email'],
                'level'      => (int)$user['level'],
                'experience' => (int)$user['experience'],
        ];

        header('Location: /dashboard.php');
        exit;
    } else {
        $error = "Adresse mail ou mot de passe incorrect.";
    }
}

/**
 * TRAITEMENT INSCRIPTION
 * Champs attendus :
 * - cre_name
 * - cre_email
 * - cre_password
 * - password-confirm
 * - company_id (select)
 * - company_new (input texte)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cre_name'], $_POST['cre_email'], $_POST['cre_password'])) {
    $form = 'register';

    $name = trim($_POST['cre_name']);
    $email = trim($_POST['cre_email']);
    $password = $_POST['cre_password'];
    $confirm = $_POST['password-confirm'] ?? '';

    $companyId = isset($_POST['company_id']) && $_POST['company_id'] !== '' ? (int)$_POST['company_id'] : null;
    $companyNew = trim($_POST['company_new'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = "Merci de remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse mail invalide.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif ($companyId === null && $companyNew === '') {
        $error = "Merci de sélectionner une entreprise ou d’en ajouter une.";
    } else {
        $exists = db_one('SELECT id FROM users WHERE email = :email', ['email' => $email]);
        if ($exists) {
            $error = "Un compte existe déjà avec cette adresse.";
        } else {
            try {
                db()->beginTransaction();

                // 1) Si l'utilisateur ajoute une nouvelle entreprise, on la crée et on récupère son id
                if ($companyId === null && $companyNew !== '') {
                    // Optionnel : éviter les doublons (insensible à la casse)
                    $existingCompany = db_one(
                            'SELECT id FROM company WHERE LOWER(name) = LOWER(:name)',
                            ['name' => $companyNew]
                    );

                    if ($existingCompany) {
                        $companyId = (int)$existingCompany['id'];
                    } else {
                        $created = db_one(
                                'INSERT INTO company (name) VALUES (:name) RETURNING id',
                                ['name' => $companyNew]
                        );
                        $companyId = (int)$created['id'];
                    }
                }

                // 2) Création user
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // level/experience à 1/0
                db_exec(
                        'INSERT INTO users (company_id, name, email, passwd, level, experience)
                     VALUES (:company_id, :name, :email, :passwd, 1, 0)',
                        [
                                'company_id' => $companyId,
                                'name'       => $name,
                                'email'      => $email,
                                'passwd'     => $hash
                        ]
                );

                db()->commit();

                // Redirige vers le login
                header('Location: /connexion.php?form=login');
                exit;

            } catch (Throwable $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $error = "Erreur lors de la création du compte. Réessayez.";
            }
        }
    }
}

// Recharge la liste des entreprises après un ajout
$companies = db_all('SELECT id, name FROM company ORDER BY name');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion / Création de compte</title>
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/connexion.css" />
    <link rel="stylesheet" href="./assets/css/header.css"/>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/index.css" />
    <script src="assets/js/composents.js"></script>
    <script>
        window.IS_LOGGED = false;
    </script>
</head>

<body>
<main-header></main-header>

<main>
    <?php if ($form === 'register'): ?>
        <section class="auth-section">
            <h1>Création de compte</h1>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="form-group">
                    <label for="name">Nom *</label>
                    <input type="text" id="name" name="cre_name" required />
                </div>

                <div class="form-group">
                    <label for="email">Adresse mail *</label>
                    <input type="email" id="email" name="cre_email" required />
                </div>

                <div class="form-group">
                    <label for="company_id">Entreprise *</label>
                    <select id="company_id" name="company_id">
                        <option value="">-- Choisir une entreprise --</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= (int)$c['id'] ?>">
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small>Ou ajoutez-en une ci-dessous :</small>
                </div>

                <div class="form-group">
                    <label for="company_new">Ajouter une entreprise (optionnel)</label>
                    <input type="text" id="company_new" name="company_new" placeholder="Nom de l’entreprise" />
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="cre_password" required />
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirmation du mot de passe *</label>
                    <input type="password" id="password-confirm" name="password-confirm" required />
                </div>

                <button type="submit" class="btn btn-primary">
                    Créer un compte
                </button>
            </form>

            <p>
                <a href="?form=login">Vous avez déjà un compte ? Connectez-vous</a>
            </p>
        </section>
    <?php else: ?>
        <section class="auth-section">
            <h1>Connexion</h1>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="form-group">
                    <label for="email">Adresse mail *</label>
                    <input type="email" id="email" name="co_email" required />
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="co_password" required />
                </div>

                <button type="submit" class="btn btn-primary">
                    Connexion
                </button>
            </form>

            <p>
                <a href="?form=register">Vous n'avez pas de compte ? Créez-en un !</a>
            </p>
        </section>
    <?php endif; ?>
</main>

<main-footer></main-footer>
</body>
</html>
