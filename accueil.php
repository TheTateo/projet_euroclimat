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

    <div>
        <form action="logout.php" method="POST" style="text-align:right;">
            <button type="submit">Déconnexion</button>
        </form>
    </div>
    
    <!-- Affichage des données -->
    <div>
        <?php 
        // va chercher le dernière enregistrement de la base de données
        $stmt_ligne = $pdo->query("SELECT id, etat_actionneur, temperature, duree_allumage
                                        FROM mesures_systeme
                                        ORDER BY id DESC
                                        LIMIT 1
                                    ");
        $ligne = $stmt_ligne->fetch(PDO::FETCH_ASSOC);

        // Evite les erreurs
        if ($ligne) {
            $temperature = $ligne['temperature'];
        } else {
            $temperature = 0; // valeur par défaut
        }
        ?>

        <!-- Tableau des valeurs courantes à afficher pour les utilisateurs -->
        <table border="1">
            <tr>
                <th>Etat de l'interrupteur</th>
                <td> <?= isset($ligne['etat_actionneur']) && $ligne['etat_actionneur'] == 1 ? "Allumé" : "Éteint" ?></td>
            </tr>
            <tr>
                <th>Température actuelle</th>
                <td> <?= htmlspecialchars($ligne['temperature']) ?> °C</td>
            </tr>
            <tr>
                <th>Durée restante d'allumage</th>
                <td><?= htmlspecialchars($ligne['duree_allumage']) ?> s</td>
            </tr>
        </table>
    </div>

    <!-- Alerte -->
    <?php
    if ($temperature < $SEUIL_MIN || $temperature > $SEUIL_MAX) {

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

        $type = ($temperature < $SEUIL_MIN) 
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
        envoyerAlerteTemperature($temperature, $SEUIL_MIN, $SEUIL_MAX);

        // Marquer comme envoyée
        $pdo->prepare("
            UPDATE alertes 
            SET envoyee = 1 
            WHERE mesure_id = ?
        ")->execute([$ligne['id']]);
    }
    } ?>
    <p style="color:red; font-weight:bold; margin-top:15px;">
        ⚠️ Température hors seuil !
    </p>
    <?php endif; ?>

    <!-- Boutons pour demander des données -->
    <div>
        <button onclick="demanderCourbes()"> Température</button>
        <button onclick="demanderDureeAllumage()"> Allumage</button>
        <button onclick="demanderCourant()"> Courant</button>
    </div>

    <!-- Options pour la courbe de température -->
    <div id="optionsCourbe" style="display:none; margin-top:20px;">
        <h3>Courbe de température</h3>

        <label>Plage temporelle :</label>
        <select id="typePlage" onchange="adapterDates()">
            <option value="jour">Jour</option>
            <option value="semaine">Semaine</option>
            <option value="perso">Personnalisée</option>
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



    <!-- Fonctionnalité supplémentaire
    <h3>Tableau des Livres et Auteurs</h3>
    <table border="1">
        <tr>
            <th>Titre</th>
            <th>Année de publication</th>
            <th>Nom de l'auteur</th>
            <th>Prénom de l'auteur</th>
        </tr>

        <?php
        // Boucle sur le maximum des deux tableaux pour éviter les erreurs
        $max = max(count($livres), count($auteurs));
        for ($i = 0; $i < $max; $i++):
        ?>
            <tr>
                <td><?= isset($livres[$i]['titre']) ? htmlspecialchars($livres[$i]['titre']) : '' ?></td>
                <td><?= isset($livres[$i]['annee_publication']) ? htmlspecialchars($livres[$i]['annee_publication']) : '' ?></td>
                <td><?= isset($auteurs[$i]['nom']) ? htmlspecialchars($auteurs[$i]['nom']) : '' ?></td>
                <td><?= isset($auteurs[$i]['prenom']) ? htmlspecialchars($auteurs[$i]['prenom']) : '' ?></td>
            </tr>
        <?php endfor; ?>
    </table>
    -->
</body>
</html>