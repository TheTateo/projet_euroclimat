<?php
session_start();
require_once("connexion.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé");
}

if (isset($_GET['valider'])) {

    $id = intval($_GET['valider']);

    $stmt = $pdo->prepare("SELECT * FROM demandes_inscription WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $insert = $pdo->prepare("INSERT INTO utilisateurs (username, mot_de_passe, email, role) VALUES (?, ?, ?, 'user')");
        $insert->execute([$user['username'], $user['mot_de_passe'], $user['email']]);

        $delete = $pdo->prepare("DELETE FROM demandes_inscription WHERE id = ?");
        $delete->execute([$id]);

        echo "Utilisateur validé avec succès.<br><br>";
    }
}

$stmt = $pdo->query("SELECT * FROM demandes_inscription");

echo "<h2>Demandes en attente</h2>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo htmlspecialchars($row['username']) . " - " . htmlspecialchars($row['email']);
    echo " <a href='?valider=" . $row['id'] . "'>Valider</a><br><br>";
}
?>
