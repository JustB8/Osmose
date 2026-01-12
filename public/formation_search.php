<?php
session_start();
$isLogged = isset($_SESSION['user']);
$type_search = isset($_GET['type_search']) ? $_GET['type_search'] : 'thematique';

switch ($type_search) {
    case 'formation':
        $texte = "Formation";
        break;
    case 'activite':
        $texte = "Activité";
        break;
    default:
        $texte = "Thématique";
        break;
}

function is_selected($btn_name, $type_search): string {
    if ($btn_name == $type_search) {
        return " btn_selected";
    }
    return "";
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
    <section class="top_part">
      <form action="/action_page.php">
        <h1>Recherche <?= $texte ?></h1>

        <div class="searchbar">
          <input type="text" placeholder="Search..." name="search"/>
          <button type="submit" class="search_button">
            <img src="img/loupe.svg" class="lens" alt="Lancer recherche"/>
          </button>
        </div>

        <div class="btn_type">
          <a type="button" class="type_search<?= is_selected("thematique", $type_search) ?>" href="?type_search=thematique">Thématique</a>
          <a type="button" class="type_search<?= is_selected("formation", $type_search) ?>" href="?type_search=formation">Formation</a>
          <a type="button" class="type_search<?= is_selected("activite", $type_search) ?>" href="?type_search=activite">Activité</a>
        </div>
      </form>
    </section>
  </main>

  <main-footer></main-footer>
  </body>
</html>
