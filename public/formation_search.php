<?php
session_start();
require_once __DIR__ . '/call_bdd.php';

$isLogged = isset($_SESSION['user']);
$type_search = isset($_GET['type_search']) ? $_GET['type_search'] : null;
$uti_search = isset($_GET['uti_search']) ? $_GET['uti_search'] : null;
$search_result = null;

switch ($type_search) {
    case 'formation':
        $texte = " par formation";

        if (!empty($uti_search))  {
            $search_result = db_all(
            "SELECT id, name
                FROM formation
                WHERE name ILIKE :search",
                    ['search' => '%'.$uti_search.'%']
            );
        }else {
          $search_result = db_all(
            "SELECT id, name
                FROM formation",
            );

        }
        break;

    case 'activite':
        $texte = "par activité";

        if (!empty($uti_search))  {
            $search_result = db_all(
            "SELECT id, name
                FROM formation
                WHERE name ILIKE :search",
                    ['search' => '%'.$uti_search.'%']
            );
        }else {
          $search_result = db_all(
            "SELECT id, name
                FROM formation",
            );

        }
        break;

    case 'thematique' :
      $texte = "par thématique";

      if (!empty($uti_search))  {
            $search_result = db_all(
            "SELECT id, name
                FROM formation
                WHERE name ILIKE :search",
                    ['search' => '%'.$uti_search.'%']
            );
      }else {
          $search_result = db_all(
            "SELECT id, name
                FROM formation",
            );

        }
      break;

    default:
        $texte = "toutes les formations";

        if (empty($uti_search))  {
            $search_result = db_all(
            "SELECT id, name
                FROM formation",
            );
        }
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
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/footer.css"/>
    <link rel="stylesheet" href="./assets/css/formation_search.css" />
    <script>
      window.IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;


    </script>
    <script src="assets/js/composents.js"></script>
  </head>
  <body>
  <main-header></main-header>

  <main class="body">
    <div class="pu-top-actions">
        <a href="/profil_util.php" class="pu-btn secondary">⬅️ Profil</a>
        <a href="/dashboard.php" class="pu-btn secondary">Dashboard ➡️</a>
    </div>
    <section class="top_part">
      <form method="get" action="">
        <h1>Recherche <?= $texte ?></h1>

        <div class="searchbar">
          <input type="text" placeholder="Search..." name="uti_search" value="<?= htmlspecialchars($uti_search) ?>"/>
          <button type="submit" class="search_button">
            <img src="img/loupe.svg" class="lens" alt="Lancer la recherche"/>
          </button>
        </div>

        <input type="hidden" name="type_search" id="type_search" value="<?= $type_search ?>">
        <div class="btn_type">
          <button
                  class="type_search<?= is_selected("thematique", $type_search) ?>"
                  onclick="setType('thematique')">
            Thématique
          </button>
          <button
                  class="type_search<?= is_selected("formation", $type_search) ?>"
                  onclick="setType('formation')">
            Formation
          </button>
          <button
                  class="type_search<?= is_selected("activite", $type_search) ?>"
                  onclick="setType('activite')">
            Activité
          </button>
        </div>
      </form>

      <script>
        function setType(type) {
          document.getElementById('type_search').value = type;
          document.querySelector('form').submit(); 
          }
      </script>
    </section>

    <section class="result_section">
      <?php if (isset($search_result)) { ?>
        <div class="list_result">
          <h2>Voici vos résultats pour <?= $texte ?> :</h2>
          <?php foreach ($search_result as $result) { ?>
            <div class="search_result">
              <a href="formation.php?id=<?= $result['id'] ?>"><?= $result['name'] ?></a>
            </div>
          <?php }?>
        </div>
      <?php } else { ?>
        <h2>Vos résultats apparaîtront ici !</h2>
      <?php } ?>
    </section>
  </main>

  <main-footer></main-footer>
  </body>
</html>
