<?php
$conn = new mysqli("localhost", "root", "", "ecole");

if ($conn->connect_error) {
    die("Connexion échouée");
}

$sql = "SELECT * FROM etudiants";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #ddd;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['id']."</td>
                    <td>".$row['nom']."</td>
                    <td>".$row['prenom']."</td>
                    <td>".$row['email']."</td>
                  </tr>";
        }
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
