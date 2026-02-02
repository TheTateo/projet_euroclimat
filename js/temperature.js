let chartTemp = null;

function demanderCourbes() {
    cacherToutesZones();
    document.getElementById("optionsCourbe").style.display = "block";
}

function adapterDates() {
    const type = document.getElementById("typePlage").value;
    const debut = document.getElementById("dateDebut");
    const fin = document.getElementById("dateFin");

    const today = new Date().toISOString().split("T")[0];

    if (type === "jour") {
        debut.value = today;
        fin.value = today;
    }

    if (type === "semaine") {
        let d = new Date();
        d.setDate(d.getDate() - 7);
        debut.value = d.toISOString().split("T")[0];
        fin.value = today;
    }
}

function chargerCourbe() {
    const debut = document.getElementById("dateDebut").value;
    const fin = document.getElementById("dateFin").value;

    fetch("getTemperatures.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ dateDebut: debut, dateFin: fin })
    })
    .then(res => res.json())
    .then(data => afficherCourbe(data));
}

function afficherCourbe(data) {
    const labels = data.map(d => d.date_mesure);
    const valeurs = data.map(d => d.temperature);

    if (chartTemp) chartTemp.destroy();

    chartTemp = new Chart(document.getElementById("graphTemp"), {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Température (°C)",
                data: valeurs,
                borderColor: "red",
                tension: 0.3
            }]
        }
    });
}

function demanderDureeAllumage() {
    cacherToutesZones();
    document.getElementById("commandeAllumage").style.display = "block";
}

function envoyerCommandeAllumage() {
    const duree = document.getElementById("tempsAllumage").value;

    if (duree === "" || duree <= 0) {
        alert("Veuillez entrer une durée valide en secondes");
        return;
    }

    fetch("setAllumage.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ duree: duree })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    });
}

function cacherToutesZones() {
    const zones = [
        "optionsCourbe",       // température
        "commandeAllumage",    // allumage
        "optionsCourant"       // courant (si tu as une zone similaire)
    ];

    zones.forEach(id => {
        const elem = document.getElementById(id);
        if (elem) elem.style.display = "none";
    });
}
