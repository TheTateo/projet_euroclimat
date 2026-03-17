let chartTemp = null;
let typeCourbe = "temperature";

// Choix du type de courbe
function choisirCourbe(type) {
    typeCourbe = type;
    demanderCourbes();
}

// Afficher options + charger la courbe
function demanderCourbes() {
    cacherToutesZones();
    document.getElementById("optionsCourbe").style.display = "block";
    adapterDates();
    chargerCourbe();
}

// Adapter les dates selon le type sélectionné
function adapterDates() {
    const type = document.getElementById("typePlage").value;
    const today = new Date().toISOString().split("T")[0];

    const jourContainer = document.getElementById("jourContainer");
    const periodeContainer = document.getElementById("periodeContainer");

    if (type === "jour") {
        jourContainer.style.display = "block";
        periodeContainer.style.display = "none";
        document.getElementById("dateJour").value = today;
    } else {
        jourContainer.style.display = "none";
        periodeContainer.style.display = "block";

        if (type === "semaine") {
            let d = new Date();
            d.setDate(d.getDate() - 7);
            document.getElementById("dateDebut").value = d.toISOString().split("T")[0];
            document.getElementById("dateFin").value = today;
        } else if (type === "custom") {
            document.getElementById("dateDebut").value = today;
            document.getElementById("dateFin").value = today;
        }
    }

    chargerCourbe();
}

// Charger les données via fetch
function chargerCourbe() {
    let debut, fin;
    const type = document.getElementById("typePlage").value;

    if (type === "jour") {
        const jour = document.getElementById("dateJour").value;
        if (!jour) return;
        debut = jour;
        fin = jour;
    } else {
        debut = document.getElementById("dateDebut").value;
        fin = document.getElementById("dateFin").value;
        if (!debut || !fin) return;
    }

    fetch("getData.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ dateDebut: debut, dateFin: fin, type: typeCourbe })
    })
    .then(res => res.json())
    .then(data => afficherCourbe(data))
    .catch(err => console.error("Erreur fetch :", err));
}

function getTitreGraphique() {
    const typePlage = document.getElementById("typePlage").value;

    if (typePlage === "jour") {
        const jour = document.getElementById("dateJour").value;
       return "Graphique du " + jour.split("-").reverse().join("/");
    } else {
        const debut = document.getElementById("dateDebut").value;
        const fin = document.getElementById("dateFin").value;
        return "Graphique du " + debut + " au " + fin;
    }
}

// Affichage de la courbe
function afficherCourbe(data) {
    if (!data || !Array.isArray(data) || data.length === 0) {
        console.log("Pas de données");
        return;
    }

    const typePlage = document.getElementById("typePlage").value;
    let labels;

    if (typePlage === "jour") {
    // Affiche l'heure
    labels = data.map(d => d.heure);
    } else {
        // Affiche date + heure
        labels = data.map(d => d.date_j + " " + d.heure);
    }

    let valeurs = [];

    switch(typeCourbe) {
        case "temperature":
            valeurs = data.map(d => d.temperature);
            break;
        case "courant":
            valeurs = data.map(d => d.courant_secteur);
            break;
    }

    if (chartTemp) chartTemp.destroy();

    chartTemp = new Chart(document.getElementById("graphTemp"), {
    type: "line",
    data: {
            labels: labels,
            datasets: [{
                label: typeCourbe,
                data: valeurs,
                borderColor:
                    typeCourbe === "temperature" ? "red" :
                    typeCourbe === "allumage" ? "blue" : "green",
                tension: 0.3
            }]
        },
        options: {
            plugins: {
                        title: {
                            display: true,
                            text: getTitreGraphique()
                        }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Date et heure"
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: getLabelAxeY()
                    }
                }
            }
        }
    });
}

function getLabelAxeY() {
    switch(typeCourbe) {
        case "temperature":
            return "Température (°C)";
        case "allumage":
            return "État (0 = éteint, 1 = allumé)";
        case "courant":
            return "Courant (A)";
        default:
            return "";
    }
}

// Cacher toutes les zones
function cacherToutesZones() {
    const zones = ["optionsCourbe"];
    zones.forEach(id => {
        const elem = document.getElementById(id);
        if (elem) elem.style.display = "none";
    });
}

// Événements automatiques
document.getElementById("typePlage").addEventListener("change", adapterDates);
document.getElementById("dateJour").addEventListener("change", chargerCourbe);
document.getElementById("dateDebut").addEventListener("change", chargerCourbe);
document.getElementById("dateFin").addEventListener("change", chargerCourbe);

// Rafraîchissement toutes les 5 minutes
setInterval(() => {
    if (document.getElementById("optionsCourbe").style.display === "block") {
        chargerCourbe();
    }
}, 300000);