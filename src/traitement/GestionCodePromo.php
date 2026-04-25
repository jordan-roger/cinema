<?php

function appliquerCodePromo($codeSaisi, $prixInitial) {

    $codesPromo = [
        "CODE10" => [
            "reduction" => 10,
            "actif" => true,
            "date_expiration" => "2026-12-31"
        ],
        "CINE20" => [
            "reduction" => 20,
            "actif" => true,
            "date_expiration" => "2026-06-30"
        ],
        "VIP5" => [
            "reduction" => 5,
            "actif" => false,
            "date_expiration" => "2026-12-31"
        ]
    ];

    $codeSaisi = strtoupper(trim($codeSaisi)); // normalisation

    // Vérification de l'existence du code
    if (!array_key_exists($codeSaisi, $codesPromo)) {
        return [
            "success" => false,
            "message" => "Ce code promo n'existe pas.",
            "prix_final" => $prixInitial
        ];
    }

    $promo = $codesPromo[$codeSaisi];


    if (!$promo["actif"]) {
        return [
            "success" => false,
            "message" => "Ce code promo est désactivé.",
            "prix_final" => $prixInitial
        ];
    }


    $dateActuelle = date("Y-m-d");
    if ($dateActuelle > $promo["date_expiration"]) {
        return [
            "success" => false,
            "message" => "Ce code promo est expiré.",
            "prix_final" => $prixInitial
        ];
    }

    $reduction = ($prixInitial * $promo["reduction"]) / 100;
    $prixFinal = $prixInitial - $reduction;

    return [
        "success" => true,
        "message" => "Code promo appliqué : -" . $promo["reduction"] . "% !",
        "prix_final" => $prixFinal
    ];
}

?>
