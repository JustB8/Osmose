<?php
$form = isset($_GET['form']) ? $_GET['form'] : 'login';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Création de compte</title>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/index.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="assets/connexion.css" />
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

            <form method="post" novalidate>
                <div class="form-group">
                    <label for="email">Adresse mail *</label>
                    <input type="email" id="email" name="cre_email" required />
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
                <a href="?form=register">Vous n'avez pas de compte ? Créez en un !</a>
            </p>
        </section>
    <?php endif; ?>
</main>

<main-footer></main-footer>

</body>
</html>
