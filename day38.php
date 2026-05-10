<?php
$conn = new mysqli("localhost", "root", "", "school");

if ($conn->connect_error) {
    die("Connexion échouée");
}

if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age = $_POST['age'];
    $email = $_POST['email'];

    $sql = "INSERT INTO etudiants (nom, prenom, age, email) 
            VALUES ('$nom', '$prenom', '$age', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Données ajoutées avec succès";
    } else {
        echo "Erreur";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Étudiant</title>
</head>
<body>

<form method="POST">
    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" required><br><br>

    <label>Âge :</label><br>
    <input type="number" name="age" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" required><br><br>

    <button type="submit" name="submit">Ajouter</button>
</form>

</body>
</html>
