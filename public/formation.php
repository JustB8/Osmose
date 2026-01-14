<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/call_bdd.php';

// Récupérer l'ID de formation depuis l'URL ou utiliser une valeur par défaut pour les tests
$formationId = isset($_GET['id']) ? (int)$_GET['id'] : 15; // Formation ID 15 par défaut pour les tests
$userId = (int)$_SESSION['user']['id'];

// 2. Initialiser la formation pour l'utilisateur (si pas encore commencée)
$userFormation = db_one(
    "SELECT * FROM user_formation WHERE user_id = :uid AND formation_id = :fid",
    ['uid' => $userId, 'fid' => $formationId]
);

if (!$userFormation) {
    db_exec(
        "INSERT INTO user_formation (user_id, formation_id, status) VALUES (:uid, :fid, 'in_progress')",
        ['uid' => $userId, 'fid' => $formationId]
    );
}

// 3. Récupérer les infos de la formation
$formation = db_one("SELECT * FROM formation WHERE id = :id", ['id' => $formationId]);
if (!$formation) {
    die("Formation introuvable. <a href='?id=15'>Essayer avec la formation ID 15</a>");
}

// 4. Traitement du Formulaire de Réponse (Activité)
$errorMsg = null;
$successMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity_id'], $_POST['answer'])) {
    $activityId = (int)$_POST['activity_id'];

    // Récupérer la bonne réponse
    $activityData = db_one("SELECT body, answer FROM activity WHERE id = :id", ['id' => $activityId]);

    // Comparaison insensible à la casse
    if ($activityData && strcasecmp($_POST['answer'], $activityData['answer']) === 0) {
        // Succès : On marque l'activité comme complétée
        try {
            db_exec(
                "INSERT INTO avance_activity (user_id, activity_id, is_completed, completed_at) 
                 VALUES (:uid, :aid, true, NOW())
                 ON CONFLICT (user_id, activity_id) DO NOTHING",
                ['uid' => $userId, 'aid' => $activityId]
            );
            $successMsg = "Bonne réponse ! Activité suivante débloquée.";
        } catch (Exception $e) {
            // Ignorer si déjà inséré
        }
    } else {
        $errorMsg = "Mauvaise réponse, essayez encore (Indice : relisez le texte).";
    }
}

// 5. Traitement du Formulaire des Actions (Fin de formation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_actions'])) {
    if (!empty($_POST['actions']) && is_array($_POST['actions'])) {
        foreach ($_POST['actions'] as $actionId) {
            // Insérer l'action si pas déjà fait
            $exists = db_one("SELECT 1 FROM user_action WHERE user_id = :uid AND action_id = :aid", 
                ['uid' => $userId, 'aid' => $actionId]);
            
            if (!$exists) {
                db_exec(
                    "INSERT INTO user_action (user_id, action_id) VALUES (:uid, :aid)",
                    ['uid' => $userId, 'aid' => $actionId]
                );
            }
        }
        $successMsg = "Vos actions ont été ajoutées au tableau de bord !";
    }
}

// 6. Récupérer les activités et le statut
$activities = db_all(
    "SELECT 
    a.id, 
    a.name, 
    a.body, 
    a.answer, 
    a.sort_order, 
    a.formation_id,
    ad.description AS description,
    CASE WHEN aa.is_completed THEN 1 ELSE 0 END AS is_completed
FROM activity a
INNER JOIN activity_description ad ON a.id = ad.activity_id
LEFT JOIN avance_activity aa ON a.id = aa.activity_id AND aa.user_id = :uid
WHERE a.formation_id = :fid
ORDER BY a.sort_order ASC;",
    ['uid' => $userId, 'fid' => $formationId]
);

// Déterminer quelle est l'activité courante (la première non complétée)
$currentActivityIndex = -1;
$allCompleted = true;

foreach ($activities as $index => $act) {
    if ($act['is_completed'] == 0) {
        $currentActivityIndex = $index;
        $allCompleted = false;
        break;
    }
}

// Si tout est fini, mettre à jour le statut global de la formation
if ($allCompleted && count($activities) > 0) {
    db_exec(
        "UPDATE user_formation SET status = 'completed', completed_at = NOW() 
         WHERE user_id = :uid AND formation_id = :fid AND status != 'completed'",
        ['uid' => $userId, 'fid' => $formationId]
    );
    
    // Récupérer les actions possibles pour cette formation
    $actions = db_all("SELECT * FROM action WHERE formation_id = :fid", ['fid' => $formationId]);
} else {
    $actions = [];
}

// Liste des formations disponibles pour navigation facile en mode test
$availableFormations = db_all("SELECT id, name FROM formation ORDER BY id ASC");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($formation['name']) ?> - Osmose</title>
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/formation_details.css" />
    <script>
      window.IS_LOGGED = true;
    </script>
    <script src="assets/js/composents.js"></script>
