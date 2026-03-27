<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>While Loop Challenge</title>
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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: var(--bg);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    background: var(--surface);
    padding: 40px 35px;
    border-radius: 16px;
    width: 340px;
    text-align: center;
    border: 1px solid var(--border);
    box-shadow: 0 0 40px var(--glow-even);
}

h2 {
    font-size: 22px;
    color: var(--accent-even);
    margin-bottom: 24px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

input[type="number"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 15px;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
    color: var(--text);
    background: var(--bg);
    margin-bottom: 14px;
}

input[type="number"]:focus {
    border-color: var(--accent-even);
    box-shadow: 0 0 0 3px var(--glow-even);
}

button {
    width: 100%;
    padding: 12px;
    background: transparent;
    color: var(--accent-odd);
    border: 2px solid var(--accent-odd);
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
}

button:hover {
    background: var(--glow-odd);
    box-shadow: 0 0 16px var(--glow-odd);
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

.result {
    margin-top: 20px;
    max-height: 200px;
    overflow-y: auto;
    padding: 14px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--accent-even);
    font-size: 15px;
    line-height: 1.8;
}

.result::-webkit-scrollbar {
    width: 4px;
}

.result::-webkit-scrollbar-track {
    background: var(--border);
    border-radius: 4px;
}

.result::-webkit-scrollbar-thumb {
    background: var(--muted);
    border-radius: 4px;
}
</style>
</head>
<body>

<div class="container">
    <h2>While Loop</h2>
    <form method="POST">
        <input type="number" name="number" placeholder="Entrer un nombre" required>
        <button type="submit">Afficher</button>
    </form>

    <div class="result">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $n = (int) $_POST["number"];
            $i = 1;
            while ($i <= $n) {
                echo $i . "<br>";
                $i++;
            }
        }
        ?>
    </div>
</div>

</body>
</html>