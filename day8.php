<?php
$result = "";
$pairs = [];
$impairs = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $number = isset($_POST["number"]) ? (int)$_POST["number"] : 0;
    for ($i = 1; $i <= $number; $i++) {
        if ($i % 2 == 0) {
            $pairs[] = $i;
            $result .= '<span class="badge even">Pair: ' . $i . '</span>';
        } else {
            $impairs[] = $i;
            $result .= '<span class="badge odd">Impair: ' . $i . '</span>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Loop If Challenge</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #12121a;
            --border: #1e1e2e;
            --accent-even: #00e5a0;
            --accent-odd: #ff4d6d;
            --text: #e8e8f0;
            --muted: #555570;
            --glow-even: rgba(0, 229, 160, 0.15);
            --glow-odd: rgba(255, 77, 109, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(0,229,160,0.04) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(255,77,109,0.04) 0%, transparent 50%);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 0 60px rgba(0,0,0,0.5);
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .header {
            margin-bottom: 2rem;
        }

        .tag {
            font-family: 'Space Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
            background: linear-gradient(135deg, var(--accent-even), var(--accent-odd));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-group {
            position: relative;
            margin-bottom: 1rem;
        }

        input[type="number"] {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Space Mono', monospace;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            -moz-appearance: textfield;
        }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }

        input[type="number"]:focus {
            border-color: var(--accent-even);
            box-shadow: 0 0 0 3px var(--glow-even);
        }

        input::placeholder { color: var(--muted); }

        button {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--accent-even), #00c4ff);
            border: none;
            border-radius: 10px;
            color: #0a0a0f;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }
        button:hover  { opacity: 0.9; transform: translateY(-1px); }
        button:active { transform: translateY(0); }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.8rem 0;
        }

        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .stat-box {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .stat-box.even { border-left: 3px solid var(--accent-even); }
        .stat-box.odd  { border-left: 3px solid var(--accent-odd); }

        .stat-label {
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 800;
        }
        .stat-box.even .stat-value { color: var(--accent-even); }
        .stat-box.odd  .stat-value { color: var(--accent-odd); }

        .result-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            font-family: 'Space Mono', monospace;
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            animation: pop 0.2s ease both;
        }

        @keyframes pop {
            from { opacity: 0; transform: scale(0.8); }
            to   { opacity: 1; transform: scale(1); }
        }

        .badge.even {
            background: var(--glow-even);
            color: var(--accent-even);
            border: 1px solid rgba(0,229,160,0.25);
        }

        .badge.odd {
            background: var(--glow-odd);
            color: var(--accent-odd);
            border: 1px solid rgba(255,77,109,0.25);
        }

        .empty {
            font-family: 'Space Mono', monospace;
            font-size: 0.8rem;
            color: var(--muted);
            text-align: center;
            padding: 1.5rem 0;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <p class="tag">Exercice PHP</p>
        <h1>Loop If<br>Challenge</h1>
    </div>

    <form method="post">
        <div class="form-group">
            <input type="number" name="number" placeholder="Entrer un nombre" min="1" value="<?php echo isset($_POST['number']) ? (int)$_POST['number'] : ''; ?>">
        </div>
        <button type="submit">Générer →</button>
    </form>

    <?php if (!empty($result)): ?>
    <hr class="divider">

    <div class="stats">
        <div class="stat-box even">
            <span class="stat-label">Pairs</span>
            <span class="stat-value"><?php echo count($pairs); ?></span>
        </div>
        <div class="stat-box odd">
            <span class="stat-label">Impairs</span>
            <span class="stat-value"><?php echo count($impairs); ?></span>
        </div>
    </div>

    <div class="result-grid">
        <?php echo $result; ?>
    </div>
    <?php else: ?>
    <hr class="divider">
    <p class="empty">// Entrez un nombre pour voir les résultats</p>
    <?php endif; ?>
</div>
</body>
</html>