<?php

declare(strict_types=1);

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    echo render_error('Exception non capturée', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
});

register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo render_error('Erreur fatale', $error['message'], $error['file'], $error['line'], '');
    }
});

function render_error(string $type, string $message, string $file, int $line, string $trace): string {
    $file = htmlspecialchars($file);
    $message = htmlspecialchars($message);
    $type = htmlspecialchars($type);
    $trace = htmlspecialchars($trace);

    return "
    <div class='error-block'>
        <div class='error-type'>{$type}</div>
        <div class='error-message'>{$message}</div>
        <div class='error-meta'>Fichier : {$file} &mdash; Ligne : {$line}</div>
        " . ($trace ? "<pre class='error-trace'>{$trace}</pre>" : "") . "
    </div>
    ";
}

class DatabaseException extends RuntimeException {}
class ValidationException extends RuntimeException {
    private array $errors;

    public function __construct(array $errors) {
        parent::__construct('Erreur de validation');
        $this->errors = $errors;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}

function divide(int $a, int $b): float {
    if ($b === 0) {
        throw new DivisionByZeroError("Division par zéro interdite.");
    }
    return $a / $b;
}

function connectDatabase(string $dsn): void {
    if (!str_starts_with($dsn, 'mysql')) {
        throw new DatabaseException("DSN invalide : connexion impossible à '{$dsn}'.");
    }
}

function validateUser(array $data): void {
    $errors = [];
    if (empty($data['nom'])) {
        $errors[] = "Le champ 'nom' est requis.";
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email est invalide.";
    }
    if (!empty($errors)) {
        throw new ValidationException($errors);
    }
}

$scenarios = [
    [
        'titre' => 'Division valide',
        'fn' => fn() => divide(10, 2),
        'success' => fn($r) => "Résultat : {$r}",
    ],
    [
        'titre' => 'Division par zéro',
        'fn' => fn() => divide(10, 0),
        'success' => fn($r) => "Résultat : {$r}",
    ],
    [
        'titre' => 'Connexion base de données invalide',
        'fn' => fn() => connectDatabase('invalid://localhost'),
        'success' => fn($r) => "Connecté.",
    ],
    [
        'titre' => 'Validation utilisateur échouée',
        'fn' => fn() => validateUser(['nom' => '', 'email' => 'pas-un-email']),
        'success' => fn($r) => "Utilisateur valide.",
    ],
    [
        'titre' => 'Validation utilisateur réussie',
        'fn' => fn() => validateUser(['nom' => 'Mostafa', 'email' => 'mostafa@example.com']),
        'success' => fn($r) => "Utilisateur valide.",
    ],
];

$results = [];

foreach ($scenarios as $scenario) {
    try {
        $output = $scenario['fn']();
        $results[] = [
            'titre'  => $scenario['titre'],
            'status' => 'ok',
            'texte'  => $scenario['success']($output),
        ];
    } catch (ValidationException $e) {
        $results[] = [
            'titre'  => $scenario['titre'],
            'status' => 'warn',
            'texte'  => implode('<br>', array_map('htmlspecialchars', $e->getErrors())),
        ];
    } catch (DivisionByZeroError | DatabaseException $e) {
        $results[] = [
            'titre'  => $scenario['titre'],
            'status' => 'error',
            'texte'  => htmlspecialchars($e->getMessage()),
        ];
    } catch (Throwable $e) {
        $results[] = [
            'titre'  => $scenario['titre'],
            'status' => 'error',
            'texte'  => htmlspecialchars($e->getMessage()),
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP — Gestion des erreurs</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0e0e10;
            --surface:   #18181c;
            --border:    #2a2a30;
            --text:      #e4e4e8;
            --muted:     #6b6b75;
            --ok:        #3ddc97;
            --warn:      #f5a623;
            --err:       #ff5f5f;
            --ok-bg:     rgba(61,220,151,.08);
            --warn-bg:   rgba(245,166,35,.08);
            --err-bg:    rgba(255,95,95,.08);
            --accent:    #7c6af7;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
            padding: 48px 24px;
        }

        header {
            max-width: 760px;
            margin: 0 auto 48px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 28px;
        }

        header p {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: var(--accent);
            font-family: 'IBM Plex Mono', monospace;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: -.02em;
        }

        h1 span {
            color: var(--muted);
            font-weight: 300;
        }

        .grid {
            max-width: 760px;
            margin: 0 auto;
            display: grid;
            gap: 14px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            animation: fadeUp .35s ease both;
        }

        .card:nth-child(1) { animation-delay: .05s; }
        .card:nth-child(2) { animation-delay: .10s; }
        .card:nth-child(3) { animation-delay: .15s; }
        .card:nth-child(4) { animation-delay: .20s; }
        .card:nth-child(5) { animation-delay: .25s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
        }

        .badge {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 2px 8px;
            border-radius: 3px;
        }

        .ok   .badge { color: var(--ok);   background: var(--ok-bg);   }
        .warn .badge { color: var(--warn); background: var(--warn-bg); }
        .error .badge { color: var(--err); background: var(--err-bg);  }

        .card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .card-body {
            padding: 14px 18px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
        }

        .ok   .card-body { color: var(--ok);   }
        .warn .card-body { color: var(--warn); }
        .error .card-body { color: var(--err); }

        .error-block {
            max-width: 760px;
            margin: 0 auto 20px;
            background: var(--err-bg);
            border: 1px solid var(--err);
            border-radius: 6px;
            padding: 20px 24px;
        }
        .error-type    { font-weight: 700; color: var(--err); margin-bottom: 6px; }
        .error-message { font-family: 'IBM Plex Mono', monospace; font-size: 13px; color: var(--text); margin-bottom: 8px; }
        .error-meta    { font-size: 12px; color: var(--muted); margin-bottom: 10px; }
        .error-trace   { font-size: 11px; color: var(--muted); white-space: pre-wrap; border-top: 1px solid var(--border); padding-top: 10px; margin-top: 10px; }
    </style>
</head>
<body>

<header>
    <p>PHP &mdash; Error Handling</p>
    <h1>Gestion des <span>erreurs &amp; exceptions</span></h1>
</header>

<div class="grid">
<?php foreach ($results as $r): ?>
    <?php
        $cls = $r['status'] === 'ok' ? 'ok' : ($r['status'] === 'warn' ? 'warn' : 'error');
        $label = $r['status'] === 'ok' ? 'Succès' : ($r['status'] === 'warn' ? 'Avertissement' : 'Erreur');
    ?>
    <div class="card <?= $cls ?>">
        <div class="card-header">
            <span class="badge"><?= $label ?></span>
            <span class="card-title"><?= htmlspecialchars($r['titre']) ?></span>
        </div>
        <div class="card-body"><?= $r['texte'] ?></div>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
