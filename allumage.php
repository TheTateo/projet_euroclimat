<?php
require 'connexion.php';
session_start();
header("Content-Type: application/json");

// Récupération de la durée
$data = json_decode(file_get_contents("php://input"), true);
$duree = intval($data["duree"]);

// Récupération de l'utilisateur
$utilisateurId = $_SESSION['id'] ?? 0;
if ($duree <= 0 || $duree > 86400 || $utilisateurId <= 0) {
    echo json_encode(["message" => "Durée ou utilisateur invalide"]);
    exit;
}

// Insertion dans la base avec l'utilisateur
$sql = "
    INSERT INTO commandes_actionneur (duree_allumage, utilisateur_id, date_commande)
    VALUES (:duree, :utilisateur_id, NOW())
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "duree" => $duree,
    "utilisateur_id" => $utilisateurId
]);

echo json_encode(["message" => "Actionneur allumé pour $duree secondes"]);