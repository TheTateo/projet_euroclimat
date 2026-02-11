<?php
require_once("connexion.php");

function envoyerAlerteTemperature($temperature, $seuilMin, $seuilMax)
{
    global $pdo; // on utilise la connexion PDO

    // Récupération des emails des utilisateurs
    $stmt = $pdo->prepare("SELECT email FROM utilisateurs WHERE role = 'user' ");
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$emails) {
        return false; // aucun destinataire
    }

    // On met tous les emails séparés par une virgule
    $to = implode(",", $emails);

    $subject = "Alerte température – Projet BTS";

    $message =
        "Bonjour,\n\n" .
        "Une température anormale a été détectée.\n\n" .
        "Température mesurée : {$temperature} °C\n" .
        "Plage autorisée : {$seuilMin} °C à {$seuilMax} °C\n\n" .
        "Date : " . date("d/m/Y H:i") . "\n\n" .
        "Système Euroclimat";

    $headers = "From: projet-euroclimat@bts.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
}
