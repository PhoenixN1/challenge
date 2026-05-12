<?php
session_start();

$host   = 'localhost';
$dbname = 'school_db';
$user   = 'root';
$pass   = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

$deleted = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $del = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $del->execute([$id]);
        $deleted = true;
    } catch (PDOException $e) {
        $error = "Une erreur est survenue lors de la suppression.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Étudiant</title>
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
            --danger: #8b2020;
            --danger-light: #c0392b;
            --danger-bg: #fff5f5;
            --danger-border: #e8a0a0;
            --success-bg: #f0f7f0;
            --success-border: #8fc48f;
            --success-text: #1a4d1a;
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
            max-width: 540px;
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
            color: var(--danger);
        }

        .card {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .card-danger-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--danger) 0%, var(--danger-border) 100%);
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

        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .alert-error {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger);
        }

        .warning-box {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            border-radius: var(--radius);
            padding: 18px 20px;
            margin-bottom: 28px;
        }

        .warning-box p {
            font-size: 14px;
            color: var(--danger);
            line-height: 1.6;
        }

        .warning-box strong {
            font-weight: 600;
        }

        .section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--accent-light);
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 32px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .info-value {
            font-size: 15px;
            font-weight: 400;
            color: var(--text);
            padding: 10px 14px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 14px;
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

        .btn-danger {
            background: var(--danger);
            color: #ffffff;
        }

        .btn-danger:hover {
            background: var(--danger-light);
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }

        .btn-danger:active {
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

        .redirect-note {
            margin-top: 20px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .redirect-note a {
            color: var(--success-text);
            font-weight: 500;
            text-decoration: none;
        }

        .redirect-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .student-info {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 28px 22px;
            }

            .page-title {
                font-size: 30px;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                text-align: center;
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
            <a href="edit.php?id=<?= $id ?>">Modifier</a>
            <span>/</span>
            Supprimer
        </div>
        <h1 class="page-title">Supprimer <em>l'étudiant</em></h1>
    </div>

    <div class="card">
        <div class="card-danger-bar"></div>
        <div class="card-body">

            <?php if ($deleted): ?>

                <div class="alert alert-success">
                    L'étudiant <strong><?= htmlspecialchars($student['prenom'] . ' ' . $student['nom']) ?></strong> a été supprimé avec succès.
                </div>
                <p class="redirect-note">
                    <a href="index.php">Retourner à la liste des étudiants</a>
                </p>

            <?php else: ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="warning-box">
                    <p>
                        Cette action est <strong>irréversible</strong>. L'étudiant et toutes ses données associées seront définitivement supprimés de la base de données.
                    </p>
                </div>

                <div class="section-label">Informations de l'étudiant</div>

                <div class="student-info">
                    <div class="info-item">
                        <span class="info-label">Nom</span>
                        <span class="info-value"><?= htmlspecialchars($student['nom']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Prénom</span>
                        <span class="info-value"><?= htmlspecialchars($student['prenom']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Classe</span>
                        <span class="info-value"><?= htmlspecialchars($student['classe']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Âge</span>
                        <span class="info-value"><?= htmlspecialchars($student['age']) ?> ans</span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($student['email']) ?></span>
                    </div>
                </div>

                <form method="POST" action="delete.php?id=<?= $id ?>">
                    <div class="form-actions">
                        <button type="submit" name="confirm_delete" class="btn btn-danger">
                            Confirmer la suppression
                        </button>
                        <a href="index.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>
