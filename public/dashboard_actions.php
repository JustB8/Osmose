<?php
  session_start();
  require_once 'call_bdd.php';

  $userId = $_SESSION['user']['id'] ?? null;
  $isLogged = isset($_SESSION['user']);

  $allActions = [];
  $userActions = [];
  $error_msg = null;
  $nb_actions = 0;
  //On va chercher toutes les actions de la base
  try {
      $actionsFromDb = db_all("SELECT id, points as pts, name as label FROM action");
      $nb_actions = count($actionsFromDb);

      if ($nb_actions > 0) {

          foreach ($actionsFromDb as $row) {
              $allActions[$row['id']] = $row;
          }

          if ($userId) {
              $userRows = db_all("SELECT action_id FROM user_action WHERE user_id = ?", [$userId]);
              $userActions = array_column($userRows, 'action_id');
          }
      }
  } catch (Exception $e) {
      $error_msg = $e->getMessage();
  }

  // On relance un requete SQL pour obtenir le reste des informations sur l'action en question puis fusionne les infos qu'on envoi au JS (Eco -> Moins de requetes lors du chargement de la page)
  if (isset($_GET['ajax']) && isset($_GET['id'])) {
      header('Content-Type: application/json');
      $id = (int)$_GET['id'];

      // Si l'action existe
      if (isset($allActions[$id])) {
          try {
              $res = db_one("SELECT description as desc FROM action WHERE id = ?", [$id]);

              // La fusion des infos
              $fullAction = $allActions[$id];
              $fullAction['desc'] = $res['desc'] ?? "Aucune description disponible.";

              echo json_encode($fullAction); //On encode en json pour le JS
          } catch (Exception $e) {
              http_response_code(500);
              echo json_encode(['error' => $e->getMessage()]);
          }
      } else {
          http_response_code(404);
          echo json_encode(['error' => "Action non trouvée"]);
      }
      exit;
  }
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Liste des actions</title>
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css" />
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css" />
    <link rel="stylesheet" href="./assets/css/dashboard_actions.css" />
    <script>
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
    <script src="./assets/js/dashboard_actions.js" defer></script>
  </head>
  <body>

      <main-header></main-header>

      <main>
          <h1 id="Titre">Liste des actions</h1>
          <?php if ($nb_actions > 0 ): ?>
            <?php foreach ($allActions as $id => $data): ?>
                <div class="action" data-id="<?= $id ?>">
                      <input type="checkbox"
                              id="action-<?= $id ?>"
                              <?= in_array($id, $userActions) ? 'checked' : '' ?>
                              disabled />
                      <label for="action-<?= $id ?>" class="action-name">
                          <?= $data['label'] ?>
                      </label>
                      <span class="action-points"><?= $data['pts'] ?> points</span>
                </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-action">Aucune action n'est disponible pour le moment.</p>
          <?php endif; ?>
      </main>

      <div id="action-modal" class="modal">
          <div class="modal-content">
              <div class="modal-header">
                  <button class="close-btn">&times;</button>
                  <script>
                      const modal = document.getElementById("action-modal");
                      const closeBtn = document.querySelector(".close-btn");

                      closeBtn.addEventListener("click", () => {
                          modal.style.display = "none";
                      });
                  </script>
              </div>

              <h2 id="modal-label">Grosse bite</h2>
              <p id="modal-points">petite bite</p>
              <p id="modal-desc">12 cm</p>
          </div>
      </div>

      <main-footer></main-footer>

  </body>
</html>
