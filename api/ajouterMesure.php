<?php

require "../connexion.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$temperature = $data["temperature"] ?? null;
$tension = $data["tension"] ?? null;
$courant = $data["courant"] ?? null;

// valeur par défaut si non envoyée
$duree_allumage = $data["duree_allumage"] ?? 0;

if ($temperature === null || $courant === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Données manquantes"
    ]);
    exit;
}

try {

    $sql = "INSERT INTO mesures_systeme
            (
                date_j,
                heure,
                temperature,
                tension,
                courant_secteur,
                duree_allumage
            )
            VALUES
            (
                CURDATE(),
                CURTIME(),
                ?,
                ?,
                ?,
                ?
            )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $temperature,
        $tension,
        $courant,
        $duree_allumage
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Mesure enregistrée"
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

}