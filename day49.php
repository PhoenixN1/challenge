<?php

$total_enregistrements = 85;
$par_page = 10;
$total_pages = ceil($total_enregistrements / $par_page);
$page_actuelle = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page_actuelle < 1) $page_actuelle = 1;
if ($page_actuelle > $total_pages) $page_actuelle = $total_pages;

$offset = ($page_actuelle - 1) * $par_page;

$donnees = [];
for ($i = 1; $i <= $total_enregistrements; $i++) {
    $donnees[] = [
        'id'  => $i,
        'nom' => 'Etudiant ' . $i,
        'note' => rand(50, 100)
    ];
}

$donnees_page = array_slice($donnees, $offset, $par_page);

function lien_page($page, $page_actuelle) {
    $classe = ($page == $page_actuelle) ? 'actif' : '';
    return '<a href="?page=' . $page . '" class="btn-page ' . $classe . '">' . $page . '</a>';
}

function construire_pagination($page_actuelle, $total_pages) {
    $html = '<div class="pagination">';

    if ($page_actuelle > 1) {
        $html .= '<a href="?page=' . ($page_actuelle - 1) . '" class="btn-nav">Precedent</a>';
    } else {
        $html .= '<span class="btn-nav desactive">Precedent</span>';
    }

    if ($total_pages <= 7) {
        for ($i = 1; $i <= $total_pages; $i++) {
            $html .= lien_page($i, $page_actuelle);
        }
    } else {
        $html .= lien_page(1, $page_actuelle);

        if ($page_actuelle > 4) {
            $html .= '<span class="points">...</span>';
        }

        $debut = max(2, $page_actuelle - 2);
        $fin   = min($total_pages - 1, $page_actuelle + 2);

        for ($i = $debut; $i <= $fin; $i++) {
            $html .= lien_page($i, $page_actuelle);
        }

        if ($page_actuelle < $total_pages - 3) {
            $html .= '<span class="points">...</span>';
        }

        $html .= lien_page($total_pages, $page_actuelle);
    }

    if ($page_actuelle < $total_pages) {
        $html .= '<a href="?page=' . ($page_actuelle + 1) . '" class="btn-nav">Suivant</a>';
    } else {
        $html .= '<span class="btn-nav desactive">Suivant</span>';
    }

    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagination</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background-color: #0f0f0f;
            color: #e8e0d0;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .conteneur {
            max-width: 860px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2rem;
            font-weight: normal;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #c9b98a;
            border-bottom: 1px solid #2a2a2a;
            padding-bottom: 16px;
            margin-bottom: 30px;
        }

        .info-page {
            font-size: 0.85rem;
            color: #6b6b6b;
            margin-bottom: 20px;
            letter-spacing: 0.04em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 36px;
        }

        thead tr {
            background-color: #1a1a1a;
            border-bottom: 1px solid #2f2f2f;
        }

        th {
            padding: 14px 18px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #c9b98a;
            font-weight: normal;
        }

        td {
            padding: 13px 18px;
            font-size: 0.92rem;
            border-bottom: 1px solid #1e1e1e;
            color: #c8c0b0;
        }

        tbody tr:hover {
            background-color: #161616;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-page,
        .btn-nav,
        .points {
            display: inline-block;
            padding: 8px 14px;
            font-size: 0.85rem;
            text-decoration: none;
            border: 1px solid #2a2a2a;
            color: #b0a890;
            background: #141414;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
            letter-spacing: 0.03em;
        }

        .btn-page:hover,
        .btn-nav:hover {
            background: #1f1f1f;
            border-color: #c9b98a;
            color: #c9b98a;
        }

        .btn-page.actif {
            background: #c9b98a;
            color: #0f0f0f;
            border-color: #c9b98a;
            font-weight: bold;
        }

        .btn-nav.desactive {
            color: #3a3a3a;
            border-color: #1e1e1e;
            cursor: default;
            pointer-events: none;
        }

        .points {
            border: none;
            background: transparent;
            color: #444;
            cursor: default;
            padding: 8px 6px;
        }
    </style>
</head>
<body>
<div class="conteneur">
    <h1>Liste des Etudiants</h1>

    <p class="info-page">
        Page <?= $page_actuelle ?> sur <?= $total_pages ?> — <?= $total_enregistrements ?> enregistrements au total
    </p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($donnees_page as $ligne): ?>
            <tr>
                <td><?= $ligne['id'] ?></td>
                <td><?= htmlspecialchars($ligne['nom']) ?></td>
                <td><?= $ligne['note'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?= construire_pagination($page_actuelle, $total_pages) ?>
</div>
</body>
</html>
