<?php
session_start();
$isLogged = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Liste des actions - Eco Actions</title>
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css" />
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css" />
    <link rel="stylesheet" href="./assets/css/DashboardActions.css" />
    <script>
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
  </head>
  <body>
      <main-header></main-header>
      <main class="body">
        <div class="body-2">
          <h1 class="text-wrapper-7">Liste des actions</h1>
          <form class="actions-form">
            <div class="action-item">
              <input type="checkbox" id="action-1" name="action-1" class="rectangle" />
              <label for="action-1" class="text-wrapper-2">Trier ses déchets</label>
              <span class="text-wrapper-8" aria-label="10 points pour cette action">10 points</span>
            </div>
            <div class="action-item">
              <input type="checkbox" id="action-2" name="action-2" class="rectangle-2" />
              <label for="action-2" class="text-wrapper-3">Eteindre les écrans</label>
              <span class="text-wrapper-9" aria-label="10 points pour cette action">10 points</span>
            </div>
            <div class="action-item">
              <input type="checkbox" id="action-3" name="action-3" class="rectangle-3" />
              <label for="action-3" class="text-wrapper-4">Utiliser son vélo</label>
              <span class="text-wrapper-10" aria-label="10 points pour cette action">10 points</span>
            </div>
            <div class="action-item">
              <input type="checkbox" id="action-4" name="action-4" class="rectangle-4" />
              <label for="action-4" class="text-wrapper-5">Utiliser une voiture éléctrique</label>
              <span class="text-wrapper-11" aria-label="10 points pour cette action">10 points</span>
            </div>
            <div class="action-item">
              <input type="checkbox" id="action-5" name="action-5" class="rectangle-5" />
              <label for="action-5" class="text-wrapper-6">Ne pas utiliser l'avion</label>
              <span class="text-wrapper-12" aria-label="10 points pour cette action">10 points</span>
            </div>
          </form>
        </div>
      </main>
      <div class="button-valider">
        <button type="submit" class="rectangle-6" aria-label="Valider les actions sélectionnées">
          <span class="text-wrapper-13">Valider</span>
        </button>
      </div>
      <main-footer></main-footer>
  </body>
</html>
