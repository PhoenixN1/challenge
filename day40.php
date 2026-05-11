<?php
session_start();

$host = 'localhost';
$dbname = 'school_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $classe   = trim($_POST['classe'] ?? '');
    $age      = (int)($_POST['age'] ?? 0);

    if (empty($nom))    $errors[] = "Le nom est requis.";
    if (empty($prenom)) $errors[] = "Le prénom est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (empty($classe)) $errors[] = "La classe est requise.";
    if ($age < 5 || $age > 100) $errors[] = "L'âge doit être entre 5 et 100.";

    if (empty($errors)) {
        $upd = $pdo->prepare("UPDATE students SET nom=?, prenom=?, email=?, classe=?, age=? WHERE id=?");
        $upd->execute([$nom, $prenom, $email, $classe, $age, $id]);
        $success = true;
        $student = compact('nom', 'prenom', 'email', 'classe', 'age') + ['id' => $id];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Étudiant</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f5f2ee;
            --surface: #ffffff;
            --primary: #1a1a2e;
            --accent: #c8a96e;
            --accent-light: #e8d5b0;
            --text: #1a1a2e;
            --text-muted: #7a7a8c;
            --border: #e0dbd2;
            --error-bg: #fff5f5;
            --error-border: #e8a0a0;
            --error-text: #8b2020;
            --success-bg: #f0f7f0;
            --success-border: #8fc48f;
            --success-text: #1a4d1a;
            --input-focus: #1a1a2e;
            --radius: 4px;
            --shadow: 0 2px 20px rgba(26, 26, 46, 0.08);
            --shadow-hover: 0 4px 30px rgba(26, 26, 46, 0.14);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .page-wrapper {
            width: 100%;
            max-width: 620px;
        }

        .page-header {
            margin-bottom: 36px;
        }

        .breadcrumb {
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: var(--accent);
        }

        .breadcrumb span {
            margin: 0 8px;
        }

        .page-title {
            font-family: 'DM Serif Display', serif;
            font-size: 38px;
            font-weight: 400;
            color: var(--primary);
            line-height: 1.15;
        }

        .page-title em {
            font-style: italic;
            color: var(--accent);
        }

        .student-id-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 12px;
            background: var(--accent-light);
            color: var(--primary);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .card {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .card-accent-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 100%);
        }

        .card-body {
            padding: 40px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            font-size: 14px;
            margin-bottom: 28px;
            border-left: 3px solid;
        }

        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error-text);
        }

        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .alert ul {
            margin-top: 6px;
            padding-left: 18px;
        }

        .alert ul li {
            margin-top: 3px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--text);
            background: var(--bg);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
            appearance: none;
        }

        input:focus {
            border-color: var(--input-focus);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(26, 26, 46, 0.07);
        }

        input::placeholder {
            color: #c0bdb8;
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 30px 0;
            grid-column: 1 / -1;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 13px 28px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.04em;
            border-radius: var(--radius);
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #2d2d4e;
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-danger {
            background: transparent;
            color: var(--error-text);
            border: 1px solid var(--error-border);
            margin-left: auto;
        }

        .btn-danger:hover {
            background: var(--error-bg);
        }

        .section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--accent-light);
        }

        @media (max-width: 540px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 28px 22px;
            }

            .page-title {
                font-size: 30px;
            }

            .btn-danger {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <div class="page-header">
        <div class="breadcrumb">
            <a href="index.php">Étudiants</a>
            <span>/</span>
            Modifier
        </div>
        <h1 class="page-title">Modifier <em>l'étudiant</em></h1>
        <div class="student-id-badge">ID #<?= htmlspecialchars($id) ?></div>
    </div>

    <div class="card">
        <div class="card-accent-bar"></div>
        <div class="card-body">

            <?php if ($success): ?>
                <div class="alert alert-success">
                    Les informations ont été mises à jour avec succès.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Veuillez corriger les erreurs suivantes :</strong>
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit.php?id=<?= $id ?>">

                <div class="section-label">Identité</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="<?= htmlspecialchars($student['nom']) ?>"
                            placeholder="Nom de famille"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input
                            type="text"
                            id="prenom"
                            name="prenom"
                            value="<?= htmlspecialchars($student['prenom']) ?>"
                            placeholder="Prénom"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="age">Âge</label>
                        <input
                            type="number"
                            id="age"
                            name="age"
                            value="<?= htmlspecialchars($student['age']) ?>"
                            min="5"
                            max="100"
                            placeholder="Ex: 20"
                            required
                        >
                    </div>

                    <div class="form-group full-width" style="margin-top:24px;">
                        <div class="section-label">Scolarité</div>
                    </div>

                    <div class="form-group">
                        <label for="classe">Classe</label>
                        <input
                            type="text"
                            id="classe"
                            name="classe"
                            value="<?= htmlspecialchars($student['classe']) ?>"
                            placeholder="Ex: Terminale A"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($student['email']) ?>"
                            placeholder="email@exemple.com"
                            required
                        >
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                    <a href="delete.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
