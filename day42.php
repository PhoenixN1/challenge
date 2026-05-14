<?php
session_start();

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function check(array $input, array $rules): bool
    {
        $this->errors = [];
        $this->data   = [];

        foreach ($rules as $field => $ruleSet) {
            $value = isset($input[$field]) ? trim($input[$field]) : '';
            $ruleList = explode('|', $ruleSet);

            foreach ($ruleList as $rule) {
                if (str_contains($rule, ':')) {
                    [$ruleName, $param] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $param    = null;
                }

                $error = $this->applyRule($field, $value, $ruleName, $param);
                if ($error) {
                    $this->errors[$field][] = $error;
                    break;
                }
            }

            $this->data[$field] = $value;
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, string $value, string $rule, ?string $param): ?string
    {
        $label = ucfirst($field);

        return match ($rule) {
            'required'  => $value === '' ? "$label est requis." : null,
            'email'     => $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL) ? "Email invalide." : null,
            'min'       => mb_strlen($value) < (int)$param ? "$label doit contenir au moins $param caractères." : null,
            'max'       => mb_strlen($value) > (int)$param ? "$label ne peut pas dépasser $param caractères." : null,
            'minval'    => is_numeric($value) && (float)$value < (float)$param ? "$label doit être au moins $param." : null,
            'maxval'    => is_numeric($value) && (float)$value > (float)$param ? "$label ne peut pas dépasser $param." : null,
            'numeric'   => $value !== '' && !is_numeric($value) ? "$label doit être un nombre." : null,
            'alpha'     => $value !== '' && !preg_match('/^[\p{L}\s\-]+$/u', $value) ? "$label ne doit contenir que des lettres." : null,
            'alphanum'  => $value !== '' && !preg_match('/^[\p{L}0-9\s\-]+$/u', $value) ? "$label ne doit contenir que des lettres et chiffres." : null,
            default     => null,
        };
    }

    public function errors(): array  { return $this->errors; }
    public function data(): array    { return $this->data; }
    public function has(string $field): bool { return isset($this->errors[$field]); }
    public function first(string $field): string { return $this->errors[$field][0] ?? ''; }
}

class Security
{
    public static function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    public static function generateToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function regenerateToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

class RateLimiter
{
    private string $key;
    private int    $maxAttempts;
    private int    $decaySeconds;

    public function __construct(string $key, int $maxAttempts = 5, int $decaySeconds = 60)
    {
        $this->key          = 'rl_' . $key;
        $this->maxAttempts  = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function attempt(): bool
    {
        $this->cleanup();
        $attempts = $_SESSION[$this->key]['attempts'] ?? 0;

        if ($attempts >= $this->maxAttempts) {
            return false;
        }

        $_SESSION[$this->key]['attempts'] = $attempts + 1;
        $_SESSION[$this->key]['last']     = time();
        return true;
    }

    public function tooManyAttempts(): bool
    {
        $this->cleanup();
        return ($_SESSION[$this->key]['attempts'] ?? 0) >= $this->maxAttempts;
    }

    public function reset(): void
    {
        unset($_SESSION[$this->key]);
    }

    public function remainingSeconds(): int
    {
        $last = $_SESSION[$this->key]['last'] ?? 0;
        return max(0, $this->decaySeconds - (time() - $last));
    }

    private function cleanup(): void
    {
        $last = $_SESSION[$this->key]['last'] ?? 0;
        if (time() - $last > $this->decaySeconds) {
            $this->reset();
        }
    }
}

$validator   = new Validator();
$csrfToken   = Security::generateToken();
$rateLimiter = new RateLimiter('student_form', 5, 60);

$fields  = ['nom' => '', 'prenom' => '', 'email' => '', 'classe' => '', 'age' => ''];
$success = false;
$csrfError = false;
$rateLimitError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
        $csrfError = true;
    } elseif ($rateLimiter->tooManyAttempts()) {
        $rateLimitError = true;
    } else {

        $valid = $validator->check($_POST, [
            'nom'    => 'required|alpha|min:2|max:50',
            'prenom' => 'required|alpha|min:2|max:50',
            'email'  => 'required|email|max:100',
            'classe' => 'required|alphanum|min:1|max:30',
            'age'    => 'required|numeric|minval:5|maxval:100',
        ]);

        if ($valid) {
            $clean = array_map([Security::class, 'sanitize'], $validator->data());
            Security::regenerateToken();
            $csrfToken = Security::generateToken();
            $rateLimiter->reset();
            $success = true;
            $fields  = array_fill_keys(array_keys($fields), '');
        } else {
            $rateLimiter->attempt();
            foreach ($validator->data() as $key => $val) {
                $fields[$key] = Security::sanitize($val);
            }
        }
    }
}

