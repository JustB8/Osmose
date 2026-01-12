<?php
declare(strict_types=1);

require_once __DIR__ . '/call_bdd.php';

session_start();
//$isLogged = isset($_SESSION['user']);

$userId = (int)($_SESSION['user_id'] ?? 1);
//if ($userId <= 0) {
//    http_response_code(401);
//    exit('Non connecté');
//}

$user = db_one(
    'SELECT u.id, u.company_id, u.name, u.email,
            COALESCE(u.level, 0) AS user_level,
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

$companyId = (int)($user['company_id'] ?? 0);

// ---- niveau d'entreprise terminées ----
$companyLevel = 0;
if ($companyId > 0) {
    $row = db_one(
        'SELECT COALESCE(ROUND(AVG(u.level))::int, 0) AS company_level
         FROM public.users u
         WHERE u.company_id = :cid',
        [':cid' => $companyId]
    );
    $companyLevel = (int)($row['company_level'] ?? 0);
}

// ---- Formations terminées (EXEMPLE) ----
// Suppose une table user_formation(user_id, formation_id, completed_at)
// + formation(id, name)
//$completedFormations = db_all(
//    'SELECT f.name
//     FROM public.user_formation uf
//     JOIN public.formation f ON f.id = uf.formation_id
//     WHERE uf.user_id = :uid
//       AND uf.completed_at IS NOT NULL
//     ORDER BY uf.completed_at DESC
//     LIMIT 20',
//    [':uid' => $userId]
//);

// fallback si rien
//if (!$completedFormations) {
//    $completedFormations = [
//        ['name' => 'Formation N°1 terminée'],
//        ['name' => 'Formation N°2 terminée'],
//        ['name' => 'Formation N°3 terminées'],
//        ['name' => '...'],
//    ];
//}

// ---- Rank entreprise ----
// Ici on calcule un "rang" en triant les entreprises par level décroissant.
$companyRank = null;
if ($companyId > 0) {
    $row = db_one(
        'WITH company_scores AS (
            SELECT c.id,
                   COALESCE(ROUND(AVG(u.level))::int, 0) AS score
            FROM public.company c
            LEFT JOIN public.users u ON u.company_id = c.id
            GROUP BY c.id
        ),
        ranked AS (
            SELECT id, score,
                   DENSE_RANK() OVER (ORDER BY score DESC, id ASC) AS rnk
            FROM company_scores
        )
        SELECT rnk
        FROM ranked
        WHERE id = :cid',
        [':cid' => $companyId]
    );

    $companyRank = $row ? (int)$row['rnk'] : null;
}


// -------------- Matrice (EXEMPLE) --------------
// À terme : remplace par une requête BDD (ex table company_skills / user_skills)
$skills = [
    'Écologique'    => 62,
    'Économique'    => 40,
    'Social'        => 90,
    'Gouvernance'   => 80,
    'Territorial'   => 25,
    'Culturel'      => 55,
];

function radar_points(array $values, float $cx, float $cy, float $r, float $startAngleDeg = -90): string {
    $values = array_values($values);        // IMPORTANT : garde juste les valeurs
    $n = count($values);
    $pts = [];

    for ($i = 0; $i < $n; $i++) {
        $v = max(0, min(100, (float)$values[$i]));
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $len = ($v / 100.0) * $r;

        $x = $cx + cos($angle) * $len;
        $y = $cy + sin($angle) * $len;

        $pts[] = round($x, 2) . "," . round($y, 2);
    }
    return implode(" ", $pts);
}

// Grille : polygone "plein rayon" (100%) pour n axes
function radar_grid_polygon(int $n, float $cx, float $cy, float $r, float $scale = 1.0, float $startAngleDeg = -90): string {
    $pts = [];
    for ($i = 0; $i < $n; $i++) {
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $x = $cx + cos($angle) * ($r * $scale);
        $y = $cy + sin($angle) * ($r * $scale);
        $pts[] = round($x, 2) . "," . round($y, 2);
    }
    return implode(" ", $pts);
}

// Axes : retourne un tableau des (x2,y2) pour dessiner les <line>
function radar_axes(int $n, float $cx, float $cy, float $r, float $startAngleDeg = -90): array {
    $axes = [];
    for ($i = 0; $i < $n; $i++) {
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $axes[] = [
            'x2' => round($cx + cos($angle) * $r, 2),
            'y2' => round($cy + sin($angle) * $r, 2),
        ];
    }
    return $axes;
}

