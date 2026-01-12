<?php
session_start();
require_once 'call_bdd.php';

// --- LOGIQUE DE CONNEXION (Simulation pour le test) ---
$isLogged = true; 
$userId = 1; 

$_SESSION['report'] = false;
$points_user = ['level' => 0];
$points_entreprise = ['level' => 0];

$question = null; // Contiendra les infos pour le JS

// --- LOGIQUE DE REPORT (À ajouter au début du PHP) ---
if (isset($_POST['action']) && $_POST['action'] === 'report_question') {
  $_SESSION['report'] = true;
  $_SESSION['report_date'] = date('Y-m-d');
  header("Location: Dashboard.php");
  exit;
}

// --- TRAITEMENT DE L'ENVOI DE RÉPONSE (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_reponse'])) {
    $qId = $_POST['question_id'];
    // Utilisation des colonnes réelles de votre BDD (user_id, daily_question_id, day)
    db_exec("INSERT INTO user_daily_answer (user_id, daily_question_id, day, answered_at) 
             VALUES (?, ?, CURRENT_DATE, NOW());", [$userId, $qId]);
    header("Location: Dashboard.php");
    exit;
}

// --- LOGIQUE D'AFFICHAGE DE LA QUESTION ---
if ($isLogged && $userId ) {
    try {
        $points_entreprise = db_one("SELECT SUM(level) AS level FROM users WHERE company_id = (SELECT company_id FROM users WHERE id = ?)", [$userId]);
        $points_user = db_one("SELECT level AS level FROM users WHERE id = ?;", [$userId]);    
        
        $last_activity = db_one("SELECT name AS nom 
                                     FROM activity 
                                     INNER JOIN avance_activity ON activity.id = avance_activity.activity_id
                                     WHERE user_id = ? AND is_completed = true
                                     ORDER BY completed_at DESC;", [$userId]);

        $last_formation = db_one("SELECT name AS nom 
                                    FROM formation 
                                    INNER JOIN user_formation ON formation.id = user_formation.formation_id
                                    WHERE user_id = ? AND status = 'completed'
                                    ORDER BY completed_at DESC;", [$userId]);

        if (empty($last_formation)){
          $last_formation = ['nom' => "Vous n'avez pas de formation complétés"];
        }

        if (empty($last_activity)){
          $last_activity = ['nom' => "Vous n'avez pas d'activité complétés"];
        }

        if($_SESSION['report_date'] != date('Y-m-d') && $_SESSION['report'] = true){
          $_SESSION['report'] = false;
        }

        if ($_SESSION['report'] == false){
          // 1. Vérifier les formations terminées (colonne user_id et statut completed)
          $formations = db_all("SELECT formation_id FROM user_formation WHERE user_id = ? AND status = 'completed';", [$userId]);
          
          if (!empty($formations)) {
              // 2. Vérifier si l'utilisateur a déjà répondu aujourd'hui
              $lastReponse = db_one("SELECT day FROM user_daily_answer WHERE user_id = ? AND day = CURRENT_DATE;", [$userId]);

              if (!$lastReponse) {
                  // 3. Récupérer une question active liée aux formations terminées
                  $formationIds = array_column($formations, 'formation_id');
                  $placeholders = implode(',', array_fill(0, count($formationIds), '?'));
                  
                  $question = db_one("SELECT id, question, answer 
                                      FROM daily_question 
                                      WHERE formation_id IN ($placeholders) 
                                      AND is_active = true 
                                      ORDER BY RANDOM() LIMIT 1;", $formationIds);
                  
                  if ($question) {
                    $question['is_reported'] = (isset($_SESSION['report']) && $_SESSION['report'] === true);
                  }
              }
          }
        }
        
    } catch (Exception $e) {
        error_log("Erreur Dashboard : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Dashboard - Points et Activités</title>
    <link rel="stylesheet" href="./assets/css/Dashboard.css" />
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css" />
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css" />
    
    <script>
      // Transmission des données au fichier Dashboard.js
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
      window.QUESTION_DU_JOUR = <?= json_encode($question) ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
    <script src="./assets/js/Dashboard.js" defer></script>
  </head>
  <body>
      <main-header></main-header>
      
      <main>
        <div id="points">
          <section id="points-entreprise">
            <h2>Nombre de Points de l'entreprise</h2>
            <div class="cadre">
              <p aria-live="polite">Level <?= $points_entreprise['level'] ?> </p>
            </div> 
          </section>

          <section id="points-utilisateur">
            <h2>Nombre de Points de l'utilisateur</h2>
            <div class="cadre">
               <p aria-live="polite">Level <?= $points_user['level'] ?></p>
            </div>
          </section>
        </div>

        <article id="activite">
            <h3>Dernière activité</h3>
            <p><?= $last_activity['nom'] ?></p>
        </article>
        
        <article id="formation">
            <h3>Dernière formation</h3>
            <p><?= $last_formation['nom'] ?></p>
        </article>

        <div id="popup-question" class="modal">
          <div class="modal-content">
              <h2 id="popup-title">Question du jour</h2>
              <p id="popup-libelle"></p>
              
              <div id="feedback-message" style="margin: 10px 0; font-weight: bold;"></div>

              <form id="form-question" method="POST">
                  <input type="hidden" name="question_id" id="popup-question-id">
                  <div id="reponses-container">
                      <input type="text" id="user-answer" placeholder="Votre réponse ici..." required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                  </div>
                  <div class="modal-footer">
                      <button type="button" id="btn-valider-js" class="btn-valider">Valider ma réponse</button>
                      <button type="button" id="btn-close-popup">Plus tard</button>
                  </div>
                  <div class="modal-footer-charg">
                    <p> Chargement... </p>
                  </div>
              </form>
          </div>
        </div>
        <div id="reminder-modal" class="reminder-badge" style="display: none;">
            <p>💡 Question en attente</p>
        </div>
      </main>

      <main-footer></main-footer>
  </body>
</html>