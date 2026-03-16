<?php
session_start();
require 'connexion.php';
require 'mail.php';
require 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de l'Interrupteur et de la Température</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- JS des courbes -->
    <script src="js/temperature.js" defer></script>
</head>
<body>
    <h1>Suivi de l'Interrupteur et de la Température</h1>
    <p>Bienvenue sur la page d'administration client usager !</p>
    
    <!-- Affichage des données -->
    <div class="container-dashboard">

    <?php 
    $stmt_ligne = $pdo->query("SELECT id, etat_actionneur, temperature, duree_allumage
                                FROM mesures_systeme
                                ORDER BY id DESC
                                LIMIT 1");
    $ligne = $stmt_ligne->fetch(PDO::FETCH_ASSOC);

    if ($ligne) {
        $temperature = $ligne['temperature'];
    } else {
        $temperature = 0;
    }
    ?>

    <!-- Bloc informations -->
    <div class="container-info">

        <div class="gauche">
            <h2>Valeurs actuelles</h2>

            <table>
                <tr>
                    <th>Etat de l'interrupteur</th>
                    <td><?= isset($ligne['etat_actionneur']) && $ligne['etat_actionneur'] == 1 ? "Allumé" : "Éteint" ?></td>
                </tr>

                <tr>
                    <th>Température actuelle</th>
                    <td><?= htmlspecialchars($ligne['temperature']) ?> °C</td>
                </tr>

                <tr>
                    <th>Durée restante d'allumage</th>
                    <td><?= htmlspecialchars($ligne['duree_allumage']) ?> s</td>
                </tr>
            </table>
        </div>

        <div class="droite">
            <h2>Alertes</h2>
        </div>

    </div>

    <div class="button-courbes">
        <!-- Boutons javascript -->
        <div class="tabs">
            <button class="tab-button active" onclick="ouvrirCourbe('temperature')">Température</button>
            <button class="tab-button" onclick="ouvrirCourbe('allumage')">Allumage</button>
            <button class="tab-button" onclick="ouvrirCourbe('courant')">Courant</button>
        </div>
    </div>

    <!-- Alerte -->
    <?php
    if ($temperature < SEUIL_MIN || $temperature > SEUIL_MAX) {

    // Vérifie si une alerte existe déjà pour cette mesure
    $check = $pdo->prepare("
        SELECT id FROM alertes
        WHERE mesure_id = ?
        AND envoyee = 0
    ");
    $check->execute([$ligne['id']]);
    $alerteExistante = $check->fetch(PDO::FETCH_ASSOC);

    // Si aucune alerte -> on la crée
    if (!$alerteExistante) {

        $type = ($temperature < SEUIL_MIN) 
            ? 'temperature_basse' 
            : 'temperature_haute';

        $insert = $pdo->prepare("
            INSERT INTO alertes (mesure_id, type_alerte, valeur, envoyee)
            VALUES (?, ?, ?, 0)
        ");

        $insert->execute([
            $ligne['id'],
            $type,
            $temperature
        ]);

        // Envoi du mail
        envoyerAlerteTemperature($temperature, SEUIL_MIN, SEUIL_MAX);

        // Marquer comme envoyée
        $pdo->prepare("
            UPDATE alertes 
            SET envoyee = 1 
            WHERE mesure_id = ?
        ")->execute([$ligne['id']]);
    }
    } ?>

    <!-- Options pour la courbe de température -->
    <div id="optionsCourbe" style="display:none; margin-top:20px;">
        <h3>Afficher une courbe</h3>

        <label>Plage temporelle :</label>
        <select id="typePlage" onchange="adapterDates()">
            <option value="jour">Jour</option>
            <option value="semaine">Semaine</option>
            <option value="custom">Personnalisée</option>
        </select>

        <br><br>

        <label>Début :</label>
        <input type="date" id="dateDebut">

        <label>Fin :</label>
        <input type="date" id="dateFin">

        <br><br>

        <button onclick="chargerCourbe()">Afficher la courbe</button>
    </div>

    <div style="margin-top:30px;">
        <canvas id="graphTemp" width="600" height="300"></canvas>
    </div>

    <!-- Commande d'allumage -->
    <div id="commandeAllumage" style="display:none; margin-top:20px;">
        <h3>Allumer l’actionneur</h3>

        <label>Temps d’allumage (secondes) :</label>
        <input type="number" id="tempsAllumage" min="1" placeholder="ex: 300">

        <br><br>

        <button onclick="envoyerCommandeAllumage()">Valider</button>
    </div>

    <div>
        <form action="logout.php" method="POST" style="text-align:right;">
            <button type="submit">Déconnexion</button>
        </form>
    </div>
</body>
</html>