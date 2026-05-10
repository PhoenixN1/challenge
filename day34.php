<?php
$conn = mysqli_connect("localhost", "root", "", "ecole");

if (!$conn) {
    die("Connexion échouée");
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age = $_POST['age'];

    $sql = "UPDATE etudiants SET nom='$nom', prenom='$prenom', age='$age' WHERE id='$id'";
    mysqli_query($conn, $sql);
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM etudiants WHERE id='$id'";
    mysqli_query($conn, $sql);
}
?>

<form method="POST">
    <input type="number" name="id" placeholder="ID" required><br><br>
    <input type="text" name="nom" placeholder="Nom"><br><br>
    <input type="text" name="prenom" placeholder="Prénom"><br><br>
    <input type="number" name="age" placeholder="Âge"><br><br>

    <button type="submit" name="update">UPDATE</button>
    <button type="submit" name="delete">DELETE</button>
</form>
