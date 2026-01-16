<?php
session_start();
require_once __DIR__ . '/call_bdd.php';

$isLogged = isset($_SESSION['user']);

$companyQuery = isset($_GET['company']) ? trim($_GET['company']) : '';
$companyError = null;
$foundCompany = null;
$compare = null;

// Normalisation PHP (min/trim/espaces). Les accents on les gère avec unaccent en SQL.
function normalize_company_input(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/\s+/', ' ', $s);
    $s = trim($s);
    return $s;
}

if ($companyQuery !== '') {
    $needle = normalize_company_input($companyQuery);

    // nécessite l'extension unaccent côté PostgreSQL:
    // code à ajouter sur la bdd si bug : CREATE EXTENSION IF NOT EXISTS unaccent;

    // On prend le premier match exact normalisé
    $foundCompany = db_one(
            "SELECT id, name
         FROM company
         WHERE unaccent(lower(trim(name))) = unaccent(:needle)
         LIMIT 1",
            ['needle' => $needle]
    );

    if (!$foundCompany) {
        $companyError = "Aucune entreprise trouvée. Vérifiez l’orthographe.";
    } else {
        $companyId = (int)$foundCompany['id'];

        // Fiche entreprise : membres + moyennes
        $stats = db_one(
                "SELECT
                c.id,
                c.name,
                COUNT(u.id) AS member_count,
                COALESCE(AVG(u.level), 0) AS avg_level,
                COALESCE(AVG(u.experience), 0) AS avg_experience
             FROM company c
             LEFT JOIN users u ON u.company_id = c.id
             WHERE c.id = :id
             GROUP BY c.id, c.name",
                ['id' => $companyId]
        );

        // Rang par score
        $rankRow = db_one(
                "WITH scores AS (
                SELECT
                    c.id,
                    COALESCE(AVG(u.experience), 0) AS score
                FROM company c
                LEFT JOIN users u ON u.company_id = c.id
                GROUP BY c.id
            )
            SELECT
                score,
                DENSE_RANK() OVER (ORDER BY score DESC) AS rank
            FROM scores
            WHERE id = :id",
                ['id' => $companyId]
        );

        $foundCompany = array_merge($foundCompany, $stats ?? [], $rankRow ?? []);

        // Si connecté : comparaison avec l'entreprise de l'user
        if ($isLogged && !empty($_SESSION['user']['company_id'])) {
            $myCompanyId = (int)$_SESSION['user']['company_id'];

            if ($myCompanyId !== $companyId) {
                $my = db_one(
                        "WITH scores AS (
                        SELECT
                            c.id,
                            c.name,
                            COUNT(u.id) AS member_count,
                            COALESCE(AVG(u.level), 0) AS avg_level,
                            COALESCE(AVG(u.experience), 0) AS score
                        FROM company c
                        LEFT JOIN users u ON u.company_id = c.id
                        GROUP BY c.id, c.name
                    ),
                    ranked AS (
                        SELECT
                            *,
                            DENSE_RANK() OVER (ORDER BY score DESC) AS rank
                        FROM scores
                    )
                    SELECT * FROM ranked WHERE id = :id",
                        ['id' => $myCompanyId]
                );

                if ($my) {
                    $compare = [
                            'my' => $my,
                            'delta_rank' => (int)$my['rank'] - (int)$foundCompany['rank'],
                            'delta_score' => (float)$my['score'] - (float)$foundCompany['score'],
                    ];
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Osmose - Accueil</title>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/index.css" />
    <script>
        window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/index.js" defer></script>
    <script src="./assets/js/composents.js"></script>
</head>

<body>
    <main-header></main-header>

    <main id="main-content" class="body">
        <!-- HERO -->
        <section class="home-section home-hero">
            <h1 class="text-title-hero">
                <span class="text-title-hero-2">Osmose</span>
            </h1>

            <p class="p home-subtitle">
                Le chemin vers le numérique responsable : progressez par niveaux, validez des actions concrètes,
                gagnez des points et améliorez le score de votre entreprise.
            </p>
            <?php if (!$isLogged): ?>
            <section class="home-new-user">
                <p class="p home-subtitle">
                    Nouvel utilisateur ? Créez donc un compte pour commencer votre formation et monter en compétence !
                </p>
                <div class="home-cta">
                    <a id="fist-element-body" class="home-btn home-btn-primary" href="/connexion.php?form=register">Créer un compte</a>
                </div>
            </section>
            <?php endif; ?>
        </section>

        <!-- COMMENT ÇA MARCHE -->
        <section class="home-section">
            <h2 class="text-wrapper-2">Comment ça marche ?</h2>

            <div class="home-cards">
                <article class="home-card">
                    <h3 class="home-card-title">Formations & activités</h3>
                    <p class="p">
                        Des thématiques (équipements, usages, accessibilité, achats…) avec des activités courtes et un examen final
                        pour valider la formation.
                    </p>
                </article>

                <article class="home-card">
                    <h3 class="home-card-title">Points & badges</h3>
                    <p class="p">
                        Chaque action validée rapporte des points et débloque des badges.
                        Exemple : “AccessiPro” quand vous avancez sur l’accessibilité.
                    </p>
                </article>

                <article class="home-card">
                    <h3 class="home-card-title">Score entreprise</h3>
                    <p class="p">
                        Chaque utilisateur est associé à une entreprise.
                        Le score entreprise correspond à la moyenne des scores des membres.
                    </p>
                </article>
            </div>
        </section>

        <!-- ACTIONS RÉELLES -->
        <section class="home-section action-applicables">
            <h2 class="text-wrapper-2">Des actions applicables au quotidien</h2>
            <p class="p">
                Un dashboard centralise toutes les actions possibles : celles vues en formation et celles non encore validées.
                Vous pouvez valider une action à tout moment, sans revenir en arrière.
            </p>

            <div class="home-bullets">
                <div class="home-bullet">✔ Actions + points</div>
                <div class="home-bullet">✔ Matrice de compétences</div>
                <div class="home-bullet">✔ Questions quotidiennes + badges de régularité</div>
            </div>
        </section>

        <!-- CLASSEMENT -->
        <section class="home-section home-leaderboard">
            <h2 class="text-wrapper-2">Consultez le classement des entreprises</h2>
            <p class="p">
                Comparez la progression et suivez votre rang.
                Idéal pour motiver une démarche collective et mesurer les progrès.
            </p>
            <div class="home-cta">
                <a class="home-btn home-btn-primary" href="/leaderboard.php">Consultez le classement</a>
            </div>
            <div class="home-search">
                <label class="home-label" for="companySearch">Trouver une entreprise</label>
                <form class="home-search-row" method="get" action="/index.php">
                    <input id="companySearch" name="company" class="home-input" type="text" placeholder="Nom de l’entreprise…" />
                    <button class="home-btn home-btn-secondary" type="submit">Rechercher</button>
                </form>
                <?php if ($companyQuery !== ''): ?>
                    <?php if ($companyError): ?>
                        <p class="p" style="margin-top:8px;"><strong><?= htmlspecialchars($companyError) ?></strong></p>
                    <?php elseif ($foundCompany): ?>
                        <section class="home-section home-card" style="margin-top:12px;">
                            <h3 class="home-card-title"><?= htmlspecialchars($foundCompany['name']) ?></h3>
                            <p class="p">
                                <strong>Rang :</strong> #<?= (int)$foundCompany['rank'] ?>
                                • <strong>Membres :</strong> <?= (int)$foundCompany['member_count'] ?>
                                • <strong>Score :</strong> <?= number_format((float)$foundCompany['score'], 1, ',', ' ') ?>
                            </p>
                            <p class="p">
                                <strong>Niveau moyen :</strong> <?= number_format((float)$foundCompany['avg_level'], 1, ',', ' ') ?>
                                • <strong>Expérience moyenne :</strong> <?= number_format((float)$foundCompany['avg_experience'], 1, ',', ' ') ?>
                            </p>

                            <?php if ($compare && isset($compare['my'])): ?>
                                <hr style="opacity:.2; margin:12px 0;">
                                <p class="p"><strong>Comparaison avec votre entreprise :</strong></p>
                                <p class="p">
                                    Votre entreprise (<strong><?= htmlspecialchars($compare['my']['name']) ?></strong>) :
                                    Rang #<?= (int)$compare['my']['rank'] ?> • Score <?= number_format((float)$compare['my']['score'], 1, ',', ' ') ?>
                                </p>
                                <p class="p">
                                    Écart :
                                    <?= $compare['delta_rank'] < 0
                                            ? "vous êtes <strong>devant</strong> de " . abs((int)$compare['delta_rank']) . " place(s)"
                                            : ($compare['delta_rank'] > 0
                                                    ? "vous êtes <strong>derrière</strong> de " . (int)$compare['delta_rank'] . " place(s)"
                                                    : "même rang") ?>
                                    • <?= $compare['delta_score'] >= 0
                                            ? "score +".number_format((float)$compare['delta_score'], 1, ',', ' ')
                                            : "score ".number_format((float)$compare['delta_score'], 1, ',', ' ') ?>
                                </p>
                            <?php elseif ($isLogged && empty($_SESSION['user']['company_id'])): ?>
                                <p class="p" style="margin-top:8px;">
                                    Connecté, mais aucune entreprise liée à votre profil : ajoutez-en une pour activer la comparaison.
                                </p>
                            <?php elseif (!$isLogged): ?>
                                <p class="p" style="margin-top:8px;">
                                    Connectez-vous pour comparer avec votre entreprise.
                                </p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- FAQ -->
        <section class="home-section faq">
            <h2 class="text-wrapper-2">FAQ</h2>

            <details class="home-faq">
                <summary>Dois-je valider toutes les activités ?</summary>
                <p class="p">
                    Certaines activités peuvent être optionnelles, mais pour valider une formation,
                    il faut valider l’ensemble des activités et réussir l’examen final.
                </p>
            </details>

            <details class="home-faq">
                <summary>À quoi servent les questions quotidiennes ?</summary>
                <p class="p">
                    Elles renforcent la mémorisation. Elles ne donnent pas de points, mais débloquent des badges de régularité
                    (1 semaine, 1 mois, 1 an…).
                </p>
            </details>

            <details class="home-faq">
                <summary>Que voit une entreprise ?</summary>
                <p class="p">
                    Le leaderboard, le rang, et une progression globale basée sur les membres rattachés.
                    (Vous pouvez ajouter plus tard un profil admin pour valider les nouveaux entrants.)
                </p>
            </details>
        </section>
    </main>

    <main-footer></main-footer>
</body>
</html>