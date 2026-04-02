<?php
$result = null;
$action = $_GET['action'] ?? '';

if ($action === 'search') {
    $items = ["pomme", "banane", "cerise", "datte", "abricot", "mangue", "kiwi", "fraise"];
    $query = strtolower(trim($_GET['query'] ?? ''));
    $result = array_filter($items, fn($i) => str_contains(strtolower($i), $query));
}

if ($action === 'sort') {
    $data = explode(',', $_GET['items'] ?? '');
    $data = array_map('trim', $data);
    $order = $_GET['order'] ?? 'asc';
    $order === 'asc' ? sort($data) : rsort($data);
    $result = $data;
}

if ($action === 'math') {
    $nums = array_map('intval', explode(',', $_GET['numbers'] ?? '0'));
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
    $nums = array_map('intval', explode(',', $_GET['numbers'] ?? '0'));
    $op   = $_GET['operator'] ?? 'gt';
    $val  = intval($_GET['value'] ?? 0);
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
    $data   = array_map('trim', explode(',', $_GET['items'] ?? ''));
    $before = count($data);
    $result = ['before' => $before, 'after' => array_unique($data), 'removed' => $before - count(array_unique($data))];
}

if ($action === 'reverse') {
    $data   = array_map('trim', explode(',', $_GET['items'] ?? ''));
    $result = array_reverse($data);
}

if ($action === 'chunk') {
    $data   = array_map('trim', explode(',', $_GET['items'] ?? ''));
    $size   = max(1, intval($_GET['size'] ?? 2));
    $result = array_chunk($data, $size);
}

if ($action === 'combine') {
    $keys   = array_map('trim', explode(',', $_GET['keys'] ?? ''));
    $vals   = array_map('trim', explode(',', $_GET['values'] ?? ''));
    if (count($keys) === count($vals)) {
        $result = array_combine($keys, $vals);
    } else {
        $result = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Forms GET</title>
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
    margin-bottom: 8px;
    letter-spacing: 2px;
}

.subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 40px;
    font-size: 0.9rem;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 24px;
    max-width: 1300px;
    margin: 0 auto;
}

.card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 14px;
    padding: 24px;
    transition: border-color 0.2s, transform 0.2s;
}

.card:hover { border-color: #7dd3fc; transform: translateY(-3px); }

.card-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #7dd3fc;
    margin-bottom: 4px;
}

.card-func {
    font-family: monospace;
    font-size: 0.78rem;
    color: #f472b6;
    margin-bottom: 16px;
}

label {
    display: block;
    font-size: 0.78rem;
    color: #94a3b8;
    margin-bottom: 5px;
    margin-top: 10px;
}

