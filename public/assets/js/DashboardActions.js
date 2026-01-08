function openModal(label, pts, desc) {
    document.getElementById('modal-label').innerText = label;
    document.getElementById('modal-points').innerText = pts + " points";
    document.getElementById('modal-desc').innerText = desc;
    document.getElementById('action-modal').style.display = "block";
}

document.querySelector('.close-btn').onclick = function() {
    document.getElementById('action-modal').style.display = "none";
}

// Fermer aussi si on clique en dehors de la boîte blanche
window.onclick = function(event) {
    let modal = document.getElementById('action-modal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}