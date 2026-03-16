<?php
require 'connexion.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$sql = "
    SELECT 
        CONCAT(date_j,' ',heure) AS date_mesure,
        temperature
    FROM mesures_systeme
    WHERE date_j BETWEEN :debut AND :fin
    ORDER BY date_j, heure
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "debut" => $data["dateDebut"],
    "fin" => $data["dateFin"]
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));