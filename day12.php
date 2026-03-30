<?php
$result = "";
$names = ["Ali", "Sara", "Youssef", "Lina"];
$numbers = [10, 20, 30, 40];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = "ok";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>PHP Arrays</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Unbounded:wght@700;900&display=swap" rel="stylesheet">
<style>
:root {
    --bg:        #050510;
    --surface:   #0c0c1d;
    --surface2:  #10102a;
    --border:    #1a1a35;
    --accent:    #4f8eff;
    --accent2:   #ff6b35;
    --accent3:   #a8ff78;
    --text:      #d8d8f0;
    --muted:     #3a3a60;
    --glow:      rgba(79, 142, 255, 0.15);
    --glow2:     rgba(255, 107, 53, 0.12);
    --glow3:     rgba(168, 255, 120, 0.12);
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
    padding: 2rem;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 10% 10%, rgba(79,142,255,0.06) 0%, transparent 55%),
        radial-gradient(ellipse 50% 45% at 90% 90%, rgba(255,107,53,0.05) 0%, transparent 55%);
    pointer-events: none;
}

.container {
    width: 400px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 0 80px var(--glow);
    animation: rise 0.5s ease both;
}

.header { margin-bottom: 1.6rem; }

.title {
    font-family: 'Unbounded', sans-serif;
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--accent);
    letter-spacing: -0.02em;
}

.subtitle {
    font-size: 0.66rem;
    color: var(--muted);
    letter-spacing: 0.14em;
    text-transform: uppercase;
    margin-top: 0.25rem;
}

button {
    width: 100%;
    padding: 0.85rem;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Unbounded', sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 4px 24px var(--glow);
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

button:hover {
    background: #6fa3ff;
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(79,142,255,0.3);
}

button:active { transform: translateY(0); }

.result { margin-top: 1.6rem; display: flex; flex-direction: column; gap: 1.2rem; }

.block {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}

.block-header {
    padding: 0.6rem 1rem;
    font-size: 0.62rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    border-bottom: 1px solid var(--border);
    font-weight: 500;
}

.block.names .block-header  { color: var(--accent);  background: rgba(79,142,255,0.05); }
.block.numbers .block-header { color: var(--accent2); background: rgba(255,107,53,0.05); }
.block.total .block-header   { color: var(--accent3); background: rgba(168,255,120,0.05); }

.items { padding: 0.6rem 0; }

.item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.45rem 1rem;
    font-size: 0.85rem;
    transition: background 0.15s;
}

.item:hover { background: rgba(255,255,255,0.03); }

.item-index {
    font-size: 0.62rem;
    color: var(--muted);
    min-width: 1.4rem;
}

.block.names  .item-value { color: var(--accent); }
.block.numbers .item-value { color: var(--accent2); }

.total-value {
    padding: 0.9rem 1rem;
    font-family: 'Unbounded', sans-serif;
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--accent3);
    text-shadow: 0 0 24px var(--glow3);
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.total-value span {
    font-family: 'DM Mono', monospace;
    font-size: 0.65rem;
    color: var(--muted);
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-shadow: none;
}

@keyframes rise {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

.item { animation: fadeIn 0.3s ease both; }
<?php foreach ($names as $i => $n): ?>
.block.names .item:nth-child(<?= $i+1 ?>) { animation-delay: <?= $i*0.07 ?>s; }
<?php endforeach; ?>
<?php foreach ($numbers as $i => $n): ?>
.block.numbers .item:nth-child(<?= $i+1 ?>) { animation-delay: <?= ($i+count($names))*0.07 ?>s; }
<?php endforeach; ?>

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(-6px); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <p class="title">PHP Arrays</p>
        <p class="subtitle">foreach — array_sum</p>
    </div>

    <form method="POST">
        <button type="submit">Afficher Arrays</button>
    </form>

    <?php if ($result === "ok"): ?>
    <div class="result">

        <div class="block names">
            <div class="block-header">$names — <?= count($names) ?> éléments</div>
            <div class="items">
                <?php foreach ($names as $i => $name): ?>
                <div class="item">
                    <span class="item-index"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="item-value"><?= htmlspecialchars($name) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="block numbers">
            <div class="block-header">$numbers — <?= count($numbers) ?> éléments</div>
            <div class="items">
                <?php foreach ($numbers as $i => $num): ?>
                <div class="item">
                    <span class="item-index"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="item-value"><?= $num ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="block total">
            <div class="block-header">array_sum($numbers)</div>
            <div class="total-value">
                <?= array_sum($numbers) ?>
                <span>total</span>
            </div>
        </div>

    </div>
    <?php endif; ?>
</div>

</body>
</html>