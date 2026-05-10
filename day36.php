<?php
$conn = new mysqli("localhost", "root", "", "school");

if ($conn->connect_error) {
    die("Connexion échouée");
}

$sql = "SELECT * FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants</title>
</head>
<body>

<h2>Liste des étudiants</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div>
            <p>ID: <?php echo $row['id']; ?></p>
            <p>Nom: <?php echo $row['name']; ?></p>
            <p>Email: <?php echo $row['email']; ?></p>
            <hr>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Aucun étudiant trouvé</p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
