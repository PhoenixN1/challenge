<?php
$post_result = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom      = isset($_POST["nom"])      ? htmlspecialchars(trim($_POST["nom"]))      : "";
    $age      = isset($_POST["age"])      ? (int) $_POST["age"]                        : 0;
    $email    = isset($_POST["email"])    ? htmlspecialchars(trim($_POST["email"]))    : "";
    $langue   = isset($_POST["langue"])   ? htmlspecialchars($_POST["langue"])         : "";

    $message_bienvenue = "Bonjour, $nom !";
    $majeur            = ($age >= 18);
    $annee_naissance   = date("Y") - $age;
    $score             = 90.5;

    $post_result = [
        "nom"             => $nom,
        "age"             => $age,
        "email"           => $email,
        "langue"          => $langue,
        "message"         => $message_bienvenue,
        "majeur"          => $majeur ? "Oui ✔" : "Non ✘",
        "annee_naissance" => $annee_naissance,
        "score"           => $score,
        "type_nom"        => gettype($nom),
        "type_age"        => gettype($age),
        "type_majeur"     => gettype($majeur),
        "type_score"      => gettype($score),
    ];
}

$get_result = "";
if (!empty($_GET)) {
    $ville   = isset($_GET["ville"])   ? htmlspecialchars(trim($_GET["ville"]))   : "Inconnue";
    $metier  = isset($_GET["metier"])  ? htmlspecialchars(trim($_GET["metier"]))  : "Inconnu";
    $niveau  = isset($_GET["niveau"])  ? (int) $_GET["niveau"]                    : 1;

    $get_result = [
        "ville"      => $ville,
        "metier"     => $metier,
        "niveau"     => $niveau,
        "description"=> "Je suis $metier à $ville (Niveau $niveau)",
        "type_ville" => gettype($ville),
        "type_niveau"=> gettype($niveau),
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP 90-Day Challenge — Variables & GET/POST</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="day5.css">
</head>
<body>

<header>
  <div class="badge"> PHP 90-DAY CHALLENGE</div>
  <h1>Variables · GET &amp; POST</h1>
  <p>Envoyez des données, explorez les types et les superglobales PHP</p>
</header>

<div class="grid">

  <div class="card">
    <div class="card-header">
      <h2>Méthode POST</h2>
      <span>$_POST</span>
    </div>
    <div class="card-body">
      <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">

        <div class="form-group">
          <label for="nom">Nom complet</label>
          <input type="text" id="nom" name="nom" placeholder="ex: Ahmed Khalil"
                 value="<?= isset($post_result["nom"]) ? $post_result["nom"] : "" ?>">
        </div>

        <div class="form-group">
          <label for="age">Âge</label>
          <input type="number" id="age" name="age" placeholder="ex: 25" min="1" max="120"
                 value="<?= isset($post_result["age"]) ? $post_result["age"] : "" ?>">
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="ex: ahmed@email.com"
                 value="<?= isset($post_result["email"]) ? $post_result["email"] : "" ?>">
        </div>

        <div class="form-group">
          <label for="langue">Langue préférée</label>
          <select id="langue" name="langue">
            <option value="">-- Choisir --</option>
            <option value="PHP"        <?= (isset($post_result["langue"]) && $post_result["langue"]==="PHP")        ? "selected" : "" ?>>PHP</option>
            <option value="Python"     <?= (isset($post_result["langue"]) && $post_result["langue"]==="Python")     ? "selected" : "" ?>>Python</option>
            <option value="JavaScript" <?= (isset($post_result["langue"]) && $post_result["langue"]==="JavaScript") ? "selected" : "" ?>>JavaScript</option>
          </select>
        </div>

        <button type="submit" class="btn btn-post"> Envoyer en POST</button>
      </form>

      <?php if ($post_result): ?>
      <div class="result-box">
        <div class="result-header"> Données reçues via $_POST</div>
        <div class="result-body">
          <?php
          $labels = [
            "nom"             => ["Nom",              $post_result["type_nom"]],
            "age"             => ["Âge",              $post_result["type_age"]],
            "email"           => ["Email",            "string"],
            "langue"          => ["Langue",           "string"],
            "message"         => ["Message",          "string"],
            "majeur"          => ["Majeur ?",         $post_result["type_majeur"]],
            "annee_naissance" => ["Né(e) en",         "integer"],
            "score"           => ["Score (float)",    "float"],
          ];
          foreach ($labels as $key => [$label, $type]):
          ?>
          <div class="kv-row">
            <span class="kv-key">$<?= $label ?></span>
            <span class="kv-val"><?= htmlspecialchars((string)$post_result[$key]) ?></span>
            <span class="kv-type"><?= $type ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <!-- <div class="icon get-icon">🔗</div> -->
      <h2>Méthode GET</h2>
      <span>$_GET</span>
    </div>
    <div class="card-body">

      <div class="url-bar">
        <span class="method">GET</span>
        <span class="url-text">
          <?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>?ville=...&amp;metier=...&amp;niveau=...
        </span>
      </div>

      <form method="GET" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">

        <div class="form-group">
          <label for="ville">Ville</label>
          <input type="text" id="ville" name="ville" placeholder="ex: Casablanca"
                 value="<?= isset($get_result["ville"]) ? $get_result["ville"] : "" ?>">
        </div>

        <div class="form-group">
          <label for="metier">Métier</label>
          <input type="text" id="metier" name="metier" placeholder="ex: Développeur PHP"
                 value="<?= isset($get_result["metier"]) ? $get_result["metier"] : "" ?>">
        </div>

        <div class="form-group">
          <label for="niveau">Niveau (1–10)</label>
          <input type="number" id="niveau" name="niveau" placeholder="ex: 7" min="1" max="10"
                 value="<?= isset($get_result["niveau"]) ? $get_result["niveau"] : "" ?>">
        </div>

        <button type="submit" class="btn btn-get"> Envoyer en GET</button>
      </form>

      <?php if ($get_result): ?>
      <div class="result-box">
        <div class="result-header"> Données reçues via $_GET</div>
        <div class="result-body">
          <?php
          $glabels = [
            "ville"       => ["Ville",        $get_result["type_ville"]],
            "metier"      => ["Métier",        "string"],
            "niveau"      => ["Niveau",        $get_result["type_niveau"]],
            "description" => ["Description",  "string"],
          ];
          foreach ($glabels as $key => [$label, $type]):
          ?>
          <div class="kv-row">
            <span class="kv-key">$<?= $label ?></span>
            <span class="kv-val"><?= htmlspecialchars((string)$get_result[$key]) ?></span>
            <span class="kv-type"><?= $type ?></span>
          </div>
          <?php endforeach; ?>

          <div class="kv-row" style="margin-top:.5rem; border-top:1px solid var(--border);">
            <span class="kv-key" style="color:var(--muted)">URL générée</span>
            <span class="kv-val" style="font-size:.72rem; color:var(--muted); word-break:break-all;">
              <?= htmlspecialchars($_SERVER["REQUEST_URI"]) ?>
            </span>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="theory-card">
  <div class="card-header"> 
    <h2>Concepts clés illustrés</h2>
    <span>PHP Variables</span>
  </div>
  <div class="card-body" style="padding:1.4rem">
    <div class="code-block">
<span class="kw">$chaine</span>   = <span class="st">"Ahmed"</span>;
<span class="kw">$entier</span>   = <span class="vr">25</span>;
<span class="kw">$flottant</span> = <span class="vr">90.5</span>;
<span class="kw">$booleen</span>  = <span class="vr">true</span>;
<span class="kw">$tableau</span>  = [<span class="st">"PHP"</span>, <span class="st">"Web"</span>];
<span class="kw">$nul</span>      = <span class="vr">null</span>;

<span class="kw">if</span> (<span class="fn">$_SERVER</span>[<span class="st">"REQUEST_METHOD"</span>] === <span class="st">"POST"</span>) {
    <span class="kw">$nom</span> = <span class="fn">htmlspecialchars</span>(<span class="fn">trim</span>(<span class="fn">$_POST</span>[<span class="st">"nom"</span>]));
    <span class="kw">$age</span> = (<span class="kw">int</span>) <span class="fn">$_POST</span>[<span class="st">"age"</span>];
}

<span class="kw">$ville</span>  = <span class="fn">htmlspecialchars</span>(<span class="fn">$_GET</span>[<span class="st">"ville"</span>]  ?? <span class="st">"Inconnue"</span>);
<span class="kw">$niveau</span> = (<span class="kw">int</span>) (<span class="fn">$_GET</span>[<span class="st">"niveau"</span>] ?? <span class="vr">1</span>);

<span class="fn">gettype</span>(<span class="kw">$nom</span>);
<span class="fn">gettype</span>(<span class="kw">$age</span>);
<span class="fn">is_string</span>(<span class="kw">$nom</span>);
<span class="fn">is_int</span>(<span class="kw">$age</span>);
    </div>
  </div>
</div>

</body>
</html>