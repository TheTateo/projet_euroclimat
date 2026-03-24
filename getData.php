<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require 'connexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$dateDebut = ($data['dateDebut'] ?? '') . " 00:00:00";
$dateFin   = ($data['dateFin'] ?? '') . " 23:59:59";
$type      = $data['type'] ?? 'temperature';

$stmt = $pdo->prepare("
    SELECT date_j, heure, temperature, courant_secteur
    FROM mesures_systeme
    WHERE CONCAT(date_j, ' ', heure) BETWEEN ? AND ?
    ORDER BY date_j, heure ASC
");

$stmt->execute([$dateDebut, $dateFin]);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultats);

error_log("Type reçu : " . $type);
exit;