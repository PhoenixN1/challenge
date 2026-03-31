<?php
require_once "day13mp.php";

$stats          = statsPromotion($etudiants, $note_passage);
$filieres       = array_unique(array_map(fn($e) => $e["filiere"], $etudiants));
sort($filieres);
$filiere_active = $_GET["filiere"] ?? "Toutes";
$liste          = ($filiere_active === "Toutes") ? $etudiants : filtrerParFiliere($etudiants, $filiere_active);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Etudiants</title>
    <link rel="stylesheet" href="day13mp.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Gestion des Etudiants</h1>
        <p>Annee universitaire 2024 / 2025</p>
    </div>
</header>

<div class="container">

    <div class="stats-grid">
        <div class="stat-card">
            <div class="value"><?= $stats["generale"] ?>/20</div>
            <div class="label">Moyenne generale</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats["meilleure"] ?></div>
            <div class="label">Meilleure moyenne</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats["plus_basse"] ?></div>
            <div class="label">Moyenne la plus basse</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats["admis"] ?></div>
            <div class="label">Admis</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats["redoublants"] ?></div>
            <div class="label">Redoublants</div>
        </div>
    </div>

    <div class="filter-bar">
        <a href="?filiere=Toutes" class="<?= $filiere_active === 'Toutes' ? 'active' : '' ?>">Toutes</a>
        <?php foreach ($filieres as $f): ?>
            <a href="?filiere=<?= urlencode($f) ?>" class="<?= $filiere_active === $f ? 'active' : '' ?>">
                <?= htmlspecialchars($f) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Filiere</th>
                <th>Notes</th>
                <th>Moyenne</th>
                <th>Mention</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($liste as $etudiant): ?>
                <?php
                    $moy     = calculerMoyenne($etudiant["notes"]);
                    $mention = getMention($moy);
                    $statut  = getStatut($moy, $note_passage);
                ?>
                <tr>
                    <td><?= $etudiant["id"] ?></td>
                    <td class="nom"><?= htmlspecialchars($etudiant["nom"]) ?></td>
                    <td class="filiere"><?= htmlspecialchars($etudiant["filiere"]) ?></td>
                    <td>
                        <div class="notes-list">
                            <?php foreach ($etudiant["notes"] as $n): ?>
                                <span class="note-badge"><?= $n ?></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="moyenne"><?= $moy ?></td>
                    <td>
                        <span class="mention <?= $mention['class'] ?>">
                            <?= $mention['label'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="statut <?= strtolower($statut) ?>">
                            <?= $statut ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<footer>
    <p>Mini Projet PHP — Variables, Arrays, Fonctions, Boucles, Conditions</p>
</footer>

</body>
</html>