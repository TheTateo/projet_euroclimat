<?php
require 'connexion.php';

// Récupérer les livres
$stmt_livres = $pdo->query("SELECT heure, annee_publication FROM projet_bdd");
$livres = $stmt_livres->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des livres</title>
</head>
<body>
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