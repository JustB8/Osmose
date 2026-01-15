document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.action');

    buttons.forEach(btn => {
        btn.onclick = function() {
            const actionId = this.getAttribute('data-id');
            fetchFullAction(actionId);
        };
    });
});

async function fetchFullAction(id) {
    try {
        const response = await fetch(`dashboard_actions.php?ajax=1&id=${id}`);
        if (!response.ok) throw new Error("Erreur lors de la récupération");
            const fullAction = await response.json();
            showModal(fullAction);
    } catch (error) {
        console.error("Erreur SQL/API :", error);
    }
}

function showModal(action) {
    document.getElementById('modal-label').innerText = action.label;
    document.getElementById('modal-points').innerText = action.pts + " points";
    document.getElementById('modal-desc').innerText = action.desc;
    document.getElementById('action-modal').style.display = 'block';
}

document.querySelector('.close-btn').onclick = function() {
    document.getElementById('action-modal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('action-modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

