class MyHeader extends HTMLElement {
    connectedCallback() {
        if (window.IS_LOGGED) {
            this.innerHTML = `
            <div class="header">
                <header class="header-2">  
                    <div class="icon_container">
                        <a href="index.php" class="Osmose">
                            <img src="img/logo.svg" alt="Accueil" class="icon">
                        </a>                        
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <button class="div" type="button">
                            <span class="con-profil">Mon profil</span>
                        </button>
                        <button class="button" type="button">
                            <span>Déconnexion</span>
                        </button>
                    </nav>
                </header>
            </div>
            `;
        } else {
            this.innerHTML = `
            <div class="header">
                <header class="header-2">  
                    <div class="icon_container">
                        <a href="index.php" class="Osmose">
                            <img src="img/logo.svg" alt="Accueil" class="icon">
                        </a>
                    </div>
                    <nav class="header-auth" aria-label="Navigation principale">
                        <button class="button" type="button">
                            <a href="connexion.php?form=register">Créer un compte</a>
                        </button>
                        <button class="div" type="button">
                            <a href="connexion.php?form=login" class="con-profil">Connexion</a>
                        </button>
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
                <img class="title" src="img/logo.svg" alt="Osmose" />
                <nav class="text-link-list" aria-labelledby="footer-compte">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-compte">
                            <span class="text-strong-2">Compte</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item">Mon profil</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-2">Déconnexion</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-leaderboard">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-leaderboard">                            
                            <a href="leaderboard.php" class="text-strong-2">LeaderBoard</a>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-3">Entreprise</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-mentions">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-mentions">
                            <span class="text-strong-2">Mention du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-4">Mention légale</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item">Justificatif RGAA</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-plan">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-plan">
                            <span class="text-strong-2">Plan du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-5">Plan du site</a>
                    </div>
                    <div class="text-link-list-item"></div>
                </nav>
            </footer>
            `;
        } else {
            this.innerHTML = `
            <footer class="footer">
                <img class="title" src="img/logo.svg" alt="Osmose" />
                <nav class="text-link-list" aria-labelledby="footer-compte">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-compte">
                            <span class="text-strong-2">Compte</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="connexion.php?form=register" class="list-item">Créer un compte</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="connexion.php?form=login" class="list-item-2">Se connecter</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-leaderboard">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-leaderboard">
                            <a href="leaderboard.php" class="text-strong-2">LeaderBoard</a>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-3">Entreprise</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-mentions">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-mentions">
                            <span class="text-strong-2">Mention du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-4">Mention légale</a>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item">Justificatif RGAA</a>
                    </div>
                </nav>
                <nav class="text-link-list" aria-labelledby="footer-plan">
                    <div class="text-strong-wrapper">
                        <h2 class="text-strong" id="footer-plan">
                            <span class="text-strong-2">Plan du site</span>
                        </h2>
                    </div>
                    <div class="text-link-list-item">
                        <a href="#" class="list-item-5">Plan du site</a>
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