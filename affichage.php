<?php
require 'connexion.php';

// Récupérer les livres
//$stmt_livres = $pdo->query("SELECT date_j, heure, temperature, courant_secteur, etat_actionneur, duree_allumage FROM projet_bdd");
//$livres = $stmt_livres->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de l'Interrupteur et de la Température</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Suivi de l'Interrupteur et de la Température</h1>
    <p>Bienvenue sur la page d'administration client usager !</p>

    <!-- Affichage des données -->
    <div>
        <?php 
        // va chercher le dernière enregistrement de la base de données
        $stmt_ligne = $pdo->query("SELECT etat_actionneur, temperature, duree_allumage 
                                        FROM mesures_systeme
                                        ORDER BY id DESC
                                        LIMIT 1");
        $ligne = $stmt_ligne->fetch(PDO::FETCH_ASSOC);
        ?>

        <!-- Tableau des valeurs courantes à afficher pour l'utilisateurs -->
        <table border="1">
            <tr>
                <th>Etat de l'interrupteur</th>
                <td> <?= ($ligne['etat_actionneur'] == 1) ? "Allumé" : "Éteint" ?></td>
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
</body>
</html>