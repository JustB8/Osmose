document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('popup-question');
    const reminder = document.getElementById('reminder-modal');
    const question = window.QUESTION_DU_JOUR;
    const feedback = document.getElementById('feedback-message');
    const form = document.getElementById('form-question');
    const btnValider = document.getElementById('btn-valider-js');
    const modalFooterButtons = document.querySelector('.modal-footer');
    const modalFooterLoading = document.querySelector('.modal-footer-charg');
    const inputAnswer = document.getElementById('user-answer');

    if (modalFooterLoading) modalFooterLoading.style.display = 'none';

    if (question && question.id) {
        document.getElementById('popup-libelle').innerText = question.question;
        document.getElementById('popup-question-id').value = question.id;

        // Si la question est marquée comme "reportée", on montre le rappel
        // Sinon, on montre la popup principale
        if (question.is_reported) {
            reminder.style.display = 'block';
        } else {
            popup.classList.add('active');
        }
    }

    const showSpinner = () => {
        if (modalFooterButtons) modalFooterButtons.style.display = 'none';
        if (modalFooterLoading) modalFooterLoading.style.display = 'block';
    };

    if (btnValider) {
        btnValider.onclick = () => {
            const userAnswer = document.getElementById('user-answer').value.trim();
            const correctAnswer = question.answer; // Réponse correcte venant de la BDD
            
            if (inputAnswer) {
                inputAnswer.disabled = true;
            }

            if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                feedback.style.color = "green";
                feedback.innerText = "Félicitations ! C'est la bonne réponse.";
            } else {
                feedback.style.color = "red";
                feedback.innerText = `Dommage ! La bonne réponse était : ${correctAnswer}`;
            }

            // ON AFFICHE LE CHARGEMENT ICI
            showSpinner();

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

    // Clic sur la petite modal pour rouvrir la question
    if (reminder) {
        reminder.onclick = () => {
            if (inputAnswer) {
                inputAnswer.disabled = false;
            }
            if (modalFooterButtons) modalFooterButtons.style.display = 'flex'; // ou 'block' selon votre CSS
            if (modalFooterLoading) modalFooterLoading.style.display = 'none';
            reminder.style.display = 'none';
            popup.classList.add('active');
        };
    }

    // Le bouton "Plus tard" ne doit plus recharger la page !
    // Il doit juste basculer l'affichage vers la petite modal
    const closeBtn = document.getElementById('btn-close-popup');
    if (closeBtn) {
        closeBtn.onclick = () => {

            if (inputAnswer) {
                inputAnswer.disabled = true;
            }

            // ON AFFICHE LE CHARGEMENT ICI
            showSpinner();

            popup.classList.remove('active');
            reminder.style.display = 'block';

            // On informe le serveur du report en arrière-plan (AJAX léger)
            fetch('Dashboard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=report_question'
            });
        };
    }

    // ... reste de votre logique de validation ...
});