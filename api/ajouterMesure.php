<?php
require "../connexion.php";

// Récupérer les données envoyées
$temperature = $_POST["temperature"] ?? null;
$temsion = $_POST["tension"] ?? null; // Trouver PK ???
$courant = $_POST["courant_secteur"] ?? null;

if ($temperature === null || $tension === null || $courant === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Données manquantes"
    ]);
    exit;
}

// Insertion dans la base
$sql = "INSERT INTO mesures_systeme (temperature, courant_secteur, date_mesure)
        VALUES (?, ?, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([$temperature, $humidite]);

echo json_encode([
    "status" => "success",
    "message" => "Mesure enregistrée"
]);