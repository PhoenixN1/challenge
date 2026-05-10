<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mini_project";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connexion échouée");
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
)");

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
    header("Location: index.php");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: index.php");
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
    header("Location: index.php");
}

$edit = false;
$name = "";
$email = "";
$id = 0;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
    $edit = true;
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Users List CRUD</title>
    <style>
        body {
            font-family: Arial;
            width: 700px;
            margin: 30px auto;
        }

        form {
            margin-bottom: 20px;
        }

        input {
            padding: 10px;
            margin: 5px;
            width: 200px;
        }

        button {
            padding: 10px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Mini Project: Users List</h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="text" name="name" placeholder="Name" required value="<?= $name ?>">
    <input type="email" name="email" placeholder="Email" required value="<?= $email ?>">

    <?php if ($edit): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td>
            <a href="?edit=<?= $row['id'] ?>">Edit</a>
            |
            <a href="?delete=<?= $row['id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
