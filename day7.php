<?php
$result = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $a = isset($_POST["a"]) ? (float)$_POST["a"] : 0;
    $b = isset($_POST["b"]) ? (float)$_POST["b"] : 0;
    $op = isset($_POST["op"]) ? $_POST["op"] : "";

    if ($op === "+") {
        $result = $a + $b;
    } elseif ($op === "-") {
        $result = $a - $b;
    } elseif ($op === "*") {
        $result = $a * $b;
    } elseif ($op === "/") {
        $result = $b != 0 ? $a / $b : "Erreur";
    } elseif ($op === "%") {
        $result = $b != 0 ? $a % $b : "Erreur";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Operators PHP</title>
<link rel="stylesheet" href="daay7.css">
</head>
<body>

<div class="container">
    <form method="POST">
        <input type="number" name="a" placeholder="Nombre 1" required>
        <input type="number" name="b" placeholder="Nombre 2" required>
        <select name="op">
            <option value="+">Addition</option>
            <option value="-">Soustraction</option>
            <option value="*">Multiplication</option>
            <option value="/">Division</option>
            <option value="%">Modulo</option>
        </select>
        <button type="submit">Calculer</button>
    </form>

    <div class="result">
        <?php echo $result !== "" ? $result : ""; ?>
    </div>
</div>

</body>
</html>