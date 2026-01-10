<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <title>Dashboard - Points et Activités</title>
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
              <img class="icon" src="img/icon.svg" alt="Logo de l'application" />
            </div>
          </div>
          <nav class="navigation-pill-list" aria-label="Navigation principale"></nav>
          <div class="header-auth">
            <button class="button" type="button" aria-label="Compte utilisateur M. Joe">
              <span class="user">M. Joe</span>
            </button>
          </div>
        </div>
      </header>
      <main class="component">
        <section class="points-section" aria-label="Points">
          <div class="points-company">
            <p class="p">Nombre de Points de l'entreprise</p>
            <div class="rectangle">
              <span class="text-wrapper">1000 Points</span>
            </div>
          </div>
          <div class="points-user">
            <p class="text-wrapper-3">Nombre de Points de l'utilisateur</p>
            <div class="rectangle-2">
              <span class="text-wrapper-2">2000 Points</span>
            </div>
          </div>
        </section>
        <section class="activity-section" aria-label="Activités et formations">
          <article class="rectangle-4">
            <h2 class="text-wrapper-7">Comment le réchaufement climatique affecte la vie des pingouins ?</h2>
            <form class="activity-form" aria-label="Formulaire de réponse">
              <label for="user-text-input" class="visually-hidden">Votre réponse</label>
              <input
                type="text"
                id="user-text-input"
                class="rectangle-6"
                placeholder="Votre texte"
                aria-required="true"
              />
              <button type="submit" class="rectangle-7">
                <span class="text-wrapper-10">Valider</span>
              </button>
            </form>
          </article>
          <article class="rectangle-3">
            <h2 class="text-wrapper-4">Dernière activité</h2>
            <p class="text-wrapper-6">Les pingouins sur la banquise</p>
          </article>
          <article class="rectangle-5">
            <h2 class="text-wrapper-5">Dernière formation</h2>
            <p class="text-wrapper-8">La banquise sous tension</p>
          </article>
        </section>
      </main>
      <footer class="footer">
        <img class="title" src="img/title.svg" alt="Logo du pied de page" />
        <nav class="text-link-list" aria-label="Compte">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h3 class="text-strong-2">Compte</h3>
            </div>
          </div>
          <ul>
            <li class="text-link-list-item">
              <a href="#" class="list-item">Créer un compte</a>
            </li>
            <li class="text-link-list-item">
              <a href="#" class="list-item-2">Se connecter</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="LeaderBoard">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h3 class="text-strong-2">LeaderBoard</h3>
            </div>
          </div>
          <ul>
            <li class="text-link-list-item">
              <a href="#" class="list-item-3">Entreprise</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="Mention du site">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h3 class="text-strong-2">Mention du site</h3>
            </div>
          </div>
          <ul>
            <li class="text-link-list-item">
              <a href="#" class="list-item-4">Mention légale</a>
            </li>
            <li class="text-link-list-item">
              <a href="#" class="list-item">Justificatif RGAA</a>
            </li>
          </ul>
        </nav>
        <nav class="text-link-list" aria-label="Plan du site">
          <div class="text-strong-wrapper">
            <div class="text-strong">
              <h3 class="text-strong-2">Plan du site</h3>
            </div>
          </div>
          <ul>
            <li class="text-link-list-item">
              <a href="#" class="list-item-5">Plan du site</a>
            </li>
          </ul>
        </nav>
      </footer>
    </div>
  </body>
</html>
