<?php
session_start();

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [
        1 => ['id' => 1, 'nom' => 'Alice', 'email' => 'alice@example.com', 'role' => 'Admin'],
        2 => ['id' => 2, 'nom' => 'Bob',   'email' => 'bob@example.com',   'role' => 'User'],
        3 => ['id' => 3, 'nom' => 'Carol', 'email' => 'carol@example.com', 'role' => 'Editor'],
    ];
    $_SESSION['next_id'] = 4;
}

$action  = $_GET['action'] ?? 'list';
$id      = isset($_GET['id']) ? (int)$_GET['id'] : null;
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom']   ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = trim($_POST['role']  ?? '');

    if ($nom === '' || $email === '' || $role === '') {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } else {
        if ($action === 'create') {
            $new_id = $_SESSION['next_id']++;
            $_SESSION['data'][$new_id] = ['id' => $new_id, 'nom' => $nom, 'email' => $email, 'role' => $role];
            $message = 'Enregistrement cree avec succes.';
            $action  = 'list';
        } elseif ($action === 'edit' && $id && isset($_SESSION['data'][$id])) {
            $_SESSION['data'][$id] = ['id' => $id, 'nom' => $nom, 'email' => $email, 'role' => $role];
            $message = 'Enregistrement mis a jour.';
            $action  = 'list';
        }
    }
}

if ($action === 'delete' && $id && isset($_SESSION['data'][$id])) {
    unset($_SESSION['data'][$id]);
    $message = 'Enregistrement supprime.';
    $action  = 'list';
}

$record = ($action === 'edit' && $id && isset($_SESSION['data'][$id])) ? $_SESSION['data'][$id] : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD PHP</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Courier New', monospace;
    background: #0f0f0f;
    color: #e0e0e0;
    min-height: 100vh;
    padding: 2rem;
}

h1 {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #fff;
    border-bottom: 1px solid #2a2a2a;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

h2 {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 1.5rem;
}

.container { max-width: 900px; margin: 0 auto; }

.msg {
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
    border-left: 3px solid #4ade80;
    background: #0d2218;
    color: #4ade80;
    font-size: 0.85rem;
    letter-spacing: 0.05em;
}

.err {
    border-left-color: #f87171;
    background: #1f0d0d;
    color: #f87171;
}

.btn {
    display: inline-block;
    padding: 0.5rem 1.2rem;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    border: 1px solid #333;
    background: transparent;
    color: #e0e0e0;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.btn:hover { background: #1a1a1a; border-color: #555; }

.btn-primary { border-color: #4ade80; color: #4ade80; }
.btn-primary:hover { background: #0d2218; }

.btn-danger { border-color: #f87171; color: #f87171; font-size: 0.75rem; padding: 0.3rem 0.7rem; }
.btn-danger:hover { background: #1f0d0d; }

.btn-edit { border-color: #60a5fa; color: #60a5fa; font-size: 0.75rem; padding: 0.3rem 0.7rem; }
.btn-edit:hover { background: #0d1a2e; }

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    margin-bottom: 2rem;
}

thead tr { border-bottom: 1px solid #2a2a2a; }

th {
    text-align: left;
    padding: 0.6rem 0.8rem;
    color: #666;
    font-size: 0.7rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 700;
}

td {
    padding: 0.7rem 0.8rem;
    border-bottom: 1px solid #1a1a1a;
    color: #ccc;
}

tr:hover td { background: #111; }

.badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    font-size: 0.7rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border: 1px solid;
}

.badge-admin  { color: #c084fc; border-color: #7c3aed; }
.badge-user   { color: #60a5fa; border-color: #2563eb; }
.badge-editor { color: #fbbf24; border-color: #d97706; }

.actions { display: flex; gap: 0.5rem; align-items: center; }

.form-card {
    border: 1px solid #2a2a2a;
    padding: 2rem;
    margin-bottom: 2rem;
    background: #111;
}

.form-group { margin-bottom: 1.2rem; }

label {
    display: block;
    font-size: 0.7rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #666;
    margin-bottom: 0.4rem;
}

input[type="text"],
input[type="email"],
select {
    width: 100%;
    padding: 0.6rem 0.8rem;
    background: #0f0f0f;
    border: 1px solid #2a2a2a;
    color: #e0e0e0;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.15s;
}

input:focus, select:focus { border-color: #4ade80; }

select option { background: #111; }

.form-actions { display: flex; gap: 1rem; margin-top: 1.5rem; }

.top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }

.id-cell { color: #444; font-size: 0.75rem; }
</style>
</head>
<body>
<div class="container">

<h1>Gestion des utilisateurs</h1>

<?php if ($message): ?>
<div class="msg"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="msg err"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>

<div class="top-bar">
    <h2>Liste</h2>
    <a href="?action=create" class="btn btn-primary">+ Nouveau</a>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($_SESSION['data'] as $row): ?>
        <tr>
            <td class="id-cell"><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <?php
                $cls = match(strtolower($row['role'])) {
                    'admin'  => 'badge-admin',
                    'editor' => 'badge-editor',
                    default  => 'badge-user',
                };
                ?>
                <span class="badge <?= $cls ?>"><?= htmlspecialchars($row['role']) ?></span>
            </td>
            <td>
                <div class="actions">
                    <a href="?action=edit&id=<?= $row['id'] ?>" class="btn btn-edit">Modifier</a>
                    <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger"
                       onclick="return confirm('Supprimer cet enregistrement ?')">Supprimer</a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($_SESSION['data'])): ?>
        <tr><td colspan="5" style="text-align:center; color:#444; padding:2rem;">Aucun enregistrement.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php elseif ($action === 'create' || $action === 'edit'): ?>

<div class="top-bar">
    <h2><?= $action === 'create' ? 'Nouvel enregistrement' : 'Modifier' ?></h2>
    <a href="?" class="btn">Retour</a>
</div>

<div class="form-card">
<form method="POST" action="?action=<?= $action ?><?= $id ? '&id=' . $id : '' ?>">

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom"
               value="<?= htmlspecialchars($record['nom'] ?? ($_POST['nom'] ?? '')) ?>"
               placeholder="Jean Dupont">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($record['email'] ?? ($_POST['email'] ?? '')) ?>"
               placeholder="jean@exemple.com">
    </div>

    <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role">
            <?php foreach (['Admin', 'User', 'Editor'] as $opt): ?>
            <option value="<?= $opt ?>"
                <?= (($record['role'] ?? ($_POST['role'] ?? '')) === $opt) ? 'selected' : '' ?>>
                <?= $opt ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $action === 'create' ? 'Creer' : 'Mettre a jour' ?>
        </button>
        <a href="?" class="btn">Annuler</a>
    </div>

</form>
</div>

<?php endif; ?>

</div>
</body>
</html>
