<?php
$fruits = ["pomme", "banane", "cerise", "datte", "abricot"];
$nombres = [3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5];
$mixte = ["nom" => "Ali", "age" => 25, "ville" => "Paris"];

$longueur = count($fruits);
$fruits_upper = array_map('strtoupper', $fruits);
$filtered = array_filter($nombres, function($n) { return $n > 4; });
$somme = array_sum($nombres);
$produit = array_product([1, 2, 3, 4, 5]);

$fruits_sorted = $fruits;
sort($fruits_sorted);
$nombres_desc = $nombres;
rsort($nombres_desc);

$cles = array_keys($mixte);
$valeurs = array_values($mixte);
$existe = in_array("banane", $fruits);
$position = array_search("cerise", $fruits);

$fruits_push = $fruits;
array_push($fruits_push, "mangue", "kiwi");
$fruits_pop = $fruits_push;
$dernier = array_pop($fruits_pop);
$fruits_shift = $fruits;
$premier = array_shift($fruits_shift);
$fruits_unshift = $fruits;
array_unshift($fruits_unshift, "ananas");

$tranche = array_slice($nombres, 2, 4);
$a = [1, 2, 3];
$b = [4, 5, 6];
$fusion = array_merge($a, $b);
$combines = array_combine(["a", "b", "c"], [10, 20, 30]);
$unique = array_unique($nombres);
$inverse = array_reverse($fruits);
$total = array_reduce($nombres, function($carry, $item) { return $carry + $item; }, 0);
$matrice = [[1, 2], [3, 4], [5, 6]];
$aplati = array_merge(...$matrice);
$chunk = array_chunk($nombres, 3);
$carre = array_map(function($n) { return $n * $n; }, $a);
$pairs = array_filter($nombres, function($n) { return $n % 2 === 0; });
$notes = ["Alice" => 15, "Bob" => 12, "Charlie" => 18];
arsort($notes);
$rempli = array_fill(0, 5, "vide");
$compte = array_count_values($nombres);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Array Functions</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Segoe UI', sans-serif;
    background: #0f172a;
    color: #e2e8f0;
    min-height: 100vh;
    padding: 40px 20px;
  }

  h1 {
    text-align: center;
    font-size: 2rem;
    color: #7dd3fc;
    margin-bottom: 10px;
    letter-spacing: 2px;
  }

  .subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 40px;
    font-size: 0.95rem;
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    max-width: 1300px;
    margin: 0 auto;
  }

  .card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 12px;
    padding: 20px;
    transition: transform 0.2s, border-color 0.2s;
  }

  .card:hover {
    transform: translateY(-3px);
    border-color: #7dd3fc;
  }

  .card-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #7dd3fc;
    margin-bottom: 6px;
  }

  .card-func {
    font-size: 0.8rem;
    color: #f472b6;
    font-family: monospace;
    margin-bottom: 12px;
  }

  .badge-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .badge {
    background: #0f172a;
    border: 1px solid #475569;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 0.78rem;
    font-family: monospace;
    color: #a3e635;
  }

  .badge.num { color: #fb923c; }
  .badge.str { color: #a3e635; }
  .badge.key { color: #c084fc; }
  .badge.val { color: #7dd3fc; }
  .badge.pair { color: #34d399; }

  .stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #1e293b;
  }

  .stat-row:last-child { border-bottom: none; }

  .stat-label { color: #94a3b8; font-size: 0.82rem; }

  .stat-value {
    font-family: monospace;
    font-size: 0.85rem;
    color: #fb923c;
    font-weight: 700;
  }

  .bool-true  { color: #4ade80; font-weight: 700; font-family: monospace; }
  .bool-false { color: #f87171; font-weight: 700; font-family: monospace; }

  .kv-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #0f172a;
    font-size: 0.82rem;
  }

  .kv-key   { color: #c084fc; font-family: monospace; }
  .kv-value { color: #fb923c; font-family: monospace; }

  .chunk-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 4px;
  }

  .chunk-box {
    background: #0f172a;
    border: 1px dashed #475569;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.78rem;
    font-family: monospace;
    color: #7dd3fc;
  }
</style>
</head>
<body>

<h1>PHP Array Functions</h1>
<p class="subtitle">More Arrays — Fonctions principales illustrees</p>

<div class="grid">

  <div class="card">
    <div class="card-title">Tableau Initial</div>
    <div class="card-func">$fruits</div>
    <div class="badge-list">
      <?php foreach ($fruits as $f): ?>
        <span class="badge str"><?= $f ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">count / sum / product</div>
    <div class="card-func">count() | array_sum() | array_product()</div>
    <div class="stat-row"><span class="stat-label">count($fruits)</span><span class="stat-value"><?= $longueur ?></span></div>
    <div class="stat-row"><span class="stat-label">array_sum($nombres)</span><span class="stat-value"><?= $somme ?></span></div>
    <div class="stat-row"><span class="stat-label">array_product([1..5])</span><span class="stat-value"><?= $produit ?></span></div>
    <div class="stat-row"><span class="stat-label">array_reduce()</span><span class="stat-value"><?= $total ?></span></div>
  </div>

  <div class="card">
    <div class="card-title">array_map — strtoupper</div>
    <div class="card-func">array_map('strtoupper', $fruits)</div>
    <div class="badge-list">
      <?php foreach ($fruits_upper as $f): ?>
        <span class="badge str"><?= $f ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_map — carres</div>
    <div class="card-func">array_map(fn => n*n, [1,2,3])</div>
    <div class="badge-list">
      <?php foreach ($carre as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_filter — n > 4</div>
    <div class="card-func">array_filter($nombres, fn => n > 4)</div>
    <div class="badge-list">
      <?php foreach ($filtered as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_filter — pairs</div>
    <div class="card-func">array_filter($nombres, fn => n % 2 == 0)</div>
    <div class="badge-list">
      <?php foreach ($pairs as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">sort / rsort</div>
    <div class="card-func">sort() | rsort()</div>
    <div class="card-func" style="color:#94a3b8; margin-bottom:6px;">Croissant</div>
    <div class="badge-list" style="margin-bottom:10px;">
      <?php foreach ($fruits_sorted as $f): ?>
        <span class="badge str"><?= $f ?></span>
      <?php endforeach; ?>
    </div>
    <div class="card-func" style="color:#94a3b8; margin-bottom:6px;">Decroissant</div>
    <div class="badge-list">
      <?php foreach ($nombres_desc as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">arsort — tableau associatif</div>
    <div class="card-func">arsort($notes)</div>
    <?php foreach ($notes as $nom => $note): ?>
      <div class="kv-row">
        <span class="kv-key"><?= $nom ?></span>
        <span class="kv-value"><?= $note ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <div class="card-title">array_keys / array_values</div>
    <div class="card-func">array_keys() | array_values()</div>
    <div class="card-func" style="color:#94a3b8; margin-bottom:6px;">Cles</div>
    <div class="badge-list" style="margin-bottom:10px;">
      <?php foreach ($cles as $k): ?>
        <span class="badge key"><?= $k ?></span>
      <?php endforeach; ?>
    </div>
    <div class="card-func" style="color:#94a3b8; margin-bottom:6px;">Valeurs</div>
    <div class="badge-list">
      <?php foreach ($valeurs as $v): ?>
        <span class="badge val"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">in_array / array_search</div>
    <div class="card-func">in_array() | array_search()</div>
    <div class="stat-row">
      <span class="stat-label">in_array("banane")</span>
      <span class="<?= $existe ? 'bool-true' : 'bool-false' ?>"><?= $existe ? 'true' : 'false' ?></span>
    </div>
    <div class="stat-row">
      <span class="stat-label">array_search("cerise")</span>
      <span class="stat-value"><?= $position !== false ? $position : 'false' ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-title">push / pop / shift / unshift</div>
    <div class="card-func">array_push() | array_pop() | array_shift() | array_unshift()</div>
    <div class="stat-row"><span class="stat-label">array_pop()</span><span class="stat-value"><?= $dernier ?></span></div>
    <div class="stat-row"><span class="stat-label">array_shift()</span><span class="stat-value"><?= $premier ?></span></div>
    <div style="margin-top:10px;">
      <div class="card-func" style="color:#94a3b8; margin-bottom:6px;">Apres unshift("ananas")</div>
      <div class="badge-list">
        <?php foreach ($fruits_unshift as $f): ?>
          <span class="badge str"><?= $f ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_slice</div>
    <div class="card-func">array_slice($nombres, 2, 4)</div>
    <div class="badge-list">
      <?php foreach ($tranche as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_merge</div>
    <div class="card-func">array_merge([1,2,3], [4,5,6])</div>
    <div class="badge-list">
      <?php foreach ($fusion as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_combine</div>
    <div class="card-func">array_combine(["a","b","c"], [10,20,30])</div>
    <?php foreach ($combines as $k => $v): ?>
      <div class="kv-row">
        <span class="kv-key">"<?= $k ?>"</span>
        <span class="kv-value"><?= $v ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <div class="card-title">array_unique</div>
    <div class="card-func">array_unique($nombres)</div>
    <div class="badge-list">
      <?php foreach ($unique as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_reverse</div>
    <div class="card-func">array_reverse($fruits)</div>
    <div class="badge-list">
      <?php foreach ($inverse as $f): ?>
        <span class="badge str"><?= $f ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_chunk</div>
    <div class="card-func">array_chunk($nombres, 3)</div>
    <div class="chunk-group">
      <?php foreach ($chunk as $i => $groupe): ?>
        <div class="chunk-box">
          [<?= $i ?>] <?= implode(', ', $groupe) ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_fill</div>
    <div class="card-func">array_fill(0, 5, "vide")</div>
    <div class="badge-list">
      <?php foreach ($rempli as $v): ?>
        <span class="badge val"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">array_count_values</div>
    <div class="card-func">array_count_values($nombres)</div>
    <?php foreach ($compte as $val => $cnt): ?>
      <div class="kv-row">
        <span class="kv-key">valeur <?= $val ?></span>
        <span class="kv-value"><?= $cnt ?>x</span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <div class="card-title">array_merge (matrice aplatie)</div>
    <div class="card-func">array_merge(...[[1,2],[3,4],[5,6]])</div>
    <div class="badge-list">
      <?php foreach ($aplati as $v): ?>
        <span class="badge num"><?= $v ?></span>
      <?php endforeach; ?>
    </div>
  </div>

</div>
</body>
</html>