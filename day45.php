<!-- ///////////PARTIE SQL  ///////-->
CREATE DATABASE IF NOT EXISTS auth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE auth_db;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)     NOT NULL,
    email         VARCHAR(150)    NOT NULL,
    password_hash VARCHAR(255)    NOT NULL,
    created_at    DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_email    (email),
    UNIQUE KEY uq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'auth_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Connexion échouée : ' . $e->getMessage()]));
        }
    }

    return $pdo;
}



<?php

require_once __DIR__ . '/../config/database.php';

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function registerUser(string $username, string $email, string $password): array
{
    $pdo = getConnection();

    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['success' => false, 'message' => "Le nom d'utilisateur doit contenir entre 3 et 50 caractères."];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => "L'adresse email est invalide."];
    }

    if (strlen($password) < 8) {
        return ['success' => false, 'message' => "Le mot de passe doit contenir au moins 8 caractères."];
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
    $stmt->execute([$email, $username]);

    if ($stmt->fetch()) {
        return ['success' => false, 'message' => "Ce nom d'utilisateur ou email est déjà utilisé."];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$username, $email, $hash]);

    return ['success' => true, 'message' => "Compte créé avec succès. Vous pouvez maintenant vous connecter."];
}

function loginUser(string $email, string $password): array
{
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => "Email ou mot de passe incorrect."];
    }

    session_regenerate_id(true);

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['logged_at'] = time();

    return ['success' => true, 'message' => "Connexion réussie."];
}

function logoutUser(): void
{
    session_unset();
    session_destroy();
}






<?php

require_once __DIR__ . '/helpers/auth.php';

startSecureSession();

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$message = '';
$type    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Requête invalide. Veuillez réessayer.";
        $type    = 'error';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result  = registerUser($username, $email, $password);
        $message = $result['message'];
        $type    = $result['success'] ? 'success' : 'error';
    }
}

$csrf = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0e0f11;
            --surface:   #16181c;
            --border:    #2a2d33;
            --accent:    #c8a96e;
            --accent-dim:#a88a50;
            --text:      #e8e6e1;
            --muted:     #7a7873;
            --error:     #e05c5c;
            --success:   #5cb87a;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 2px;
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
        }

        .card-header {
            margin-bottom: 2.5rem;
        }

        .card-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .card-header p {
            margin-top: .4rem;
            font-size: .85rem;
            color: var(--muted);
        }

        .card-header p a {
            color: var(--accent);
            text-decoration: none;
        }

        .card-header p a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: .75rem 1rem;
            border-radius: 2px;
            font-size: .85rem;
            margin-bottom: 1.5rem;
            border-left: 3px solid;
        }

        .alert.error   { background: rgba(224,92,92,.08);  border-color: var(--error);   color: var(--error);   }
        .alert.success { background: rgba(92,184,122,.08); border-color: var(--success); color: var(--success); }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: .78rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .5rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 2px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            padding: .75rem 1rem;
            outline: none;
            transition: border-color .2s;
        }

        input:focus {
            border-color: var(--accent);
        }

        button[type="submit"] {
            width: 100%;
            background: var(--accent);
            color: #0e0f11;
            border: none;
            border-radius: 2px;
            font-family: 'DM Sans', sans-serif;
            font-size: .85rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .85rem;
            cursor: pointer;
            margin-top: .5rem;
            transition: background .2s;
        }

        button[type="submit"]:hover {
            background: var(--accent-dim);
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 2rem 0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>Inscription</h1>
            <p>Vous avez un compte ? <a href="login.php">Se connecter</a></p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Creer le compte</button>
        </form>
    </div>
</body>
</html>






<?php

require_once __DIR__ . '/helpers/auth.php';

startSecureSession();

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$message = '';
$type    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Requête invalide. Veuillez réessayer.";
        $type    = 'error';
    } else {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = loginUser($email, $password);
        $message = $result['message'];
        $type    = $result['success'] ? 'success' : 'error';

        if ($result['success']) {
            redirect('dashboard.php');
        }
    }
}

$csrf = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0e0f11;
            --surface:   #16181c;
            --border:    #2a2d33;
            --accent:    #c8a96e;
            --accent-dim:#a88a50;
            --text:      #e8e6e1;
            --muted:     #7a7873;
            --error:     #e05c5c;
            --success:   #5cb87a;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 2px;
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
        }

        .card-header {
            margin-bottom: 2.5rem;
        }

        .card-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .card-header p {
            margin-top: .4rem;
            font-size: .85rem;
            color: var(--muted);
        }

        .card-header p a {
            color: var(--accent);
            text-decoration: none;
        }

        .card-header p a:hover { text-decoration: underline; }

        .alert {
            padding: .75rem 1rem;
            border-radius: 2px;
            font-size: .85rem;
            margin-bottom: 1.5rem;
            border-left: 3px solid;
        }

        .alert.error   { background: rgba(224,92,92,.08);  border-color: var(--error);   color: var(--error);   }
        .alert.success { background: rgba(92,184,122,.08); border-color: var(--success); color: var(--success); }

        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: .78rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .5rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 2px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            padding: .75rem 1rem;
            outline: none;
            transition: border-color .2s;
        }

        input:focus { border-color: var(--accent); }

        button[type="submit"] {
            width: 100%;
            background: var(--accent);
            color: #0e0f11;
            border: none;
            border-radius: 2px;
            font-family: 'DM Sans', sans-serif;
            font-size: .85rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .85rem;
            cursor: pointer;
            margin-top: .5rem;
            transition: background .2s;
        }

        button[type="submit"]:hover { background: var(--accent-dim); }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>Connexion</h1>
            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>








<?php

require_once __DIR__ . '/helpers/auth.php';

startSecureSession();

logoutUser();

redirect('login.php');
