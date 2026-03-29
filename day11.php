<?php
$result = "";

function calcul($a, $b, $op) {
    if ($op === "+") return $a + $b;
    elseif ($op === "-") return $a - $b;
    elseif ($op === "*") return $a * $b;
    elseif ($op === "/") return $b != 0 ? $a / $b : "Erreur";
    elseif ($op === "%") return $b != 0 ? $a % $b : "Erreur";
    return "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $a = isset($_POST["a"]) ? (int)$_POST["a"] : 0;
    $b = isset($_POST["b"]) ? (int)$_POST["b"] : 0;
    $op = isset($_POST["op"]) ? $_POST["op"] : "";
    $result = calcul($a, $b, $op);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Calculatrice PHP</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Unbounded:wght@700;900&display=swap" rel="stylesheet">
<style>
:root {
    --bg:        #06060d;
    --surface:   #0e0e1c;
    --border:    #1c1c32;
    --accent:    #7b61ff;
    --accent2:   #f0c040;
    --text:      #dcdcf0;
    --muted:     #44445a;
    --glow:      rgba(123, 97, 255, 0.18);
    --glow2:     rgba(240, 192, 64, 0.14);
}

*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'DM Mono', monospace;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 55% 45% at 15% 15%, rgba(123,97,255,0.07) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 85% 85%, rgba(240,192,64,0.06) 0%, transparent 60%);
    pointer-events: none;
}

.container {
    width: 340px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    animation: rise 0.5s ease both;
    box-shadow: 0 0 60px var(--glow);
}

.title {
    font-family: 'Unbounded', sans-serif;
    font-size: 1rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    color: var(--accent);
    margin-bottom: 0.25rem;
}

.subtitle {
    font-size: 0.68rem;
    color: var(--muted);
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 1.6rem;
}

input[type="number"],
select,
button {
    width: 100%;
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
    border-radius: 8px;
    font-family: 'DM Mono', monospace;
    font-size: 0.88rem;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

input[type="number"],
select {
    background: var(--bg);
    border: 1px solid var(--border);
    color: var(--text);
}

input[type="number"]::placeholder { color: var(--muted); }

input[type="number"]:focus,
select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--glow);
}

select {
    appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%237b61ff' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

select option {
    background: #0e0e1c;
    color: var(--text);
}

button {
    background: var(--accent);
    color: #fff;
    border: none;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    margin-top: 1rem;
    box-shadow: 0 4px 20px var(--glow);
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

button:hover {
    background: #9580ff;
    box-shadow: 0 6px 28px rgba(123,97,255,0.35);
    transform: translateY(-1px);
}

button:active { transform: translateY(0); }

.result {
    margin-top: 1.4rem;
    padding: 1rem;
    border-radius: 8px;
    background: var(--bg);
    border: 1px solid var(--border);
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.result-label {
    font-size: 0.68rem;
    color: var(--muted);
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.result-value {
    font-family: 'Unbounded', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--accent2);
    text-shadow: 0 0 20px var(--glow2);
}

@keyframes rise {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="container">
    <p class="title">Calculatrice</p>
    <p class="subtitle">function calcul($a, $b, $op)</p>

    <form method="POST">
        <input type="number" name="a" placeholder="Nombre A" required>
        <input type="number" name="b" placeholder="Nombre B" required>
        <select name="op">
            <option value="+">Addition +</option>
            <option value="-">Soustraction −</option>
            <option value="*">Multiplication ×</option>
            <option value="/">Division ÷</option>
            <option value="%">Modulo %</option>
        </select>
        <button type="submit">Calculer</button>
    </form>

    <div class="result">
        <span class="result-label">Résultat</span>
        <?php if ($result !== ""): ?>
            <span class="result-value"><?= htmlspecialchars((string)$result) ?></span>
        <?php endif; ?>
    </div>
</div>

</body>
</html>