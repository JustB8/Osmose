class MyHeader extends HTMLElement {
    connectedCallback() {
        if (window.IS_LOGGED) {
            this.innerHTML = `
            <nav role="navigation" aria-label="Liens pour skip" >
                <ul class="skip-links">
                    <li><a href="#site" class="skip-links__link visually-hidden-focusable">Passer à mon profil</a></li>
                    <li><a href="#Button_search" class="skip-links__link visually-hidden-focusable">Passer à la déconnexion</a></li>
                    <li><a href="#main-content" class="skip-links__link visually-hidden-focusable">Passer au contenu principal</a></li>
                    <li><a href="#main-content" class="skip-links__link visually-hidden-focusable">Passer au bas de page</a></li>
                </ul>
            </nav>
            <div class="header">
                <header>  
                    <div class="icon_container">
                        <a href="/index.php" class="logo-link">
                            <img id="site_logo" src="img/logo.svg" alt="Logo du site" />
                        </a>
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <a href="/profil_util.php" class="bouton-noir">
                            <span class="button-2">Mon profil</span>
                        </a>
                        <a href="/logout.php" class="bouton-blanc">
                            <span class="text-wrapper">Déconnexion</span>
                        </a>
                    </nav>
                </header>
            </div>
            `;
        } else {
            this.innerHTML = `
            <nav role="navigation" aria-label="Liens pour skip" >
                <ul class="skip-links">
                    <li><a href="#cree_compte" class="skip-links__link visually-hidden-focusable">Passer à la création de compte</a></li>
                    <li><a href="#Button_search" class="skip-links__link visually-hidden-focusable">Passer à la connexion</a></li>
                    <li><a href="#main-content" class="skip-links__link visually-hidden-focusable">Passer au contenu principal</a></li>
                    <li><a href="#main-content" class="skip-links__link visually-hidden-focusable">Passer au bas de page</a></li>
                </ul>
            </nav>
            <div class="header">
                <header>  
                    <div class="icon_container">
                        <a href="/index.php" class="logo-link">
                            <img id="site_logo" src="img/logo.svg" alt="Logo du site" />
                        </a>
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <a href="/connexion.php?form=register" class="bouton-blanc">
                            <span id="cree_compte" >Créer un compte</span>
                        </a>
                        <a href="/connexion.php" class="bouton-noir">
                            <span id="connexion_compte">Connexion</span>
                        </a>
                    </nav>
                </header>
            </div>
            `;
        }
    }
}

class MyFooter extends HTMLElement {
    connectedCallback() {
        if (window.IS_LOGGED) {
            this.innerHTML = `
            <footer class="footer">
                <a href="/index.php" class="logo-link">
                    <img class="title" src="img/logo.svg" alt="Osmose" />
                </a>

                <nav class="text-link-list" aria-labelledby="footer-compte">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-compte">
                            <span class="text-strong-2">Compte</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/profil_util.php" class="list-item">Mon profil</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/logout.php" class="list-item-2">Déconnexion</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-leaderboard">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-leaderboard">                            
                            <p class="text-strong-2">LeaderBoard</p>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/leaderboard.php" class="list-item-3">Entreprise</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-mentions">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-mentions">
                            <span class="text-strong-2">Mention du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/legal_notice.php" class="list-item-4">Mention légale</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/rgaa.php" class="list-item">Justificatif RGAA</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-plan">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-plan">
                            <span class="text-strong-2">Plan du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/sitemap.php" class="list-item-5">Plan du site</a>
                    </div>
                    <div class="text-link-list-item"></div>
                </nav>
            </footer>
            `;
        } else {
            this.innerHTML = `
            <footer class="footer">
                <a href="/index.php" class="logo-link">
                    <img class="title" src="img/logo.svg" alt="Osmose" />
                </a>

                <nav class="text-link-list" aria-labelledby="footer-compte">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-compte">
                            <span class="text-strong-2">Compte</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/connexion.php?form=register" class="list-item">Créer un compte</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/connexion.php" class="list-item-2">Se connecter</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-leaderboard">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-leaderboard">
                            <p class="text-strong-2">LeaderBoard</p>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/leaderboard.php" class="list-item-3">Entreprise</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-mentions">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-mentions">
                            <span class="text-strong-2">Mention du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/legal_notice.php" class="list-item-4">Mention légale</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/rgaa.php" class="list-item">Justificatif RGAA</a>
                    </div>
                </nav>

                <nav class="text-link-list" aria-labelledby="footer-plan">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-plan">
                            <span class="text-strong-2">Plan du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/sitemap.php" class="list-item-5">Plan du site</a>
                    </div>
                    <div class="text-link-list-item"></div>
                </nav>
            </footer>
            `;
        }
    }
}

customElements.define('main-header', MyHeader);
customElements.define('main-footer', MyFooter);
