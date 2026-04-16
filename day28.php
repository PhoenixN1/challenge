<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Site</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        nav { background: #34495e; padding: 10px; text-align: center; }
        nav a { color: white; text-decoration: none; margin: 0 15px; }
        nav a:hover { text-decoration: underline; }
        main { padding: 30px; min-height: 400px; }
        footer { background: #2c3e50; color: white; text-align: center; padding: 15px; }
    </style>
</head>
<body>
<header>
    <h1>Mon Site Web</h1>
</header>
<nav>
    <a href="index.php">Accueil</a>
    <a href="index.php">Services</a>
    <a href="index.php">Portfolio</a>
    <a href="index.php">Contact</a>
</nav>
<main>
    <h2>Bienvenue sur mon site</h2>
    <p>Ceci est le contenu principal de la page.</p>
</main>
<footer>
    <p>&copy; <?php echo date('Y'); ?> Mon Site. Tous droits réservés.</p>
</footer>
</body>
</html>
