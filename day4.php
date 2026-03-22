<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $age = $_POST['age'];
    $ville = $_POST['ville'];

    header("Location: ?nom=$nom&age=$age&ville=$ville");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Challenge Variables</title>
</head>
<body>

<h2>Entrer vos informations</h2>

<form method="POST">
    Nom: <input type="text" name="nom" required><br><br>
    Age: <input type="number" name="age" required><br><br>
    Ville: <input type="text" name="ville" required><br><br>
    <button type="submit">Envoyer</button>
</form>

<hr>

<?php
if (isset($_GET['nom']) && isset($_GET['age']) && isset($_GET['ville'])) {
    $nom = htmlspecialchars($_GET['nom']);
    $age = htmlspecialchars($_GET['age']);
    $ville = htmlspecialchars($_GET['ville']);

    echo "<h3>Résultat :</h3>";
    echo "Nom : " . $nom . "<br>";
    echo "Age : " . $age . "<br>";
    echo "Ville : " . $ville . "<br>";
    echo "Dans 5 ans, vous aurez : " . ($age + 5) . " ans";
}
?>

</body>
</html>