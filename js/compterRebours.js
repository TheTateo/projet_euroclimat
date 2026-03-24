let timerAllumage = null;

function demarrerCompteARebours(duree) {
    // Si un timer existait déjà, on l'arrête
    if (timerAllumage) clearInterval(timerAllumage);

    let restant = duree; // durée en secondes
    const td = document.getElementById("compteAReboursAllumage");

    timerAllumage = setInterval(() => {
        if (restant <= 0) {
            clearInterval(timerAllumage);
            td.textContent = "0 s";
            // Ici tu peux aussi couper l'allumage si besoin
            return;
        }

        td.textContent = restant + " s";
        restant--;
    }, 1000);
}