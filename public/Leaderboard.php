<?php
session_start();
$isLogged = isset($_SESSION['user']);
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
          <button class="rectangle-15" type="button" aria-label="Rechercher">
            <img class="image" src="img/loupe.svg" alt="Icône de recherche" />
          </button>
        </div>
        <div class="podium" role="img" aria-label="Podium des trois premières places">
          <div class="place" aria-label="Troisième place"></div>
          <div class="place-2" aria-label="Première place"></div>
          <div class="place-3" aria-label="Deuxième place"></div>
        </div>
        <section class="component" aria-labelledby="leaderboard-title">
          <div class="top">
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-1">4</span>
              </div>
              <div class="leaderboard-content rectangle"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-7">5</span>
              </div>
              <div class="leaderboard-content rectangle-14"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-6">6</span>
              </div>
              <div class="leaderboard-content rectangle-13"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-5">7</span>
              </div>
              <div class="leaderboard-content rectangle-12"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-4">8</span>
              </div>
              <div class="leaderboard-content rectangle-11"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-3">9</span>
              </div>
              <div class="leaderboard-content rectangle-9"></div>
            </div>
            <div class="leaderboard-row">
              <div class="leaderboard-rank">
                <span class="text-wrapper-2">10</span>
              </div>
              <div class="leaderboard-content rectangle-10"></div>
            </div>
          </div>
          
        </section>
        
      </main>
      <main-footer></main-footer>
  </body>
</html>
