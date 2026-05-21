<?php
session_start();

define('USER', 'admin');
define('PASS', 'secret123');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        if ($_POST['username'] === USER && $_POST['password'] === PASS) {
            $_SESSION['authenticated'] = true;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }

    if ($_POST['action'] === 'logout') {
        $_SESSION = [];
        session_destroy();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$authenticated = !empty($_SESSION['authenticated']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page protegee</title>
</head>
<body>

<?php if ($authenticated): ?>

    <h2>Tableau de bord</h2>
    <p>Bienvenue, vous etes connecte.</p>
    <form method="POST">
        <input type="hidden" name="action" value="logout">
        <button type="submit">Se deconnecter</button>
    </form>

<?php else: ?>

    <h2>Connexion</h2>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="hidden" name="action" value="login">
        <input type="text" name="username" placeholder="Utilisateur" required><br><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br><br>
        <button type="submit">Se connecter</button>
    </form>

<?php endif; ?>

</body>
</html>
