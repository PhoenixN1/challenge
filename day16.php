<?php
$result = null;
$action = $_POST['action'] ?? '';

if ($action === 'search') {
    $items = ["pomme", "banane", "cerise", "datte", "abricot", "mangue", "kiwi", "fraise"];
    $query = strtolower(trim($_POST['query'] ?? ''));
    $result = array_filter($items, fn($i) => str_contains(strtolower($i), $query));
}

if ($action === 'sort') {
    $data  = array_map('trim', explode(',', $_POST['items'] ?? ''));
    $order = $_POST['order'] ?? 'asc';
    $order === 'asc' ? sort($data) : rsort($data);
    $result = $data;
}

if ($action === 'math') {
    $nums   = array_map('intval', explode(',', $_POST['numbers'] ?? '0'));
    $result = [
        'sum'     => array_sum($nums),
        'product' => array_product($nums),
        'max'     => max($nums),
        'min'     => min($nums),
        'count'   => count($nums),
        'average' => round(array_sum($nums) / count($nums), 2),
    ];
}

if ($action === 'filter') {
    $nums   = array_map('intval', explode(',', $_POST['numbers'] ?? '0'));
    $op     = $_POST['operator'] ?? 'gt';
    $val    = intval($_POST['value'] ?? 0);
    $result = array_filter($nums, function($n) use ($op, $val) {
        return match($op) {
            'gt'  => $n > $val,
            'lt'  => $n < $val,
            'gte' => $n >= $val,
            'lte' => $n <= $val,
            'eq'  => $n == $val,
            default => false,
        };
    });
}

if ($action === 'unique') {
    $data   = array_map('trim', explode(',', $_POST['items'] ?? ''));
    $before = count($data);
    $after  = array_unique($data);
    $result = ['before' => $before, 'after' => $after, 'removed' => $before - count($after)];
}

if ($action === 'reverse') {
    $data   = array_map('trim', explode(',', $_POST['items'] ?? ''));
    $result = array_reverse($data);
}

if ($action === 'chunk') {
    $data   = array_map('trim', explode(',', $_POST['items'] ?? ''));
    $size   = max(1, intval($_POST['size'] ?? 2));
    $result = array_chunk($data, $size);
}

if ($action === 'combine') {
    $keys = array_map('trim', explode(',', $_POST['keys']   ?? ''));
    $vals = array_map('trim', explode(',', $_POST['values'] ?? ''));
    $result = count($keys) === count($vals) ? array_combine($keys, $vals) : 'error';
}

if ($action === 'map') {
    $nums      = array_map('intval', explode(',', $_POST['numbers'] ?? '0'));
    $operation = $_POST['operation'] ?? 'square';
    $result = array_map(function($n) use ($operation) {
        return match($operation) {
            'square' => $n * $n,
            'double' => $n * 2,
            'triple' => $n * 3,
            'abs'    => abs($n),
            default  => $n,
        };
    }, $nums);
}

if ($action === 'slice') {
    $data   = array_map('trim', explode(',', $_POST['items']  ?? ''));
    $offset = intval($_POST['offset'] ?? 0);
    $length = intval($_POST['length'] ?? 3);
    $result = array_slice($data, $offset, $length);
}

if ($action === 'merge') {
    $arr1   = array_map('trim', explode(',', $_POST['array1'] ?? ''));
    $arr2   = array_map('trim', explode(',', $_POST['array2'] ?? ''));
    $arr3   = array_map('trim', explode(',', $_POST['array3'] ?? ''));
    $result = ['merged' => array_merge($arr1, $arr2, $arr3), 'arr1' => $arr1, 'arr2' => $arr2, 'arr3' => $arr3];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Forms POST</title>
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
    color: #a78bfa;
    margin-bottom: 8px;
    letter-spacing: 2px;
}

.subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 40px;
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

.card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 14px;
    padding: 24px;
    transition: border-color 0.2s, transform 0.2s;
}

