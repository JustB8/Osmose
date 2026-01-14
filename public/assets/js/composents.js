class MyHeader extends HTMLElement {
    connectedCallback() {
        if (window.IS_LOGGED) {
            this.innerHTML = `
            <div class="header">
                <header class="header-2">  
                    <div class="icon_container">
                        <a href="/index.php" class="logo-link">
                            <img class="icon" src="img/logo.svg" alt="Logo du site" />
                        </a>
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <a href="/profil_util.php" class="div">
                            <span class="button-2">Mon profil</span>
                        </a>
                        <a href="/logout.php" class="button">
                            <span class="text-wrapper">Déconnexion</span>
                        </a>
                    </nav>
                </header>
            </div>
            `;
        } else {
            this.innerHTML = `
            <div class="header">
                <header class="header-2">  
                    <div class="icon_container">
                        <a href="/index.php" class="logo-link">
                            <img class="icon" src="img/logo.svg" alt="Logo du site" />
                        </a>
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <a href="/connexion.php?form=register" class="button">
                            <span class="text-wrapper">Créer un compte</span>
                        </a>
                        <a href="/connexion.php" class="div">
                            <span class="button-2">Connexion</span>
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
                        <a href="/register.php" class="list-item">Créer un compte</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="/login.php" class="list-item-2">Se connecter</a>
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
