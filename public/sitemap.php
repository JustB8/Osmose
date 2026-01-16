<?php
session_start();
$isLogged = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osmose - Plan du Site</title>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <style>
        .sitemap-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 60vh;
        }
        .sitemap-list {
            list-style: none;
            padding: 0;
        }
        .sitemap-item {
            margin-bottom: 20px;
        }
        .sitemap-item h2 {
            color: var(--primary-color, #2d3436);
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .sitemap-sublist {
            padding-left: 20px;
        }
        .sitemap-sublist li {
            margin: 10px 0;
        }
        .sitemap-sublist a {
            text-decoration: none;
            color: #555;
            transition: color 0.3s;
        }
        .sitemap-sublist a:hover {
            color: #000;
            text-decoration: underline;
        }
        .badge-auth {
            font-size: 0.8em;
            background: #eee;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            color: #666;
        }
    </style>
    <script src="assets/js/composents.js"></script>
</head>
<body>
    <main-header></main-header>

    <main id="main-content" class="sitemap-container">
        <h1>Plan du site</h1>
        <p class="p">Retrouvez ici l'ensemble des pages disponibles sur la plateforme Osmose.</p>

        <ul class="sitemap-list">
            <li class="sitemap-item">
                <h2>Navigation Principale</h2>
                <ul class="sitemap-sublist">
                    <li><a href="/index.php">Accueil</a></li>
                    <li><a href="/leaderboard.php">Classement des entreprises</a></li>
                    <?php if (!$isLogged): ?>
                        <li><a href="/connexion.php">Connexion / Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <li class="sitemap-item">
                <h2>Espace Personnel</h2>
                <ul class="sitemap-sublist">
                    <?php if ($isLogged): ?>
                        <li><a href="/dashboard.php">Tableau de bord</a> <span class="badge-auth">Connecté</span></li>
                        <li><a href="/formation_search.php">Mes Formations</a> <span class="badge-auth">Connecté</span></li>
                        <li><a href="/profil_util.php">Mon Profil</a> <span class="badge-auth">Connecté</span></li>
                        <li><a href="/param_profil_util.php">Paramètres du compte</a> <span class="badge-auth">Connecté</span></li>
                        <li><a href="/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><em>Connectez-vous pour accéder à votre tableau de bord et vos formations.</em></li>
                    <?php endif; ?>
                </ul>
            </li>

            <li class="sitemap-item">
                <h2>Informations Légales</h2>
                <ul class="sitemap-sublist">
                    <li><a href="/legal_notice.php">Mentions Légales</a></li>
                    <li><a href="/rgaa.php">Justificatif RGAA</a></li>
                </ul>
            </li>
        </ul>
    </main>

    <main-footer></main-footer>
</body>
</html>