.card:hover { border-color: #a78bfa; transform: translateY(-3px); }
.card.active { border-color: #4ade80; }

.card-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #a78bfa;
    margin-bottom: 4px;
}

.card-func {
    font-family: monospace;
    font-size: 0.78rem;
    color: #f472b6;
    margin-bottom: 16px;
}

.method-badge {
    display: inline-block;
    background: #7c3aed;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 1px;
    padding: 2px 8px;
    border-radius: 4px;
    margin-bottom: 12px;
}

label {
    display: block;
    font-size: 0.78rem;
    color: #94a3b8;
    margin-bottom: 5px;
    margin-top: 10px;
}

input[type="text"], select {
    width: 100%;
    background: #0f172a;
    border: 1px solid #475569;
    border-radius: 8px;
    color: #e2e8f0;
    padding: 9px 12px;
    font-size: 0.85rem;
    font-family: monospace;
    outline: none;
    transition: border-color 0.2s;
}

input[type="text"]:focus, select:focus { border-color: #a78bfa; }
select option { background: #1e293b; }

button {
    margin-top: 14px;
    width: 100%;
    background: #7c3aed;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 1px;
    transition: background 0.2s;
}

button:hover { background: #6d28d9; }

.result-box {
    margin-top: 16px;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 8px;
    padding: 14px;
}

.result-title {
    font-size: 0.68rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 10px;
}

.badge-list { display: flex; flex-wrap: wrap; gap: 6px; }

.badge {
    background: #1e293b;
    border: 1px solid #475569;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 0.78rem;
    font-family: monospace;
}

.badge.str  { color: #a3e635; }
.badge.num  { color: #fb923c; }
.badge.key  { color: #c084fc; }
.badge.val  { color: #7dd3fc; }

.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #1e293b;
    font-size: 0.82rem;
}

.stat-row:last-child { border-bottom: none; }
.stat-label { color: #94a3b8; }
.stat-value { font-family: monospace; color: #fb923c; font-weight: 700; }

.kv-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #1e293b;
    font-size: 0.82rem;
}

.kv-row:last-child { border-bottom: none; }
.kv-key   { color: #c084fc; font-family: monospace; }
.kv-value { color: #7dd3fc; font-family: monospace; }

.chunk-group { display: flex; gap: 8px; flex-wrap: wrap; }

.chunk-box {
    background: #1e293b;
    border: 1px dashed #475569;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.78rem;
    font-family: monospace;
    color: #7dd3fc;
}

.arr-group { display: flex; flex-direction: column; gap: 8px; }

.arr-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.arr-label {
    font-size: 0.72rem;
    color: #64748b;
    font-family: monospace;
    min-width: 50px;
}

.error { color: #f87171; font-size: 0.82rem; font-family: monospace; }

.divider {
    border: none;
    border-top: 1px solid #334155;
    margin: 10px 0;
}
</style>
</head>
<body>

<h1>PHP Array Functions</h1>
<p class="subtitle">Formulaires POST — Methode securisee</p>

<div class="grid">

  <div class="card <?= $action === 'search' ? 'active' : '' ?>">
    <div class="card-title">Recherche dans tableau</div>
    <div class="card-func">array_filter + str_contains</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="search">
      <label>Mot a rechercher</label>
      <input type="text" name="query" value="<?= htmlspecialchars($_POST['query'] ?? '') ?>" placeholder="ex: an">
      <button type="submit">Rechercher</button>
    </form>
    <?php if ($action === 'search'): ?>
    <div class="result-box">
      <div class="result-title">Resultats trouves</div>
      <div class="badge-list">
        <?php if (empty($result)): ?>
          <span class="error">Aucun resultat</span>
        <?php else: foreach ($result as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'sort' ? 'active' : '' ?>">
    <div class="card-title">Tri de tableau</div>
    <div class="card-func">sort() | rsort()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="sort">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_POST['items'] ?? '') ?>" placeholder="ex: banane,pomme,cerise">
      <label>Ordre</label>
      <select name="order">
        <option value="asc"  <?= ($_POST['order'] ?? '') === 'asc'  ? 'selected' : '' ?>>Croissant (sort)</option>
        <option value="desc" <?= ($_POST['order'] ?? '') === 'desc' ? 'selected' : '' ?>>Decroissant (rsort)</option>
      </select>
      <button type="submit">Trier</button>
    </form>
    <?php if ($action === 'sort'): ?>
    <div class="result-box">
      <div class="result-title">Tableau trie</div>
      <div class="badge-list">
        <?php foreach ($result as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'math' ? 'active' : '' ?>">
    <div class="card-title">Calculs sur tableau</div>
    <div class="card-func">array_sum | array_product | max | min</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="math">
      <label>Nombres (separes par virgule)</label>
      <input type="text" name="numbers" value="<?= htmlspecialchars($_POST['numbers'] ?? '') ?>" placeholder="ex: 3,7,2,9,1">
      <button type="submit">Calculer</button>
    </form>
    <?php if ($action === 'math' && is_array($result)): ?>
    <div class="result-box">
      <div class="result-title">Statistiques</div>
      <?php foreach ($result as $k => $v): ?>
        <div class="stat-row">
          <span class="stat-label"><?= $k ?></span>
          <span class="stat-value"><?= $v ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'filter' ? 'active' : '' ?>">
    <div class="card-title">Filtrage conditionnel</div>
    <div class="card-func">array_filter() avec operateur</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="filter">
      <label>Nombres (separes par virgule)</label>
      <input type="text" name="numbers" value="<?= htmlspecialchars($_POST['numbers'] ?? '') ?>" placeholder="ex: 1,5,3,8,2,9">
      <label>Operateur</label>
      <select name="operator">
        <option value="gt"  <?= ($_POST['operator'] ?? '') === 'gt'  ? 'selected' : '' ?>>Superieur a ( > )</option>
        <option value="lt"  <?= ($_POST['operator'] ?? '') === 'lt'  ? 'selected' : '' ?>>Inferieur a ( < )</option>
        <option value="gte" <?= ($_POST['operator'] ?? '') === 'gte' ? 'selected' : '' ?>>Superieur ou egal ( >= )</option>
        <option value="lte" <?= ($_POST['operator'] ?? '') === 'lte' ? 'selected' : '' ?>>Inferieur ou egal ( <= )</option>
        <option value="eq"  <?= ($_POST['operator'] ?? '') === 'eq'  ? 'selected' : '' ?>>Egal a ( == )</option>
      </select>
      <label>Valeur de comparaison</label>
      <input type="text" name="value" value="<?= htmlspecialchars($_POST['value'] ?? '') ?>" placeholder="ex: 4">
      <button type="submit">Filtrer</button>
    </form>
    <?php if ($action === 'filter'): ?>
    <div class="result-box">
      <div class="result-title">Elements filtres</div>
      <div class="badge-list">
        <?php if (empty($result)): ?>
          <span class="error">Aucun element correspond</span>
        <?php else: foreach ($result as $v): ?>
          <span class="badge num"><?= $v ?></span>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'map' ? 'active' : '' ?>">
    <div class="card-title">Transformation tableau</div>
    <div class="card-func">array_map()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="map">
      <label>Nombres (separes par virgule)</label>
      <input type="text" name="numbers" value="<?= htmlspecialchars($_POST['numbers'] ?? '') ?>" placeholder="ex: 2,3,4,5">
      <label>Operation</label>
      <select name="operation">
        <option value="square" <?= ($_POST['operation'] ?? '') === 'square' ? 'selected' : '' ?>>Carre (n * n)</option>
        <option value="double" <?= ($_POST['operation'] ?? '') === 'double' ? 'selected' : '' ?>>Double (n * 2)</option>
        <option value="triple" <?= ($_POST['operation'] ?? '') === 'triple' ? 'selected' : '' ?>>Triple (n * 3)</option>
        <option value="abs"    <?= ($_POST['operation'] ?? '') === 'abs'    ? 'selected' : '' ?>>Valeur absolue</option>
      </select>
      <button type="submit">Transformer</button>
    </form>
    <?php if ($action === 'map'): ?>
    <div class="result-box">
      <div class="result-title">Tableau transforme</div>
      <div class="badge-list">
        <?php foreach ($result as $v): ?>
          <span class="badge num"><?= $v ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'unique' ? 'active' : '' ?>">
    <div class="card-title">Valeurs uniques</div>
    <div class="card-func">array_unique()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="unique">
      <label>Elements avec doublons</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_POST['items'] ?? '') ?>" placeholder="ex: a,b,a,c,b,d,a">
      <button type="submit">Supprimer doublons</button>
    </form>
    <?php if ($action === 'unique' && is_array($result)): ?>
    <div class="result-box">
      <div class="result-title">Resultat</div>
      <div class="stat-row"><span class="stat-label">Avant</span><span class="stat-value"><?= $result['before'] ?> elements</span></div>
      <div class="stat-row"><span class="stat-label">Doublons supprimes</span><span class="stat-value"><?= $result['removed'] ?></span></div>
      <hr class="divider">
      <div class="badge-list">
        <?php foreach ($result['after'] as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'reverse' ? 'active' : '' ?>">
    <div class="card-title">Inverser tableau</div>
    <div class="card-func">array_reverse()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="reverse">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_POST['items'] ?? '') ?>" placeholder="ex: un,deux,trois,quatre">
      <button type="submit">Inverser</button>
    </form>
    <?php if ($action === 'reverse'): ?>
    <div class="result-box">
      <div class="result-title">Tableau inverse</div>
      <div class="badge-list">
        <?php foreach ($result as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'slice' ? 'active' : '' ?>">
    <div class="card-title">Extraire une portion</div>
    <div class="card-func">array_slice()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="slice">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_POST['items'] ?? '') ?>" placeholder="ex: a,b,c,d,e,f,g">
      <label>Offset (debut)</label>
      <input type="text" name="offset" value="<?= htmlspecialchars($_POST['offset'] ?? '') ?>" placeholder="ex: 2">
      <label>Longueur</label>
      <input type="text" name="length" value="<?= htmlspecialchars($_POST['length'] ?? '') ?>" placeholder="ex: 3">
      <button type="submit">Extraire</button>
    </form>
    <?php if ($action === 'slice'): ?>
    <div class="result-box">
      <div class="result-title">Portion extraite</div>
      <div class="badge-list">
        <?php if (empty($result)): ?>
          <span class="error">Aucun element</span>
        <?php else: foreach ($result as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'chunk' ? 'active' : '' ?>">
    <div class="card-title">Decouper en groupes</div>
    <div class="card-func">array_chunk()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="chunk">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_POST['items'] ?? '') ?>" placeholder="ex: a,b,c,d,e,f,g">
      <label>Taille de chaque groupe</label>
      <input type="text" name="size" value="<?= htmlspecialchars($_POST['size'] ?? '') ?>" placeholder="ex: 3">
      <button type="submit">Decouper</button>
    </form>
    <?php if ($action === 'chunk'): ?>
    <div class="result-box">
      <div class="result-title">Groupes</div>
      <div class="chunk-group">
        <?php foreach ($result as $i => $groupe): ?>
          <div class="chunk-box">[<?= $i ?>] <?= implode(', ', array_map('htmlspecialchars', $groupe)) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'combine' ? 'active' : '' ?>">
    <div class="card-title">Combiner cles et valeurs</div>
    <div class="card-func">array_combine()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="combine">
      <label>Cles (separes par virgule)</label>
      <input type="text" name="keys" value="<?= htmlspecialchars($_POST['keys'] ?? '') ?>" placeholder="ex: nom,age,ville">
      <label>Valeurs (separes par virgule)</label>
      <input type="text" name="values" value="<?= htmlspecialchars($_POST['values'] ?? '') ?>" placeholder="ex: Ali,25,Paris">
      <button type="submit">Combiner</button>
    </form>
    <?php if ($action === 'combine'): ?>
    <div class="result-box">
      <div class="result-title">Tableau associatif</div>
      <?php if ($result === 'error'): ?>
        <span class="error">Nombre de cles et valeurs different</span>
      <?php else: foreach ($result as $k => $v): ?>
        <div class="kv-row">
          <span class="kv-key"><?= htmlspecialchars($k) ?></span>
          <span class="kv-value"><?= htmlspecialchars($v) ?></span>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'merge' ? 'active' : '' ?>">
    <div class="card-title">Fusionner tableaux</div>
    <div class="card-func">array_merge()</div>
    <span class="method-badge">POST</span>
    <form method="POST">
      <input type="hidden" name="action" value="merge">
      <label>Tableau 1</label>
      <input type="text" name="array1" value="<?= htmlspecialchars($_POST['array1'] ?? '') ?>" placeholder="ex: a,b,c">
      <label>Tableau 2</label>
      <input type="text" name="array2" value="<?= htmlspecialchars($_POST['array2'] ?? '') ?>" placeholder="ex: d,e,f">
      <label>Tableau 3</label>
      <input type="text" name="array3" value="<?= htmlspecialchars($_POST['array3'] ?? '') ?>" placeholder="ex: g,h,i">
      <button type="submit">Fusionner</button>
    </form>
    <?php if ($action === 'merge' && is_array($result)): ?>
    <div class="result-box">
      <div class="result-title">Resultat fusionne</div>
      <div class="arr-group">
        <?php foreach (['arr1', 'arr2', 'arr3'] as $i => $key): ?>
        <div class="arr-row">
          <span class="arr-label">Tab <?= $i+1 ?></span>
          <?php foreach ($result[$key] as $v): ?>
            <span class="badge val"><?= htmlspecialchars($v) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <hr class="divider">
        <div class="arr-row">
          <span class="arr-label">Fusion</span>
          <?php foreach ($result['merged'] as $v): ?>
            <span class="badge str"><?= htmlspecialchars($v) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>
</body>
</html>