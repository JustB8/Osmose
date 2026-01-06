<?php
$form = isset($_GET['form']) ? $_GET['form'] : 'login';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Création de compte</title>
    <link rel="stylesheet" href="assets/connexion.css" />
</head>

<body>

<header class="site-header">
    <img src="img/icon.svg" alt="Logo du site" class="logo" />

    <nav aria-label="Navigation principale">
        <a href="?form=register" class="btn btn-secondary">Créer un compte</a>
        <a href="?form=login" class="btn btn-primary">Connexion</a>
    </nav>
</header>

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

<footer class="site-footer">
    <img src="img/title.svg" alt="Logo du site" />

    <nav aria-label="Liens du site">
        <section>
            <h2>Compte</h2>
            <ul>
                <li><a href="?form=register">Créer un compte</a></li>
                <li><a href="?form=login">Se connecter</a></li>
            </ul>
        </section>

        <section>
            <h2>Leaderboard</h2>
            <ul>
                <li><a href="#">Entreprise</a></li>
            </ul>
        </section>

        <section>
            <h2>Mentions</h2>
            <ul>
                <li><a href="#">Mentions légales</a></li>
                <li><a href="#">Justificatif RGAA</a></li>
            </ul>
        </section>

        <section>
            <h2>Plan du site</h2>
            <ul>
                <li><a href="#">Plan du site</a></li>
            </ul>
        </section>
    </nav>
</footer>

</body>
</html>
