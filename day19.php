<?php
$chaine   = "Bonjour le monde";
$chercher = "le";
$liste    = "pomme,banane,orange";

$longueur = strlen($chaine);
$position = strpos($chaine, $chercher);
$tableau  = explode(",", $liste);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fonctions PHP - Strings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 700px;
      margin: 40px auto;
      padding: 0 20px;
      background: #f5f5f5;
      color: #333;
    }
    h1 {
      font-size: 22px;
      margin-bottom: 30px;
      color: #222;
    }
    .card {
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px 24px;
      margin-bottom: 20px;
    }
    .card h2 {
      font-size: 16px;
      color: #534AB7;
      margin-bottom: 12px;
    }
    .result {
      background: #f0fdf7;
      border-left: 3px solid #1D9E75;
      border-radius: 0 6px 6px 0;
      padding: 10px 14px;
      font-size: 14px;
      font-family: monospace;
      color: #085041;
    }
    ul {
      list-style: none;
      padding: 0;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    ul li {
      background: #E1F5EE;
      color: #085041;
      padding: 4px 12px;
      border-radius: 6px;
      font-family: monospace;
      font-size: 13px;
    }
  </style>
</head>
<body>

  <h1>Fonctions PHP sur les chaînes</h1>

  <div class="card">
    <h2>strlen</h2>
    <div class="result">
      strlen("<?php echo $chaine; ?>") = <?php echo $longueur; ?>
    </div>
  </div>

  <div class="card">
    <h2>strpos</h2>
    <div class="result">
      <?php
        if ($position !== false) {
          echo 'strpos("' . $chaine . '", "' . $chercher . '") = ' . $position;
        } else {
          echo "Sous-chaîne non trouvée.";
        }
      ?>
    </div>
  </div>

  <div class="card">
    <h2>explode</h2>
    <ul>
      <?php
        foreach ($tableau as $index => $item) {
          echo "<li>[" . $index . "] " . $item . "</li>";
        }
      ?>
    </ul>
  </div>

</body>
</html>
