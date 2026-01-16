class MyHeader extends HTMLElement {
    connectedCallback() {
        if (window.IS_LOGGED) {
            this.innerHTML = `
            <nav role="navigation" aria-label="Liens pour skip" >
                <ul class="skip-links">
                    <li><a href="#bouton-noir-profil" class="skip-links__link visually-hidden-focusable">Passer à mon profil</a></li>
                    <li><a href="#bouton-blanc-deco" class="skip-links__link visually-hidden-focusable">Passer à la déconnexion</a></li>
                    <li><a href="#id="main-content" class="skip-links__link visually-hidden-focusable">Passer au contenu principal</a></li>
                    <li><a href="#logo-link-footer" class="skip-links__link visually-hidden-focusable">Passer au bas de page</a></li>
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
                        <a href="/profil_util.php" id="bouton-noir-profil">
                            <span id="profil">Mon profil</span>
                        </a>
                        <a href="/logout.php" id="bouton-blanc-deco">
                            <span id="deconnex">Déconnexion</span>
                        </a>
                    </nav>
                </header>
            </div>
            `;
        } else {
            this.innerHTML = `
            <nav role="navigation" aria-label="Liens pour skip" >
                <ul class="skip-links">
                    <li><a href="#bouton-blanc-crea" class="skip-links__link visually-hidden-focusable">Passer à la création de compte</a></li>
                    <li><a href="#bouton-noir-con" class="skip-links__link visually-hidden-focusable">Passer à la connexion</a></li>
                    <li><a href="#main-content" class="skip-links__link visually-hidden-focusable">Passer au contenu principal</a></li>
                    <li><a href="#logo-link-footer" class="skip-links__link visually-hidden-focusable">Passer au bas de page</a></li>
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
                        <a href="/connexion.php?form=register" id="bouton-blanc-crea">
                            <span id="cree_compte" >Créer un compte</span>
                        </a>
                        <a href="/connexion.php" id="bouton-noir-con">
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
            <footer>
                <a href="/index.php" id="logo-link-footer">
                    <img class="title" src="img/logo.svg" alt="Osmose" />
                </a>
                <nav class="liste-liens" aria-labelledby="footer-compte">
                    <div>
                        <h2 id="footer-compte">
                            <span class="text-strong">Compte</span>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/profil_util.php" class="lien">Mon profil</a>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/logout.php" class="lien">Déconnexion</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-leaderboard">
                    <div>
                        <h2 id="footer-leaderboard">
                            <p class="text-strong">LeaderBoard</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/leaderboard.php" class="lien">Entreprise</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-mentions">
                    <div>
                        <h2 id="footer-mentions">
                            <p class="text-strong">Mention du site</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/legal_notice.php" class="lien">Mention légale</a>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/rgaa.php" class="lien">Justificatif RGAA</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-plan">
                    <div>
                        <h2 id="footer-plan">
                            <p class="text-strong">Plan du site</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/sitemap.php" class="lien">Plan du site</a>
                    </div>
                    <div class="liste-liens-item"></div>
                </nav>
            </footer>
            `;
        } else {
            this.innerHTML = `
            <footer>
                <a href="/index.php" id="logo-link-footer">
                    <img class="title" src="img/logo.svg" alt="Osmose" />
                </a>
                <nav class="liste-liens" aria-labelledby="footer-compte">
                    <div>
                        <h2 id="footer-compte">
                            <p class="text-strong">Compte</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/connexion.php?form=register" class="lien">Créer un compte</a>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/connexion.php" class="lien">Se connecter</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-leaderboard">
                    <div>
                        <h2 id="footer-leaderboard">
                            <p class="text-strong">LeaderBoard</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/leaderboard.php" class="lien">Entreprise</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-mentions">
                    <div>
                        <h2 id="footer-mentions">
                            <p class="text-strong">Mention du site</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/legal_notice.php" class="lien">Mention légale</a>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/rgaa.php" class="lien">Justificatif RGAA</a>
                    </div>
                </nav>

                <nav class="liste-liens" aria-labelledby="footer-plan">
                    <div>
                        <h2 id="footer-plan">
                            <p class="text-strong">Plan du site</p>
                        </h2>
                    </div>
                    <div class="liste-liens-item">
                        <a href="/sitemap.php" class="lien">Plan du site</a>
                    </div>
                    <div class="liste-liens-item"></div>
                </nav>
            </footer>
            `;
        }
    }
}

customElements.define('main-header', MyHeader);
customElements.define('main-footer', MyFooter);
