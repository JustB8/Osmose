<?php
session_start();
$isLogged = isset($_SESSION['user']);
$type_search = isset($_GET['type_search']) ? $_GET['type_search'] : 'thematique';

switch ($type_search) {
    case 'formation':
        $texte = "Formation";
        break;
    case 'activite':
        $texte = "Activié";
        break;
    default:
        $texte = "Thématique";
        break;
}
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
    <p>Recherche de <?= $texte ?></p>
  </main>

  <main-footer></main-footer>
  </body>
</html>
