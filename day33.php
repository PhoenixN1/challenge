```php
<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed");
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo $row["id"] . " " . $row["name"] . "<br>";
    }
}

$conn->close();
?>
```
