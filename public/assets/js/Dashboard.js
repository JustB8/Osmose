document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('popup-question');
    const question = window.QUESTION_DU_JOUR;
    const feedback = document.getElementById('feedback-message');
    const form = document.getElementById('form-question');
    const btnValider = document.getElementById('btn-valider-js');

    // Affichage initial de la popup
    if (question && question.id) {
        document.getElementById('popup-libelle').innerText = question.question; // Utilise 'question' du SELECT
        document.getElementById('popup-question-id').value = question.id;
        popup.classList.add('active'); 
    }

    if (btnValider) {
        btnValider.onclick = () => {
            const userAnswer = document.getElementById('user-answer').value.trim();
            const correctAnswer = question.answer; // Réponse correcte venant de la BDD

            if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                feedback.style.color = "green";
                feedback.innerText = "Félicitations ! C'est la bonne réponse.";
            } else {
                feedback.style.color = "red";
                feedback.innerText = `Dommage ! La bonne réponse était : ${correctAnswer}`;
            }

            // Désactiver le bouton pour éviter les doubles clics
            btnValider.disabled = true;

            // Attendre 3 secondes pour que l'utilisateur lise le message, puis envoyer le formulaire
            setTimeout(() => {
                // Créer un champ caché pour simuler le bouton 'valider_reponse' attendu par le PHP
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'valider_reponse';
                hiddenInput.value = 'true';
                form.appendChild(hiddenInput);
                
                form.submit(); // Envoie les données vers Dashboard.php
            }, 3000);
        };
    }

    // Gestion de la fermeture
    const closeBtn = document.getElementById('btn-close-popup');
    if (closeBtn) {
        closeBtn.onclick = () => {
            popup.classList.remove('active');
        };
    }
});