</head>
<body>
<main-header></main-header>

<main class="body formation-page">

    <div class="formation-header">
        <a href="?id=<?= $formationId ?>" class="back-link">🔄 Recharger la page</a>
        <h1><?= htmlspecialchars($formation['name']) ?></h1>
        <div class="progress-bar">
            <?php 
                $total = count($activities);
                $done = 0;
                foreach($activities as $a) if($a['is_completed']) $done++;
                $percent = $total > 0 ? ($done / $total) * 100 : 0;
            ?>
            <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
        </div>
        <p><?= $done ?> / <?= $total ?> activités validées</p>
    </div>

    <?php if ($errorMsg): ?>
        <div class="alert error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
        <div class="alert success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <?php if (empty($activities)): ?>
        <div class="alert error">
            Aucune activité trouvée pour cette formation. 
            Veuillez vérifier que des activités existent dans la base de données pour formation_id = <?= $formationId ?>.
        </div>
    <?php else: ?>
        <section class="activities-list">
            <?php foreach ($activities as $index => $activity): ?>
                <?php 
                    // Logique d'état visuel
                    $statusClass = 'locked';
                    if ($activity['is_completed']) {
                        $statusClass = 'completed';
                    } elseif ($index === $currentActivityIndex) {
                        $statusClass = 'current';
                    }
                ?>
                
                <article class="activity-card <?= $statusClass ?>">
                    <div class="activity-status-icon">
                        <?php if ($statusClass === 'completed'): ?>
                            ✓
                        <?php elseif ($statusClass === 'current'): ?>
                            ➤
                        <?php else: ?>
                            🔒
                        <?php endif; ?>
                    </div>

                    <div class="activity-content">
                        <h2><?= htmlspecialchars($activity['name']) ?></h2>
                        
                        <?php if ($statusClass !== 'locked'): ?>
                            <div class="activity-body">
                                <?= nl2br(htmlspecialchars($activity['description'])) ?>
                            </div>

                            <?php if ($statusClass === 'current'): ?>
                                <div class="activity-quiz">
                                    <hr>
                                    <p>Pour passer à la suite, répondez à cette question :</p>
                                    <p><strong>Question :</strong> <?= $activity['body'] ?></p>
                                    <form method="POST" class="quiz-form">
                                        <input type="hidden" name="activity_id" value="<?= $activity['id'] ?>">
                                        <label>Quelle est la réponse ?</label>
                                        <input type="text" name="answer" placeholder="Votre réponse..." required autocomplete="off">
                                        <button type="submit" class="btn-primary">Valider</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <p class="locked-text">Terminez l'activité précédente pour débloquer ce contenu.</p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ($allCompleted && count($activities) > 0): ?>
        <section class="formation-actions">
            <h2>🎉 Formation terminée !</h2>
            <p>Bravo, vous avez validé toutes les connaissances. Passez maintenant à la pratique en choisissant des actions à ajouter à votre Dashboard.</p>
            
            <form method="POST" class="actions-form">
                <input type="hidden" name="submit_actions" value="1">
                <div class="actions-grid">
                    <?php if (empty($actions)): ?>
                        <p>Aucune action spécifique liée à cette formation.</p>
                    <?php else: ?>
                        <?php foreach ($actions as $act): ?>
                            <?php 
                                // Vérifier si déjà coché
                                $alreadyAdded = db_one("SELECT 1 FROM user_action WHERE user_id = :uid AND action_id = :aid", 
                                    ['uid' => $userId, 'aid' => $act['id']]);
                            ?>
                            <div class="action-checkbox">
                                <input type="checkbox" id="act_<?= $act['id'] ?>" name="actions[]" value="<?= $act['id'] ?>" <?= $alreadyAdded ? 'checked disabled' : '' ?>>
                                <label for="act_<?= $act['id'] ?>">
                                    <strong><?= htmlspecialchars($act['name']) ?></strong>
                                    <span class="badge-points">+<?= $act['points'] ?> pts</span>
                                    <br>
                                    <small><?= htmlspecialchars((string)$act['description']) ?></small>
                                    <?php if($alreadyAdded): ?> <span style="color:green">(Déjà ajouté)</span> <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($actions)): ?>
                    <button type="submit" class="btn-primary large">Ajouter ces actions à mon Dashboard</button>
                <?php endif; ?>
            </form>
            
            <br>
            <div style="text-align: center;">
                <a href="dashboard.php" class="btn-secondary">Aller au Dashboard</a>
            </div>
        </section>
    <?php endif; ?>

</main>
<main-footer></main-footer>
</body>
</html>