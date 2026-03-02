<?php
session_start();
require_once("connexion.php"); // doit contenir $pdo

// Sécurité : admin uniquement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé");
}

/* ===========================
   TRAITEMENT VALIDATION
=========================== */

// Valider un utilisateur
if (isset($_POST['valider'])) {

    $id = intval($_POST['id']);

    $stmt = $pdo->prepare("SELECT * FROM demandes_creation WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // Insertion dans utilisateurs
        $insert = $pdo->prepare("INSERT INTO utilisateurs (username, mot_de_passe, email, role) VALUES (?, ?, ?, 'user')");
        $insert->execute([$user['username'], $user['mot_de_passe'], $user['email']]);

        // Suppression de la demande
        $delete = $pdo->prepare("DELETE FROM demandes_creation WHERE id = ?");
        $delete->execute([$id]);
    }
}

// Refuser un utilisateur
if (isset($_POST['refuser'])) {

    $id = intval($_POST['id']);

    $delete = $pdo->prepare("DELETE FROM demandes_creation WHERE id = ?");
    $delete->execute([$id]);
}

/* ===========================
   RÉCUPÉRATION DES DEMANDES
=========================== */

$stmt = $pdo->query("SELECT * FROM demandes_creation ORDER BY date_demande DESC");
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des comptes</title>
    <style>
        body { font-family: Arial; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .valider { background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .refuser { background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>

<h2>Demandes d_creation en attente</h2>

<?php if (count($demandes) > 0): ?>

<table>
    <tr>
        <th>ID</th>
        <th>Nom d'utilisateur</th>
        <th>Email</th>
        <th>Date de demande</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($demandes as $demande): ?>
        <tr>
            <td><?= htmlspecialchars($demande['id']) ?></td>
            <td><?= htmlspecialchars($demande['username']) ?></td>
            <td><?= htmlspecialchars($demande['email']) ?></td>
            <td><?= htmlspecialchars($demande['date_demande']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $demande['id'] ?>">
                    <button type="submit" name="valider" class="valider">Valider</button>
                </form>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $demande['id'] ?>">
                    <button type="submit" name="refuser" class="refuser">Refuser</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php else: ?>
    <p>Aucune demande en attente.</p>
<?php endif; ?>

<br>
<a href="logout.php">Déconnexion</a>

</body>
</html>
