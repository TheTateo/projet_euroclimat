<?php
require_once("connexion.php"); // doit contenir $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO demandes_inscription (username, mot_de_passe, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $email]);

        echo "Demande envoyée. En attente de validation par un administrateur.";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div>
        <h2>Créer un compte</h2>
        <form method="POST">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required><br>

            <label>Email :</label>
            <input type="email" name="email" required><br>

            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" required><br>

            <button type="submit">Envoyer la demande</button>
        </form>
        <a href="index.html">Retour</a>
    </div>
</body>
<br>