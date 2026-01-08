<?php
session_start();
$isLogged = isset($_SESSION['user']);
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
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;
    </script>
    <script src="./assets/js/composents.js"></script>
  </head>
  <body>
    <div class="ecran-de-desktop">
      <main-header></main-header>
      <main class="component">
        <div class="points">
          <section class="points-section">
            <h2 class="p">Nombre de Points de l'entreprise</h2>
            <div class="rectangle" role="img" aria-label="Cadre des points de l'entreprise">
              <div class="text-wrapper-1" aria-live="polite">1000 Points</div>
            </div> 
          </section>
          <section class="points-section-user">
            <h2 class="text-wrapper-3">Nombre de Points de l'utilisateur</h2>
            <div class="rectangle-2" role="img" aria-label="Cadre des points de l'utilisateur">
              <div class="text-wrapper-2" aria-live="polite">2000 Points</div>
            </div>
          </section>
        </div>
        <div class = "activité-formation">
          <article class="rectangle-3">
            <h3 class="text-wrapper-4">Dernière activité</h3>
            <p class="text-wrapper-6">Les pingouins sur la banquise</p>
          </article>
          <article class="rectangle-4">
            <h3 class="text-wrapper-5">Dernière formation</h3>
            <p class="text-wrapper-7">La banquise sous tension</p>
          </article>
        </div>
      </main>
      <main-footer></main-footer>
    </div>
  </body>
</html>
