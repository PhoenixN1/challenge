<?php

$langages = [
    ["nom" => "PHP", "version" => "8.3", "type" => "Backend"],
    ["nom" => "JavaScript", "version" => "ES2024", "type" => "Frontend"],
    ["nom" => "Python", "version" => "3.12", "type" => "Data Science"],
    ["nom" => "Rust", "version" => "1.78", "type" => "Système"],
    ["nom" => "TypeScript", "version" => "5.4", "type" => "Full Stack"],
    ["nom" => "Go", "version" => "1.22", "type" => "Backend"],
];

$index = 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP foreach — Langages</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
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

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Syne', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 20% 20%, rgba(0, 229, 160, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(255, 77, 109, 0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        .wrapper {
            width: 100%;
            max-width: 780px;
        }

        header {
            margin-bottom: 3rem;
        }

        .label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.6rem;
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        h1 span.even { color: var(--accent-even); }
        h1 span.odd  { color: var(--accent-odd); }

        .subtitle {
            margin-top: 0.8rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: var(--muted);
        }

        .grid {
            display: grid;
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .card {
            background: var(--surface);
            padding: 1.4rem 1.6rem;
            display: grid;
            grid-template-columns: 2rem 1fr auto;
            align-items: center;
            gap: 1.2rem;
            transition: background 0.2s;
            animation: fadeSlide 0.4s ease both;
        }

        .card:hover {
            background: #16161f;
        }

        .card.even { --accent: var(--accent-even); --glow: var(--glow-even); }
        .card.odd  { --accent: var(--accent-odd);  --glow: var(--glow-odd); }

        .card:hover {
            box-shadow: inset 4px 0 0 var(--accent), inset 0 0 40px var(--glow);
        }

        .index {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 700;
            opacity: 0.7;
        }

        .info { display: flex; flex-direction: column; gap: 0.2rem; }

        .nom {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .type {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .version {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            color: var(--accent);
            background: var(--glow);
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            white-space: nowrap;
            opacity: 0.85;
        }

        footer {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 0.2rem;
        }

        .count {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .count strong { color: var(--accent-even); }

        .code-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--muted);
        }

        .code-tag span { color: var(--accent-odd); }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        <?php foreach ($langages as $i => $lang): ?>
        .card:nth-child(<?= $i + 1 ?>) { animation-delay: <?= $i * 0.07 ?>s; }
        <?php endforeach; ?>
    </style>
</head>
<body>
<div class="wrapper">

    <header>
        <p class="label">// PHP foreach loop</p>
        <h1>
            <span class="even">Langages</span>
            <span class="odd"> &amp;</span><br>
            Versions
        </h1>
        <p class="subtitle">$langages — itération avec foreach</p>
    </header>

    <div class="grid">
        <?php foreach ($langages as $index => $lang): ?>
            <?php $classe = ($index % 2 === 0) ? 'even' : 'odd'; ?>
            <div class="card <?= $classe ?>">
                <span class="index"><?= str_pad($index, 2, '0', STR_PAD_LEFT) ?></span>
                <div class="info">
                    <span class="nom"><?= htmlspecialchars($lang['nom']) ?></span>
                    <span class="type"><?= htmlspecialchars($lang['type']) ?></span>
                </div>
                <span class="version">v<?= htmlspecialchars($lang['version']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        <p class="count"><strong><?= count($langages) ?></strong> langages chargés</p>
        <p class="code-tag"><span>foreach</span> ($langages as $index => $lang)</p>
    </footer>

</div>
</body>
</html>