function fieldClass(Validator $v, string $field): string
{
    if (empty($_POST)) return '';
    return $v->has($field) ? ' input-error' : ' input-valid';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation & Sécurité</title>
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
            --valid: #4a7c4a;
            --valid-bg: #f0f7f0;
            --valid-border: #8fc48f;
            --error: #8b2020;
            --error-bg: #fff5f5;
            --error-border: #e8a0a0;
            --warn-bg: #fffbf0;
            --warn-border: #e8c87a;
            --warn-text: #7a5c00;
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
            max-width: 640px;
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

        .breadcrumb a:hover { color: var(--accent); }

        .breadcrumb span { margin: 0 8px; }

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

        .security-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .badge {
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .badge-csrf     { background: #e8f0ff; color: #1a3a8b; }
        .badge-xss      { background: #f0f7e8; color: #2a5c1a; }
        .badge-ratelimit{ background: var(--accent-light); color: #5c3a00; }
        .badge-sanitize { background: #f0e8f7; color: #4a1a6b; }

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

        .card-body { padding: 40px; }

        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            font-size: 14px;
            margin-bottom: 28px;
            border-left: 3px solid;
            line-height: 1.6;
        }

        .alert-success {
            background: var(--valid-bg);
            border-color: var(--valid-border);
            color: var(--valid);
        }

        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error);
        }

        .alert-warn {
            background: var(--warn-bg);
            border-color: var(--warn-border);
            color: var(--warn-text);
        }

        .alert strong { font-weight: 600; }

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

        .form-group.full-width { grid-column: 1 / -1; }

        label {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .rule-hint {
            font-size: 11px;
            color: #b0aaa4;
            margin-top: 2px;
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
            border-color: var(--primary);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(26, 26, 46, 0.07);
        }

        input.input-error {
            border-color: var(--error-border);
            background: var(--error-bg);
        }

        input.input-valid {
            border-color: var(--valid-border);
            background: var(--valid-bg);
        }

        input::placeholder { color: #c0bdb8; }

        .field-error {
            font-size: 12px;
            color: var(--error);
            font-weight: 500;
            margin-top: 2px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 32px;
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

        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .security-panel {
            margin-top: 32px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .security-panel-header {
            padding: 12px 18px;
            background: var(--primary);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .security-panel-body {
            padding: 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .security-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .security-key {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .security-val {
            font-size: 12px;
            color: var(--text);
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .dot-active   { color: var(--valid); font-weight: 600; }
        .dot-inactive { color: var(--error); font-weight: 600; }

        @media (max-width: 540px) {
            .form-grid { grid-template-columns: 1fr; }
            .security-panel-body { grid-template-columns: 1fr; }
            .card-body { padding: 28px 22px; }
            .page-title { font-size: 30px; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <div class="page-header">
        <div class="breadcrumb">
            <a href="index.php">Étudiants</a>
            <span>/</span>
            Validation &amp; Sécurité
        </div>
        <h1 class="page-title">Validation &amp; <em>Sécurité</em></h1>
        <div class="security-badges">
            <span class="badge badge-csrf">CSRF Token</span>
            <span class="badge badge-xss">XSS Protection</span>
            <span class="badge badge-ratelimit">Rate Limiter</span>
            <span class="badge badge-sanitize">Sanitisation</span>
        </div>
    </div>

    <div class="card">
        <div class="card-accent-bar"></div>
        <div class="card-body">

            <?php if ($csrfError): ?>
                <div class="alert alert-error">
                    <strong>Token CSRF invalide.</strong> La requête a été rejetée pour des raisons de sécurité. Veuillez recharger la page.
                </div>
            <?php endif; ?>

            <?php if ($rateLimitError): ?>
                <div class="alert alert-warn">
                    <strong>Trop de tentatives.</strong> Veuillez patienter <?= $rateLimiter->remainingSeconds() ?> secondes avant de réessayer.
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    Formulaire validé et données assainies avec succès. Aucune donnée malveillante détectée.
                </div>
            <?php endif; ?>

            <form method="POST" action="validate.php" novalidate>

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="section-label">Informations étudiant</div>

                <div class="form-grid">

                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="<?= $fields['nom'] ?>"
                            placeholder="Nom de famille"
                            class="<?= fieldClass($validator, 'nom') ?>"
                        >
                        <span class="rule-hint">Lettres uniquement, 2–50 caractères</span>
                        <?php if ($validator->has('nom')): ?>
                            <span class="field-error"><?= $validator->first('nom') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input
                            type="text"
                            id="prenom"
                            name="prenom"
                            value="<?= $fields['prenom'] ?>"
                            placeholder="Prénom"
                            class="<?= fieldClass($validator, 'prenom') ?>"
                        >
                        <span class="rule-hint">Lettres uniquement, 2–50 caractères</span>
                        <?php if ($validator->has('prenom')): ?>
                            <span class="field-error"><?= $validator->first('prenom') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="age">Âge</label>
                        <input
                            type="number"
                            id="age"
                            name="age"
                            value="<?= $fields['age'] ?>"
                            placeholder="Ex: 20"
                            class="<?= fieldClass($validator, 'age') ?>"
                        >
                        <span class="rule-hint">Nombre entre 5 et 100</span>
                        <?php if ($validator->has('age')): ?>
                            <span class="field-error"><?= $validator->first('age') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="classe">Classe</label>
                        <input
                            type="text"
                            id="classe"
                            name="classe"
                            value="<?= $fields['classe'] ?>"
                            placeholder="Ex: Terminale A"
                            class="<?= fieldClass($validator, 'classe') ?>"
                        >
                        <span class="rule-hint">Lettres et chiffres, max 30 caractères</span>
                        <?php if ($validator->