$cx = 100; $cy = 100; $r = 85;
$n  = count($skills);

// Polygone valeurs
$points = radar_points($skills, $cx, $cy, $r);

// Grilles (3 niveaux)
$grid1 = radar_grid_polygon($n, $cx, $cy, $r, 1.00);
$grid2 = radar_grid_polygon($n, $cx, $cy, $r, 0.75);
$grid3 = radar_grid_polygon($n, $cx, $cy, $r, 0.50);

// Axes
$axes = radar_axes($n, $cx, $cy, $r);

function radar_labels(array $labels, float $cx, float $cy, float $rLabel, float $startAngleDeg = -90): array {
    $labels = array_values($labels);
    $n = count($labels);
    $out = [];
    for ($i=0; $i<$n; $i++) {
        $angle = deg2rad($startAngleDeg + (360/$n)*$i);
        $out[] = [
            'text' => (string)$labels[$i],
            'x' => round($cx + cos($angle) * $rLabel, 2),
            'y' => round($cy + sin($angle) * $rLabel, 2),
        ];
    }
    return $out;
}
$labelPoints = radar_labels(array_keys($skills), $cx, $cy, $r + 18);

// niveaux (affichage)
$userLevel    = (int)($user['user_level'] ?? 0);
$companyLevel = (int)($user['company_level'] ?? 0);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Profil</title>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="assets/css/profil_util.css">

    <script>
        window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
</head>

<body>
<main-header></main-header>

<main class="pu-page">
    <section class="pu-card" aria-label="profil utilisateur">

        <!-- TOP BAR : titre + niveau + bouton -->
        <header class="pu-topbar">
            <h2 class="pu-title">Matrice de competence</h2>

            <div class="pu-level">
                <span>Niveau :</span>
                <span class="pu-pill"><?= (int)$userLevel ?></span>
            </div>

            <a class="pu-btn" href="modifier_profil.php">Modifier le profil</a>
        </header>

        <!-- 2 colonnes : gauche (radar + infos entreprise) / droite (formations) -->
        <div class="pu-grid">

            <!-- COLONNE GAUCHE -->
            <div class="pu-left">

                <div class="pu-radarWrap">
                    <svg viewBox="0 0 200 200" class="radar" role="img" aria-label="Compétences">
                        <!-- Grille (auto, 6 côtés) -->
                        <polygon class="grid"  points="<?= htmlspecialchars($grid1) ?>"></polygon>
                        <polygon class="grid grid2" points="<?= htmlspecialchars($grid2) ?>"></polygon>
                        <polygon class="grid grid3" points="<?= htmlspecialchars($grid3) ?>"></polygon>

                        <!-- Axes (auto, 6 lignes) -->
                        <?php foreach ($labelPoints as $lp): ?>
                            <text x="<?= $lp['x'] ?>" y="<?= $lp['y'] ?>" class="radar-label" text-anchor="middle">
                                <?= htmlspecialchars($lp['text']) ?>
                            </text>
                        <?php endforeach; ?>

                        <!-- Polygone valeurs -->
                        <polygon class="fill"   points="<?= htmlspecialchars($points) ?>"></polygon>
                        <polygon class="stroke" points="<?= htmlspecialchars($points) ?>"></polygon>
                    </svg>
                </div>

                <div class="pu-companyInfo">
                    <div class="pu-companyBlock">
                        <div class="pu-label">Niveau entreprise</div>
                        <div class="pu-box"><?= (int)$companyLevel ?></div>
                    </div>

                    <div class="pu-companyBlock">
                        <div class="pu-label">Rank entreprise</div>
                        <div class="pu-box"><?= $companyRank ? ('#' . (int)$companyRank) : '—' ?></div>
                    </div>
                </div>

            </div>

            <!-- COLONNE DROITE -->
            <div class="pu-right">
                <div class="pu-panel">
                    <h3 class="pu-panelTitle">Formations terminées</h3>

                    <ul class="pu-list">
                        <?php foreach ($completedFormations as $f): ?>
                            <li><?= htmlspecialchars((string)$f['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>
</main>

<main-footer></main-footer>
</body>
</html>
