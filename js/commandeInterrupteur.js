function envoyerCommandeAllumage() {
    let duree = parseInt(document.getElementById("tempsAllumage").value);
    if (isNaN(duree) || duree <= 0) return;

    // utiliser la variable globale définie dans le PHP
    fetch("allumage.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            duree: duree,
            utilisateur_id: utilisateurId
        })
    });

    // Lancer le compte à rebours dans le tableau
    demarrerCompteARebours(duree);
}