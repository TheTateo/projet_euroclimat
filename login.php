<?php
require 'connexion.php';
session_start();

$username = $_POST['username'] ?? '';
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

// Sécurité minimale
$username = trim($username);

$stmt = $pdo->prepare("
    SELECT id, username, mot_de_passe, role
    FROM utilisateurs
    WHERE username = :username
");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {

    // Protection contre la fixation de session
    session_regenerate_id(true);

    $_SESSION['user'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    header("Location: suivi.php"); // ou ta page principale
    exit;

} else {
    echo "Identifiants incorrects";
}
