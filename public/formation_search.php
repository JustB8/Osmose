<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Recherche de Formation</title>
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="ecran-de-desktop">
      <header class="header">
        <div class="div">
          <div class="block">
            <div class="figma">
              <img class="icon" src="img/icon.svg" alt="Logo" />
            </div>
          </div>
          <nav class="header-auth" aria-label="Navigation principale">
            <button class="button" type="button" aria-label="Créer un compte">
              <span class="text-wrapper">Créer un compte</span>
            </button>
            <button class="button-2" type="button" aria-label="Connexion">
              <span class="button-3">Connexion</span>
            </button>
          </nav>
        </div>
      </header>
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
      <footer class="footer">
        <img class="title" src="img/title.svg" alt="Logo du site" />
        <nav class="text-link-list" aria-label="Navigation compte">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h2 class="text-strong-2">Compte</h2>
            </div>
          </div>
          <ul class="footer-links">
            <li class="text-link-list-item">
              <a href="#" class="list-item">Créer un compte</a>
            </li>
            <li class="text-link-list-item">
              <a href="#" class="list-item-2">Se connecter</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="Navigation leaderboard">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h2 class="text-strong-2">LeaderBoard</h2>
            </div>
          </div>
          <ul class="footer-links">
            <li class="text-link-list-item">
              <a href="#" class="list-item-3">Entreprise</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="Navigation mentions légales">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h2 class="text-strong-2">Mention du site</h2>
            </div>
          </div>
          <ul class="footer-links">
            <li class="text-link-list-item">
              <a href="#" class="list-item-4">Mention légale</a>
            </li>
            <li class="text-link-list-item">
              <a href="#" class="list-item">Justificatif RGAA</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="Navigation plan du site">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h2 class="text-strong-2">Plan du site</h2>
            </div>
          </div>
          <ul class="footer-links">
            <li class="text-link-list-item">
              <a href="#" class="list-item-5">Plan du site</a>
            </li>
          </ul>
        </nav>
      </footer>
    </div>
  </body>
</html>
