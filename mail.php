<?php

function envoyerAlerteTemperature($temperature, $seuilMin, $seuilMax)
{
    $to = "prof@lycee.fr";
    $subject = "⚠️ Alerte température – Projet BTS";

    $message =
        "Bonjour,\n\n" .
        "Une température élevé a été détectée.\n\n" .
        "Température mesurée : {$temperature} °C\n" .
        "Plage autorisée : {$seuilMin} °C à {$seuilMax} °C\n\n" .
        "Date : " . date("d/m/Y H:i");

    $headers = "From: projet-euroclimat@bts.local";

    return mail($to, $subject, $message, $headers);
}
