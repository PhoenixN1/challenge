<?php

$host = "localhost";
$dbname = "nom_base";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

echo "Connexion réussie";

?>
<?php

$host = "localhost";
$dbname = "nom_base";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie";
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

?>
