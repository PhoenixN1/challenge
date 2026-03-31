<?php

$note_passage = 10;

$etudiants = [
    ["id" => 1, "nom" => "Amine Benali",   "filiere" => "Informatique",  "notes" => [14, 18, 12, 16]],
    ["id" => 2, "nom" => "Sara El Fassi",  "filiere" => "Mathematiques", "notes" => [8,  9,  7,  10]],
    ["id" => 3, "nom" => "Youssef Karimi", "filiere" => "Physique",      "notes" => [15, 13, 17, 14]],
    ["id" => 4, "nom" => "Nadia Chraibi",  "filiere" => "Informatique",  "notes" => [6,  5,  8,   7]],
    ["id" => 5, "nom" => "Omar Tazi",      "filiere" => "Chimie",        "notes" => [11, 12, 10, 13]],
    ["id" => 6, "nom" => "Hind Moussaoui", "filiere" => "Physique",      "notes" => [9,  10, 11,  8]],
];

function calculerMoyenne(array $notes): float {
    $somme = 0;
    foreach ($notes as $note) {
        $somme += $note;
    }
    return round($somme / count($notes), 2);
}

function getMention(float $moyenne): array {
    if ($moyenne >= 16) {
        return ["label" => "Tres Bien",  "class" => "tb"];
    } elseif ($moyenne >= 14) {
        return ["label" => "Bien",       "class" => "b"];
    } elseif ($moyenne >= 12) {
        return ["label" => "Assez Bien", "class" => "ab"];
    } elseif ($moyenne >= 10) {
        return ["label" => "Passable",   "class" => "p"];
    } else {
        return ["label" => "Echec",      "class" => "ec"];
    }
}

function getStatut(float $moyenne, float $seuil): string {
    return ($moyenne >= $seuil) ? "Admis" : "Redoublant";
}

function statsPromotion(array $etudiants, float $seuil): array {
    $moyennes = [];
    $admis = 0;
    for ($i = 0; $i < count($etudiants); $i++) {
        $moy = calculerMoyenne($etudiants[$i]["notes"]);
        $moyennes[] = $moy;
        if ($moy >= $seuil) $admis++;
    }
    return [
        "meilleure"   => max($moyennes),
        "plus_basse"  => min($moyennes),
        "generale"    => round(array_sum($moyennes) / count($moyennes), 2),
        "admis"       => $admis,
        "redoublants" => count($etudiants) - $admis,
    ];
}

function filtrerParFiliere(array $etudiants, string $filiere): array {
    $resultat = [];
    foreach ($etudiants as $etudiant) {
        if ($etudiant["filiere"] === $filiere) {
            $resultat[] = $etudiant;
        }
    }
    return $resultat;
}