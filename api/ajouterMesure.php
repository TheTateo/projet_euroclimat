<?php
require "../connexion.php";

// Récupérer les données envoyées
$temperature = $_POST["temperature"] ?? null;
$humidite = $_POST["humidite"] ?? null;

if ($temperature === null || $humidite === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Données manquantes"
    ]);
    exit;
}

// Insertion dans la base
$sql = "INSERT INTO mesures (temperature, humidite, date_mesure)
        VALUES (?, ?, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([$temperature, $humidite]);

echo json_encode([
    "status" => "success",
    "message" => "Mesure enregistrée"
]);
?>