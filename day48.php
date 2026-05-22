<?php
session_start();

$users = [
    ['id' => 1, 'username' => 'admin',   'password' => 'admin123',   'role' => 'admin'],
    ['id' => 2, 'username' => 'mohamed', 'password' => 'user123',    'role' => 'user'],
    ['id' => 3, 'username' => 'sara',    'password' => 'user456',    'role' => 'user'],
];

function findUser(array $users, string $username, string $password): ?array {
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return $user;
        }
    }
    return null;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function hasRole(string $role): bool {
    return isLoggedIn() && $_SESSION['user']['role'] === $role;
}

function requireRole(string $role): void {
    if (!hasRole($role)) {
        $_SESSION['error'] = "Acces refuse. Role requis : $role.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$error   = '';
$success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {

        if ($_POST['action'] === 'login') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $user = findUser($users, $username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error = 'Identifiants incorrects.';
            }
        }

        if ($_POST['action'] === 'logout') {
            session_destroy();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        if ($_POST['action'] === 'admin_action') {
            requireRole('admin');
            $success = 'Action administrateur executee avec succes.';
        }

        if ($_POST['action'] === 'user_action') {
            if (!isLoggedIn()) {
                $_SESSION['error'] = 'Vous devez etre connecte.';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
            $success = 'Action utilisateur executee avec succes.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des roles</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            background: #0d0d0d;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            width: 100%;
            max-width: 520px;
        }

        h1 {
            font-size: 1.1rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 32px;
            border-left: 2px solid #444;
            padding-left: 12px;
        }

        .card {
            background: #161616;
            border: 1px solid #222;
            padding: 28px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: #0d0d0d;
            border: 1px solid #2a2a2a;
            color: #e0e0e0;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 0.85rem;
            margin-bottom: 16px;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #444;
        }

        button {
            width: 100%;
            padding: 11px;
            font-family: inherit;
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            border: 1px solid;
            transition: background 0.2s, color 0.2s;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: #e0e0e0;
            color: #0d0d0d;
            border-color: #e0e0e0;
        }

        .btn-primary:hover {
            background: #fff;
            border-color: #fff;
        }

        .btn-danger {
            background: transparent;
            color: #c0392b;
            border-color: #c0392b;
        }

        .btn-danger:hover {
            background: #c0392b;
            color: #fff;
        }

        .btn-secondary {
            background: transparent;
            color: #888;
            border-color: #333;
        }

        .btn-secondary:hover {
            border-color: #888;
            color: #e0e0e0;
        }

        .btn-admin {
            background: transparent;
            color: #e67e22;
            border-color: #e67e22;
        }

        .btn-admin:hover {
            background: #e67e22;
            color: #fff;
        }

        .alert {
            padding: 10px 14px;
            font-size: 0.78rem;
            margin-bottom: 18px;
            border-left: 3px solid;
        }

        .alert-error {
            background: #1a0a0a;
            border-color: #c0392b;
            color: #e07070;
        }

        .alert-success {
            background: #0a1a0a;
            border-color: #27ae60;
            color: #70e090;
        }

        .badge {
            display: inline-block;
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 3px 8px;
            border: 1px solid;
        }

        .badge-admin { color: #e67e22; border-color: #e67e22; }
        .badge-user  { color: #3498db; border-color: #3498db; }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .info-label {
            font-size: 0.7rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .info-value {
            font-size: 0.85rem;
            color: #ccc;
        }

        .divider {
            border: none;
            border-top: 1px solid #1e1e1e;
            margin: 18px 0;
        }

        .hint {
            font-size: 0.7rem;
            color: #444;
            margin-top: 14px;
        }

        .hint span { color: #666; }
    </style>
</head>
<body>
<div class="container">

    <h1>Systeme de roles</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>

        <div class="card">
            <h2>Connexion</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" required autocomplete="off">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
                <button type="submit" class="btn-primary">Se connecter</button>
            </form>
            <p class="hint">
                Admin : <span>admin / admin123</span><br>
                Utilisateur : <span>mohamed / user123</span> &nbsp;|&nbsp; <span>sara / user456</span>
            </p>
        </div>

    <?php else:
        $currentUser = $_SESSION['user'];
    ?>

        <div class="card">
            <h2>Session active</h2>
            <div class="info-row">
                <span class="info-label">Utilisateur</span>
                <span class="info-value"><?= htmlspecialchars($currentUser['username']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Role</span>
                <span class="badge badge-<?= $currentUser['role'] ?>"><?= $currentUser['role'] ?></span>
            </div>
            <hr class="divider">

            <form method="POST">
                <input type="hidden" name="action" value="user_action">
                <button type="submit" class="btn-secondary">Action utilisateur</button>
            </form>

            <?php if (hasRole('admin')): ?>
            <form method="POST">
                <input type="hidden" name="action" value="admin_action">
                <button type="submit" class="btn-admin">Action administrateur</button>
            </form>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="btn-danger">Se deconnecter</button>
            </form>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
