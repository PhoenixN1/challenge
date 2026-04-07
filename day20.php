
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon Site</title>
  <!-- <link rel="stylesheet" href="style.css"> -->
</head>
<style>

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: sans-serif;
  font-size: 16px;
  color: #333;
  background: #f9f9f9;
}

header {
  background: #fff;
  border-bottom: 1px solid #ddd;
  padding: 1rem 2rem;
}

nav ul {
  list-style: none;
  display: flex;
  gap: 1.5rem;
}

nav a {
  text-decoration: none;
  color: #333;
}

nav a:hover {
  color: #5340d8;
}

main {
  max-width: 900px;
  margin: 2rem auto;
  padding: 0 1rem;
}

footer {
  text-align: center;
  padding: 1.5rem;
  color: #888;
  font-size: 14px;
  border-top: 1px solid #ddd;
  margin-top: 3rem;
}

</style>
<body>
  <header>
    <nav>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="#">À propos</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
  </header>
  <main>
      </main>
  <footer>
    <p><?php echo date('Y'); ?> — Mon Site</p>
  </footer>
</body>
</html>