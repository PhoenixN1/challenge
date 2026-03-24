<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jour 6 - Switch</title>
</head>
<body>

<form method="POST">
    <label>Choisir un jour (1-7):</label>
    <input type="number" name="jour" min="1" max="7" required>
    <button type="submit">Envoyer</button>
</form>

<?php
$resultat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jour = isset($_POST["jour"]) ? (int) $_POST["jour"] : 0;

    switch ($jour) {
        case 1:
            $resultat = "Lundi";
            break;
        case 2:
            $resultat = "Mardi";
            break;
        case 3:
            $resultat = "Mercredi";
            break;
        case 4:
            $resultat = "Jeudi";
            break;
        case 5:
            $resultat = "Vendredi";
            break;
        case 6:
            $resultat = "Samedi";
            break;
        case 7:
            $resultat = "Dimanche";
            break;
        default:
            $resultat = "Jour invalide";
    }

    echo "<h3>Jour choisi: " . $resultat . "</h3>";
}
?>

</body>
</html>