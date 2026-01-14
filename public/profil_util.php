<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/call_bdd.php';

$isLogged = isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id']);

if (!$isLogged) {
    // Pas d'output avant header()
    header('Location: /connexion.php'); // ou /index.php si tu préfères
    exit;
}

$userId = (int)$_SESSION['user']['id'];

/* =========================
   User + company
   ========================= */
$user = db_one(
        'SELECT u.id, u.company_id, u.name, u.email,
            COALESCE(u.level, 0) AS user_level,
            c.name AS company_name
     FROM public.users u
     LEFT JOIN public.company c ON c.id = u.company_id
     WHERE u.id = :id',
        ['id' => $userId]
);

if (!$user) {
    // Toujours avant HTML
    http_response_code(404);
    exit('Utilisateur introuvable');
}

$companyId = (int)($user['company_id'] ?? 0);

/* =========================
   Niveau entreprise = AVG(level) des users
   ========================= */
$companyLevel = 0;
if ($companyId > 0) {
    $row = db_one(
            'SELECT COALESCE(ROUND(AVG(u.level))::int, 0) AS company_level
         FROM public.users u
         WHERE u.company_id = :cid',
            ['cid' => $companyId]
    );
    $companyLevel = (int)($row['company_level'] ?? 0);
}

/* =========================
   Rank entreprise (comparaison entre entreprises)
   ========================= */
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
            ['cid' => $companyId]
    );

    $companyRank = $row ? (int)$row['rnk'] : null;
}

/* =========================
   Stats thématiques -> skills (0..100)
   ========================= */
$thematicStats = db_all(
        "SELECT
         t.id   AS thematic_id,
         t.name AS thematic_name,

         COALESCE(tp.total_points, 0) AS total_points,
         COALESCE(up.user_points, 0)  AS user_points,

         CASE
           WHEN COALESCE(tp.total_points, 0) = 0 THEN 0
           ELSE ROUND((COALESCE(up.user_points, 0)::numeric / tp.total_points::numeric) * 100)
         END AS percent
     FROM public.thematic t
     LEFT JOIN (
         SELECT thematic_id, SUM(points)::int AS total_points
         FROM public.formation
         GROUP BY thematic_id
     ) tp ON tp.thematic_id = t.id
     LEFT JOIN (
         SELECT f.thematic_id, SUM(f.points)::int AS user_points
         FROM public.user_formation uf
         JOIN public.formation f ON f.id = uf.formation_id
         WHERE uf.user_id = :uid
           AND uf.status = 'completed'
         GROUP BY f.thematic_id
     ) up ON up.thematic_id = t.id
     ORDER BY CASE t.name
        WHEN 'Écologique'  THEN 1
        WHEN 'Économique'  THEN 2
        WHEN 'Social'      THEN 3
        WHEN 'Gouvernance' THEN 4
        WHEN 'Territorial' THEN 5
        WHEN 'Culturel'    THEN 6
        ELSE 999
     END, t.name ASC",
        ['uid' => $userId]
);

$skills = [];
$skillMeta = [];

foreach ($thematicStats as $row) {
    $label = (string)$row['thematic_name'];

    $percent = (int)$row['percent'];
    $percent = max(0, min(100, $percent));

    $skills[$label] = $percent;

    $skillMeta[$label] = [
            'user_points'  => (int)$row['user_points'],
            'total_points' => (int)$row['total_points'],
            'percent'      => $percent,
    ];
}

/* =========================
   Formations terminées
   ========================= */
$completedFormations = db_all(
        "SELECT f.name
     FROM public.user_formation uf
     JOIN public.formation f ON f.id = uf.formation_id
     WHERE uf.user_id = :uid
       AND uf.status = 'completed'
     ORDER BY uf.completed_at DESC
     LIMIT 20",
        ['uid' => $userId]
);

if (!$completedFormations) {
    $completedFormations = [['name' => 'Pas de formation, commencez votre apprentissage maintenant !']];
}

