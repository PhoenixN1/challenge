<?php
session_start();

$host     = 'localhost';
$dbname   = 'ecole_db';
$username = 'root';
$password = '';

function getConnection($host, $dbname, $username, $password) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateSearchQuery($query) {
    if (strlen($query) < 2) {
        return 'La recherche doit contenir au moins 2 caracteres.';
    }
    if (strlen($query) > 100) {
        return 'La recherche ne peut pas depasser 100 caracteres.';
    }
    if (!preg_match('/^[\p{L}\p{N}\s\-_.@]+$/u', $query)) {
        return 'La recherche contient des caracteres non autorises.';
    }
    return null;
}

function checkRateLimit() {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'search_rate_' . md5($ip);

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }

    if (time() - $_SESSION[$key]['time'] > 60) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }

    $_SESSION[$key]['count']++;

    return $_SESSION[$key]['count'] <= 20;
}

function searchDatabase($pdo, $query, $table, $columns) {
    $allowed_tables  = ['eleves', 'enseignants', 'classes', 'matieres'];
    $allowed_columns = ['nom', 'prenom', 'email', 'telephone', 'nom_classe', 'nom_matiere'];

    if (!in_array($table, $allowed_tables, true)) {
        return [];
    }

    $valid_columns = array_filter($columns, fn($c) => in_array($c, $allowed_columns, true));
    if (empty($valid_columns)) {
        return [];
    }

    $conditions = array_map(fn($col) => "$col LIKE :query", $valid_columns);
    $sql        = "SELECT * FROM $table WHERE " . implode(' OR ', $conditions) . " LIMIT 50";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':query' => '%' . $query . '%']);
    return $stmt->fetchAll();
}

$csrfToken = generateCsrfToken();
$results   = [];
$error     = '';
$success   = false;
$query     = '';
$table     = 'eleves';

$tableConfig = [
    'eleves'      => ['label' => 'Eleves',      'columns' => ['nom', 'prenom', 'email']],
    'enseignants' => ['label' => 'Enseignants', 'columns' => ['nom', 'prenom', 'email']],
    'classes'     => ['label' => 'Classes',     'columns' => ['nom_classe']],
    'matieres'    => ['label' => 'Matieres',    'columns' => ['nom_matiere']],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de securite invalide. Veuillez recharger la page.';
    } elseif (!checkRateLimit()) {
        $error = 'Trop de requetes. Veuillez patienter avant de relancer une recherche.';
    } else {
        $query = sanitizeInput($_POST['query'] ?? '');
        $table = sanitizeInput($_POST['table'] ?? 'eleves');

        $validationError = validateSearchQuery($query);
        if ($validationError) {
            $error = $validationError;
        } elseif (!array_key_exists($table, $tableConfig)) {
            $error = 'Table invalide.';
        } else {
            $pdo = getConnection($host, $dbname, $username, $password);
            if (!$pdo) {
                $error = 'Connexion a la base de donnees echouee.';
            } else {
                $results = searchDatabase($pdo, $query, $table, $tableConfig[$table]['columns']);
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systeme de Recherche</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap');

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg:      #0d0d0d;
            --surface: #161616;
            --border:  #2a2a2a;
            --accent:  #c8f560;
            --text:    #e8e8e8;
            --muted:   #666;
            --error:   #ff5c5c;
            --radius:  4px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Syne', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 20px;
        }

        header {
            width: 100%;
            max-width: 820px;
            margin-bottom: 48px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 24px;
        }

        header h1 {
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--accent);
        }

        header p {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 6px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .card {
            width: 100%;
            max-width: 820px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 32px;
            margin-bottom: 32px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 12px;
            align-items: end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.72rem;
            font-family: 'DM Mono', monospace;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        input[type="text"],
        select {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 12px 14px;
            font-family: 'DM Mono', monospace;
            font-size: 0.875rem;
            border-radius: var(--radius);
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--accent);
        }

        select {
            cursor: pointer;
            appearance: none;
            padding-right: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23666'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        button[type="submit"] {
            background: var(--accent);
            color: #0d0d0d;
            border: none;
            padding: 12px 28px;
            font-family: 'Syne', sans-serif;
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            border-radius: var(--radius);
            cursor: pointer;
            text-transform: uppercase;
            transition: opacity 0.2s;
            white-space: nowrap;
            align-self: flex-end;
        }

        button[type="submit"]:hover {
            opacity: 0.85;
        }

        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            font-family: 'DM Mono', monospace;
            font-size: 0.8rem;
            margin-bottom: 24px;
            border-left: 3px solid;
        }

        .alert-error {
            background: rgba(255, 92, 92, 0.08);
            border-color: var(--error);
            color: var(--error);
        }

        .meta {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: var(--muted);
            margin-bottom: 20px;
            letter-spacing: 0.06em;
        }

        .meta span {
            color: var(--accent);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'DM Mono', monospace;
            font-size: 0.82rem;
        }

        thead th {
            text-align: left;
            padding: 10px 14px;
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: rgba(200, 245, 96, 0.04);
        }

        tbody td {
            padding: 12px 14px;
            color: var(--text);
        }

        .empty {
            text-align: center;
            padding: 40px 0;
            color: var(--muted);
            font-family: 'DM Mono', monospace;
            font-size: 0.82rem;
        }

        .security-note {
            width: 100%;
            max-width: 820px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .badge {
            font-family: 'DM Mono', monospace;
            font-size: 0.68rem;
            padding: 5px 10px;
            border: 1px solid var(--border);
            border-radius: 2px;
            color: var(--muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Recherche</h1>
    <p>Systeme de recherche securise — ecole_db</p>
</header>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="form-row">
            <div class="field">
                <label for="query">Mot-cle</label>
                <input
                    type="text"
                    id="query"
                    name="query"
                    value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Rechercher..."
                    maxlength="100"
                    required
                >
            </div>

            <div class="field">
                <label for="table">Table</label>
                <select id="table" name="table">
                    <?php foreach ($tableConfig as $key => $cfg): ?>
                        <option value="<?= $key ?>" <?= $table === $key ? 'selected' : '' ?>>
                            <?= $cfg['label'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Rechercher</button>
        </div>
    </form>
</div>

<?php if ($success): ?>
<div class="card">
    <p class="meta">
        <span><?= count($results) ?></span> resultat<?= count($results) !== 1 ? 's' : '' ?>
        pour <span>"<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"</span>
        dans <span><?= $tableConfig[$table]['label'] ?></span>
    </p>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($results[0]) as $col): ?>
                        <th><?= htmlspecialchars($col, ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= htmlspecialchars((string)$cell, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">Aucun resultat trouve.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="security-note">
    <span class="badge">CSRF Token</span>
    <span class="badge">Prepared Statements</span>
    <span class="badge">Input Validation</span>
    <span class="badge">Rate Limiting</span>
    <span class="badge">XSS Protection</span>
    <span class="badge">Whitelist Tables</span>
</div>

</body>
</html>
