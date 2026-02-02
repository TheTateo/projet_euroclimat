<?php
require 'connexion.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$duree = intval($data["duree"]);

// Sécurité basique
if ($duree <= 0 || $duree > 86400) {
    echo json_encode(["message" => "Durée invalide"]);
    exit;
}

/*
  ICI selon ton système :
  - soit tu écris juste en base
  - soit tu déclenches aussi un GPIO / API / script
*/

// Exemple : insertion en base
$sql = "
    INSERT INTO commandes_actionneur (duree_allumage, date_commande)
    VALUES (:duree, NOW())
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["duree" => $duree]);

echo json_encode(["message" => "Actionneur allumé pour $duree secondes"]);