input[type="text"],
select {
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

input[type="text"]:focus,
select:focus { border-color: #7dd3fc; }

select option { background: #1e293b; }

button {
    margin-top: 14px;
    width: 100%;
    background: #0ea5e9;
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

button:hover { background: #0284c7; }

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

.badge.str { color: #a3e635; }
.badge.num { color: #fb923c; }
.badge.key { color: #c084fc; }

.stat-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
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

.error { color: #f87171; font-size: 0.82rem; font-family: monospace; }
.active-card { border-color: #4ade80; }
</style>
</head>
<body>

<h1>PHP Array Functions</h1>
<p class="subtitle">Formulaires GET — Interactions en temps reel</p>

<div class="grid">

  <div class="card <?= $action === 'search' ? 'active-card' : '' ?>">
    <div class="card-title">Recherche dans tableau</div>
    <div class="card-func">array_filter + str_contains</div>
    <form method="GET">
      <input type="hidden" name="action" value="search">
      <label>Mot a rechercher</label>
      <input type="text" name="query" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" placeholder="ex: an">
      <button type="submit">Rechercher</button>
    </form>
    <?php if ($action === 'search'): ?>
    <div class="result-box">
      <div class="result-title">Resultats</div>
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

  <div class="card <?= $action === 'sort' ? 'active-card' : '' ?>">
    <div class="card-title">Tri de tableau</div>
    <div class="card-func">sort() | rsort()</div>
    <form method="GET">
      <input type="hidden" name="action" value="sort">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_GET['items'] ?? '') ?>" placeholder="ex: banane,pomme,cerise">
      <label>Ordre</label>
      <select name="order">
        <option value="asc"  <?= ($_GET['order'] ?? '') === 'asc'  ? 'selected' : '' ?>>Croissant (sort)</option>
        <option value="desc" <?= ($_GET['order'] ?? '') === 'desc' ? 'selected' : '' ?>>Decroissant (rsort)</option>
      </select>
      <button type="submit">Trier</button>
    </form>
    <?php if ($action === 'sort'): ?>
    <div class="result-box">
      <div class="result-title">Resultat trie</div>
      <div class="badge-list">
        <?php foreach ($result as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'math' ? 'active-card' : '' ?>">
    <div class="card-title">Calculs sur tableau</div>
    <div class="card-func">array_sum | array_product | max | min</div>
    <form method="GET">
      <input type="hidden" name="action" value="math">
      <label>Nombres (separes par virgule)</label>
      <input type="text" name="numbers" value="<?= htmlspecialchars($_GET['numbers'] ?? '') ?>" placeholder="ex: 3,7,2,9,1">
      <button type="submit">Calculer</button>
    </form>
    <?php if ($action === 'math' && is_array($result)): ?>
    <div class="result-box">
      <div class="result-title">Resultats</div>
      <?php foreach ($result as $k => $v): ?>
        <div class="stat-row">
          <span class="stat-label"><?= $k ?></span>
          <span class="stat-value"><?= $v ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'filter' ? 'active-card' : '' ?>">
    <div class="card-title">Filtrage conditionnel</div>
    <div class="card-func">array_filter() avec operateur</div>
    <form method="GET">
      <input type="hidden" name="action" value="filter">
      <label>Nombres (separes par virgule)</label>
      <input type="text" name="numbers" value="<?= htmlspecialchars($_GET['numbers'] ?? '') ?>" placeholder="ex: 1,5,3,8,2,9">
      <label>Operateur</label>
      <select name="operator">
        <option value="gt"  <?= ($_GET['operator'] ?? '') === 'gt'  ? 'selected' : '' ?>>Superieur a</option>
        <option value="lt"  <?= ($_GET['operator'] ?? '') === 'lt'  ? 'selected' : '' ?>>Inferieur a</option>
        <option value="gte" <?= ($_GET['operator'] ?? '') === 'gte' ? 'selected' : '' ?>>Superieur ou egal</option>
        <option value="lte" <?= ($_GET['operator'] ?? '') === 'lte' ? 'selected' : '' ?>>Inferieur ou egal</option>
        <option value="eq"  <?= ($_GET['operator'] ?? '') === 'eq'  ? 'selected' : '' ?>>Egal a</option>
      </select>
      <label>Valeur de comparaison</label>
      <input type="text" name="value" value="<?= htmlspecialchars($_GET['value'] ?? '') ?>" placeholder="ex: 4">
      <button type="submit">Filtrer</button>
    </form>
    <?php if ($action === 'filter'): ?>
    <div class="result-box">
      <div class="result-title">Elements filtres</div>
      <div class="badge-list">
        <?php if (empty($result)): ?>
          <span class="error">Aucun element</span>
        <?php else: foreach ($result as $v): ?>
          <span class="badge num"><?= $v ?></span>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'unique' ? 'active-card' : '' ?>">
    <div class="card-title">Valeurs uniques</div>
    <div class="card-func">array_unique()</div>
    <form method="GET">
      <input type="hidden" name="action" value="unique">
      <label>Elements avec doublons</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_GET['items'] ?? '') ?>" placeholder="ex: a,b,a,c,b,d">
      <button type="submit">Supprimer doublons</button>
    </form>
    <?php if ($action === 'unique' && is_array($result)): ?>
    <div class="result-box">
      <div class="result-title">Resultat</div>
      <div class="stat-row"><span class="stat-label">Avant</span><span class="stat-value"><?= $result['before'] ?> elements</span></div>
      <div class="stat-row"><span class="stat-label">Supprimes</span><span class="stat-value"><?= $result['removed'] ?> doublons</span></div>
      <div style="margin-top:10px;" class="badge-list">
        <?php foreach ($result['after'] as $v): ?>
          <span class="badge str"><?= htmlspecialchars($v) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="card <?= $action === 'reverse' ? 'active-card' : '' ?>">
    <div class="card-title">Inverser tableau</div>
    <div class="card-func">array_reverse()</div>
    <form method="GET">
      <input type="hidden" name="action" value="reverse">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_GET['items'] ?? '') ?>" placeholder="ex: un,deux,trois,quatre">
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

  <div class="card <?= $action === 'chunk' ? 'active-card' : '' ?>">
    <div class="card-title">Decouper en groupes</div>
    <div class="card-func">array_chunk()</div>
    <form method="GET">
      <input type="hidden" name="action" value="chunk">
      <label>Elements (separes par virgule)</label>
      <input type="text" name="items" value="<?= htmlspecialchars($_GET['items'] ?? '') ?>" placeholder="ex: a,b,c,d,e,f,g">
      <label>Taille de chaque groupe</label>
      <input type="text" name="size" value="<?= htmlspecialchars($_GET['size'] ?? '') ?>" placeholder="ex: 3">
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

  <div class="card <?= $action === 'combine' ? 'active-card' : '' ?>">
    <div class="card-title">Combiner cles et valeurs</div>
    <div class="card-func">array_combine()</div>
    <form method="GET">
      <input type="hidden" name="action" value="combine">
      <label>Cles (separes par virgule)</label>
      <input type="text" name="keys" value="<?= htmlspecialchars($_GET['keys'] ?? '') ?>" placeholder="ex: nom,age,ville">
      <label>Valeurs (separes par virgule)</label>
      <input type="text" name="values" value="<?= htmlspecialchars($_GET['values'] ?? '') ?>" placeholder="ex: Ali,25,Paris">
      <button type="submit">Combiner</button>
    </form>
    <?php if ($action === 'combine'): ?>
    <div class="result-box">
      <div class="result-title">Tableau associatif</div>
      <?php if ($result === 'error'): ?>
        <span class="error">Les cles et valeurs doivent avoir le meme nombre d'elements</span>
      <?php else: foreach ($result as $k => $v): ?>
        <div class="kv-row">
          <span class="kv-key"><?= htmlspecialchars($k) ?></span>
          <span class="kv-value"><?= htmlspecialchars($v) ?></span>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <?php endif; ?>
  </div>

</div>
</body>
</html>