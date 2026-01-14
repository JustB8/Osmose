<?php
session_start();
require_once __DIR__ . '/call_bdd.php';

$isLogged = isset($_SESSION['user']);
$myCompanyId = ($isLogged && !empty($_SESSION['user']['company_id'])) ? (int)$_SESSION['user']['company_id'] : null;

/**
 * Leaderboard : score = AVG(users.experience) par entreprise
 * On calcule aussi member_count, avg_level, score, rank
 */
$rows = db_all(
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
    SELECT * FROM ranked
    ORDER BY rank ASC, name ASC"
);

$top3 = array_slice($rows, 0, 3);
$rest = array_slice($rows, 3, 7); // 4 à 10 (7 lignes)

$myCompany = null;
if ($myCompanyId !== null) {
    foreach ($rows as $r) {
        if ((int)$r['id'] === $myCompanyId) {
            $myCompany = $r;
            break;
        }
    }
}

// Helpers d’affichage
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function fmt_score($v): string { return number_format((float)$v, 1, ',', ' '); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Classement des entreprises - LeaderBoard</title>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css" />
    <link rel="stylesheet" href="./assets/css/Leaderboard.css" />
    <script>
        window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
</head>

<body>
<main-header></main-header>

<main class="body">
    <div class="bouton-recherche">
        <h1 class="text-wrapper-8" id="leaderboard-title">Classement des entreprises</h1>
    </div>

    <?php if ($myCompany): ?>
        <section class="my-company-card" aria-label="Votre entreprise">
            <div>
                <strong>Votre entreprise :</strong> <?= h($myCompany['name']) ?>
                - Rang <strong>#<?= (int)$myCompany['rank'] ?></strong>
                - Score <strong><?= fmt_score($myCompany['score']) ?></strong>
                - Membres <strong><?= (int)$myCompany['member_count'] ?></strong>
            </div>
        </section>
    <?php elseif ($isLogged): ?>
        <section class="my-company-card" aria-label="Votre entreprise">
            <div>
                <strong>Astuce :</strong> vous n’êtes pas encore rattaché à une entreprise (profil) → pas de rang affiché.
            </div>
        </section>
    <?php endif; ?>

    <!-- PODIUM -->
    <div class="podium" role="img" aria-label="Podium des trois premières places">
        <?php
        // On veut afficher : 3ème, 1er, 2ème dans cet ordre visuel
        $first  = $top3[0] ?? null;
        $second = $top3[1] ?? null;
        $third  = $top3[2] ?? null;
        ?>

        <div class="place" aria-label="Deuxième place">
            <?php if ($second): ?>
                <div class="podium-label">#2</div>
                <div class="podium-name"><?= h($second['name']) ?></div>
                <div class="podium-score"><?= fmt_score($second['score']) ?></div>
            <?php else: ?>
                <div class="podium-name">—</div>
            <?php endif; ?>
        </div>

        <div class="place-2" aria-label="Première place">
            <?php if ($first): ?>
                <div class="podium-label">#1</div>
                <div class="podium-name"><?= h($first['name']) ?></div>
                <div class="podium-score"><?= fmt_score($first['score']) ?></div>
            <?php else: ?>
                <div class="podium-name">—</div>
            <?php endif; ?>
        </div>

        <div class="place-3" aria-label="Troisième place">
            <?php if ($third): ?>
                <div class="podium-label">#3</div>
                <div class="podium-name"><?= h($third['name']) ?></div>
                <div class="podium-score"><?= fmt_score($third['score']) ?></div>
            <?php else: ?>
                <div class="podium-name">—</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- LISTE 4 à 10 -->
    <section class="component" aria-labelledby="leaderboard-title">
        <div class="top">
            <?php if (count($rows) === 0): ?>
                <p>Aucune entreprise à afficher.</p>
            <?php else: ?>
                <?php foreach ($rest as $r): ?>
                    <?php
                    $isMine = ($myCompanyId !== null && (int)$r['id'] === $myCompanyId);
                    ?>
                    <div class="leaderboard-row <?= $isMine ? 'is-mine' : '' ?>">
                        <div class="leaderboard-rank">
                            <span>Rang : <?= (int)$r['rank'] ?></span>
                        </div>

                        <div class="leaderboard-content">
                            <div class="lb-main">
                                <span class="lb-name"><?= h($r['name']) ?></span>
                                <?php if ($isMine): ?>
                                    <span class="lb-badge">Votre entreprise</span>
                                <?php endif; ?>
                            </div>
                            <div class="lb-meta">
                                <span>Score : <strong><?= fmt_score($r['score']) ?></strong></span>
                                <span>• Membres : <strong><?= (int)$r['member_count'] ?></strong></span>
                                <span>• Niveau moyen : <strong><?= number_format((float)$r['avg_level'], 1, ',', ' ') ?></strong></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<main-footer></main-footer>
</body>
</html>
