<?php
session_start();

$utilisateurs = [
    'admin' => '1234',
    'alice' => 'motdepasse',
];

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = trim($_POST['mot_de_passe'] ?? '');

    if (isset($utilisateurs[$login]) && $utilisateurs[$login] === $mdp) {
        $_SESSION['utilisateur'] = $login;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $erreur = 'Identifiant ou mot de passe incorrect.';
    }
}

if (isset($_POST['deconnexion'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .carte {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 380px;
        }

        h1 {
            text-align: center;
            font-size: 1.5rem;
            color: #1a1a2e;
            margin-bottom: 28px;
            font-weight: 600;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #333;
            transition: border-color 0.2s;
            margin-bottom: 18px;
            background: #fafafa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4f7cff;
            outline: none;
            background: #fff;
        }

        .btn-connexion {
            width: 100%;
            padding: 11px;
            background: #4f7cff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-connexion:hover {
            background: #3a63e0;
        }

        .erreur {
            background: #fff0f0;
            border: 1px solid #ffb3b3;
            color: #cc0000;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 18px;
            text-align: center;
        }

        .bienvenue {
            text-align: center;
        }

        .bienvenue h2 {
            font-size: 1.3rem;
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .bienvenue p {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 24px;
        }

        .badge {
            display: inline-block;
            background: #e8f0fe;
            color: #4f7cff;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .btn-deconnexion {
            width: 100%;
            padding: 10px;
            background: #fff;
            color: #cc0000;
            border: 1.5px solid #cc0000;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .btn-deconnexion:hover {
            background: #cc0000;
            color: #fff;
        }

        .separateur {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        .astuce {
            font-size: 0.78rem;
            color: #aaa;
            text-align: center;
            margin-top: 16px;
        }
    </style>
</head>
<body>

<div class="carte">

    <?php if (isset($_SESSION['utilisateur'])): ?>

        <div class="bienvenue">
            <h2>Bienvenue</h2>
            <div class="badge"><?= htmlspecialchars($_SESSION['utilisateur']) ?></div>
            <p>Vous etes connecte avec succes.</p>
            <form method="POST">
                <button class="btn-deconnexion" name="deconnexion">Se deconnecter</button>
            </form>
        </div>

    <?php else: ?>

        <h1>Connexion</h1>

        <?php if ($erreur): ?>
            <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="login">Identifiant</label>
            <input type="text" id="login" name="login" placeholder="Votre identifiant" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" required>

            <button type="submit" class="btn-connexion">Se connecter</button>
        </form>

        <hr class="separateur">
        <p class="astuce">Comptes de test : admin / 1234 &nbsp;|&nbsp; alice / motdepasse</p>

    <?php endif; ?>

</div>

</body>
</html>
