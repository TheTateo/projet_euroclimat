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
    <p>
        Bienvenue sur la page d'administration client !
        <br>
        Utilisateur connecté : 
        <strong>
        <?php 
        if (isset($_SESSION['username'])) {
            echo htmlspecialchars($_SESSION['username']);
        } else {
            echo "Inconnu";
        }
        ?>
        </strong>
    </p>
    
    <!-- Affichage des données -->
    <div class="container-dashboard">

    <?php 
    $stmt_ligne = $pdo->query("SELECT id, etat_actionneur, temperature, courant_secteur, duree_allumage
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
                    <th>Température</th>
                    <td><?= htmlspecialchars($ligne['temperature']) ?> °C</td>
                </tr>

                <tr>
                    <th>Courant secteur</th>
                    <td><?= htmlspecialchars($ligne['courant_secteur']) ?> A</td>
                </tr>

                <tr>
                    <th>Durée restante d'allumage</th>
                    <td><?= htmlspecialchars($ligne['duree_allumage']) ?> s</td>
                </tr>
            </table>
        </div>

        <div class="droite">
            <h2>Historique des Alertes</h2>
            <?php
            $stmt_alerte = $pdo->query("SELECT valeur, date_alerte
                                FROM alertes
                                ORDER BY date_alerte ASC
                                LIMIT 5");
            $alerte = $stmt_alerte->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <table>
                <tr>
                    <th>Valeur</th>
                    <th>Heure / Date</th>
                </tr>

                <?php while ($alerte = $stmt_alerte->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?= htmlspecialchars($alerte['valeur']) ?> °C</td>
                    <td>
                        <?= (new DateTime($alerte['date_alerte']))->format('H:i - d/m/Y') ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <!-- Container pour l'allumage de l'interrupteur-->
    <div class="container-allumage">
        <h2>Durée d'allumage</h2>
        <label for="tempsAllumage">Temps (secondes) :</label>
        <input type="number" id="tempsAllumage" min="1" placeholder="ex: 30">

        <button class="btn-allumage" onclick="envoyerCommandeAllumage()">Allumage</button>
    </div>

    <div class="container-courbes">
        <h2>Affichage des courbes</h2>
        <!-- Boutons de sélection -->
        <div class="tabs">
            <button class="tabs-button" onclick="choisirCourbe(event, 'temperature')">Température</button>
            <button class="tabs-button" onclick="choisirCourbe(event, 'courant')">Courant</button>
        </div>

        <!-- Options de la courbe -->
        <div id="optionsCourbe" style="display:none; margin-top:20px;">

            <label>Plage temporelle :</label>
            <select id="typePlage" onchange="adapterDates()">
                <option value="jour">Jour</option>
                <option value="semaine">Semaine</option>
                <option value="custom">Personnalisée</option>
            </select>
            <br><br>

            <!-- Pour Jour -->
            <div id="jourContainer">
                <label>Jour :</label>
                <input type="date" id="dateJour">
            </div>

            <div id="periodeContainer" style="display:none;">
                <label>Début :</label>
                <input type="date" id="dateDebut">

                <label>Fin :</label>
                <input type="date" id="dateFin">
            </div>
        </div>

        <!-- Canvas de la courbe -->
        <div style="margin-top:30px;">
            <canvas id="graphTemp" width="600" height="300"></canvas>
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

    <!-- Deconnexion -->
    <div>
        <form action="logout.php" method="POST" style="text-align:right;">
            <button type="submit">Déconnexion</button>
        </form>
    </div>
</body>
</html>