/* =========================
   Radar SVG helpers
   ========================= */
function radar_points(array $values, float $cx, float $cy, float $r, float $startAngleDeg = -90): string {
    $values = array_values($values);
    $n = count($values);
    if ($n === 0) return '';

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

function radar_grid_polygon(int $n, float $cx, float $cy, float $r, float $scale = 1.0, float $startAngleDeg = -90): string {
    if ($n === 0) return '';
    $pts = [];
    for ($i = 0; $i < $n; $i++) {
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $x = $cx + cos($angle) * ($r * $scale);
        $y = $cy + sin($angle) * ($r * $scale);
        $pts[] = round($x, 2) . "," . round($y, 2);
    }
    return implode(" ", $pts);
}

function radar_axes(int $n, float $cx, float $cy, float $r, float $startAngleDeg = -90): array {
    $axes = [];
    if ($n === 0) return $axes;

    for ($i = 0; $i < $n; $i++) {
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $axes[] = [
                'x2' => round($cx + cos($angle) * $r, 2),
                'y2' => round($cy + sin($angle) * $r, 2),
        ];
    }
    return $axes;
}

function radar_labels(array $labels, float $cx, float $cy, float $rLabel, float $startAngleDeg = -90): array {
    $labels = array_values($labels);
    $n = count($labels);
    $out = [];
    if ($n === 0) return $out;

    for ($i = 0; $i < $n; $i++) {
        $angle = deg2rad($startAngleDeg + (360 / $n) * $i);
        $out[] = [
                'text' => (string)$labels[$i],
                'x' => round($cx + cos($angle) * $rLabel, 2),
                'y' => round($cy + sin($angle) * $rLabel, 2),
        ];
    }
    return $out;
}

/* =========================
   Radar compute
   ========================= */
$cx = 100; $cy = 100; $r = 85;
$n  = count($skills);

$points = radar_points($skills, $cx, $cy, $r);
$grid1  = radar_grid_polygon($n, $cx, $cy, $r, 1.00);
$grid2  = radar_grid_polygon($n, $cx, $cy, $r, 0.75);
$grid3  = radar_grid_polygon($n, $cx, $cy, $r, 0.50);
$axes   = radar_axes($n, $cx, $cy, $r);
$labelPoints = radar_labels(array_keys($skills), $cx, $cy, $r + 18);

/* =========================
   Niveaux affichage
   ========================= */
$userLevel = (int)($user['user_level'] ?? 0);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Profil</title>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/index.css" />
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

        <header class="pu-topbar">
            <h2 class="pu-title">Matrice de compétence</h2>

            <div class="pu-level">
                <span>Niveau :</span>
                <span class="pu-pill"><?= (int)$userLevel ?></span>
            </div>

            <a class="pu-btn" href="param_profil_util.php">Modifier le profil</a>
        </header>

        <div class="pu-grid">
            <div class="pu-left">
                <div class="pu-radarWrap">
                    <svg viewBox="-13 -13 230 230" class="radar" role="img" aria-label="Compétences">
                        <polygon class="grid"  points="<?= htmlspecialchars($grid1) ?>"></polygon>
                        <polygon class="grid grid2" points="<?= htmlspecialchars($grid2) ?>"></polygon>
                        <polygon class="grid grid3" points="<?= htmlspecialchars($grid3) ?>"></polygon>

                        <?php foreach ($axes as $a): ?>
                            <line class="axis" x1="<?= $cx ?>" y1="<?= $cy ?>" x2="<?= $a['x2'] ?>" y2="<?= $a['y2'] ?>"></line>
                        <?php endforeach; ?>

                        <?php foreach ($labelPoints as $lp): ?>
                            <text x="<?= $lp['x'] ?>" y="<?= $lp['y'] ?>" class="radar-label" text-anchor="middle">
                                <?= htmlspecialchars($lp['text']) ?>
                            </text>
                        <?php endforeach; ?>

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
