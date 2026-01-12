<?php
session_start();
$isLogged = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Recherche de Formation</title>
    <link rel="stylesheet" href="./assets/css/globals.css" />
    <link rel="stylesheet" href="./assets/css/styleguide.css"/>
    <link rel="stylesheet" href="./assets/css/formation_search.css" />
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <script>
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="assets/js/composents.js"></script>
  </head>
  <body>
  <main-header></main-header>

  <main class="body">
    <section class="recherche-formation" aria-label="Recherche et filtres">
      <form class="barre-de-recherche" role="search">
        <label for="search-input" class="visually-hidden">Rechercher une formation</label>
        <input
          type="search"
          id="search-input"
          class="search-input"
          placeholder="Rechercher..."
          aria-label="Rechercher une formation"
        />
        <img class="line" src="img/line-1.svg" alt="" role="presentation" />
        <button type="submit" class="search-button" aria-label="Lancer la recherche">
          <img class="image" src="img/image-5.png" alt="" />
        </button>
      </form>
      <div class="filtre-thmatique">
        <button type="button" class="filter-button" aria-label="Filtrer par thématique">
          <img class="rectangle" src="img/rectangle-18.svg" alt="" role="presentation" />
          <span class="text-wrapper-2">Thématique</span>
        </button>
      </div>
      <div class="filtre-formation">
        <button type="button" class="filter-button" aria-label="Filtrer par formation">
          <img class="rectangle" src="img/rectangle-16.svg" alt="" role="presentation" />
          <span class="text-wrapper-3">Formation</span>
        </button>
      </div>
      <div class="filtre-activit">
        <button type="button" class="filter-button" aria-label="Filtrer par activité">
          <img class="rectangle" src="img/rectangle-17.svg" alt="" role="presentation" />
          <span class="text-wrapper-4">Activité</span>
        </button>
      </div>
    </section>
    <section class="resultats-recherche" aria-label="Résultats de recherche">
      <h1 class="text-wrapper-5">Liste de ....</h1>
      <ul class="results-list">
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
        <li class="rectangle-2"></li>
      </ul>
    </section>
  </main>

  <main-footer></main-footer>
  </body>
</html>
