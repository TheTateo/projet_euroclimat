<?php
require 'connexion.php';
session_start();

$email = $_POST['email'] ?? '';
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
    $_SESSION['user'] = $user['email'];
    header("Location: affichage.php");
    exit;
} else {
    echo "Identifiants incorrects";
}
