<?php

$user = [
    'nom'          => 'Karim Benali',
    'poste'        => 'Développeur Full Stack',
    'entreprise'   => 'TechNova Solutions',
    'email'        => 'karim.benali@technova.io',
    'telephone'    => '+213 555 123 456',
    'localisation' => 'Alger, Algérie',
    'bio'          => 'Passionné par la création d\'applications web robustes et scalables. Spécialisé en PHP, MySQL et JavaScript avec plus de 5 ans d\'expérience dans des environnements agiles.',
    'photo'        => 'https://i.pravatar.cc/300?img=12',
    'competences'  => ['PHP', 'MySQL', 'JavaScript', 'Laravel', 'Vue.js', 'Docker'],
    'stats'        => [
        ['label' => 'Projets', 'valeur' => '48'],
        ['label' => 'Clients',  'valeur' => '23'],
        ['label' => 'Années',   'valeur' => '5+'],
    ],
    'reseaux' => [
        ['nom' => 'GitHub',   'url' => '#'],
        ['nom' => 'LinkedIn', 'url' => '#'],
        ['nom' => 'Twitter',  'url' => '#'],
    ],
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — <?= htmlspecialchars($user['nom']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --noir:      #0d0d0d;
            --blanc:     #f5f0e8;
            --accent:    #c8a96e;
            --gris:      #2a2a2a;
            --gris-clair:#3e3e3e;
            --texte-dim: #888;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--noir);
            color: var(--blanc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-image:
                radial-gradient(ellipse 60% 40% at 80% 10%, rgba(200,169,110,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 40% 50% at 10% 90%, rgba(200,169,110,0.05) 0%, transparent 60%);
        }

        .carte {
            width: 100%;
            max-width: 920px;
            background: var(--gris);
            border: 1px solid rgba(200,169,110,0.2);
            border-radius: 2px;
            display: grid;
            grid-template-columns: 320px 1fr;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6);
            animation: apparition 0.8s ease both;
        }

        @keyframes apparition {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .colonne-gauche {
            background: var(--noir);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            border-right: 1px solid rgba(200,169,110,0.15);
            position: relative;
        }

        .colonne-gauche::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }

        .cadre-photo {
            position: relative;
            width: 160px;
            height: 160px;
        }

        .cadre-photo::before {
            content: '';
            position: absolute;
            inset: -6px;
            border: 1px solid var(--accent);
            border-radius: 2px;
            transform: rotate(3deg);
        }

        .cadre-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: grayscale(20%);
            transition: filter 0.4s ease;
        }

        .cadre-photo:hover img {
            filter: grayscale(0%);
        }

        .badge-statut {
            position: absolute;
            bottom: -10px;
            right: -10px;
            background: var(--accent);
            color: var(--noir);
            font-size: 0.6rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 4px 10px;
        }

        .identite {
            text-align: center;
        }

        .identite h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--blanc);
            line-height: 1.2;
            margin-bottom: 0.4rem;
        }

        .identite .poste {
            font-size: 0.8rem;
            color: var(--accent);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 300;
        }

        .separateur {
            width: 40px;
            height: 1px;
            background: var(--accent);
            opacity: 0.5;
        }

        .info-contact {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .ligne-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .ligne-info span.etiquette {
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--texte-dim);
        }

        .ligne-info span.valeur {
            font-size: 0.85rem;
            color: var(--blanc);
            font-weight: 400;
        }

        .reseaux {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .reseaux a {
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--texte-dim);
            text-decoration: none;
            padding: 5px 12px;
            border: 1px solid var(--gris-clair);
            transition: all 0.25s ease;
        }

        .reseaux a:hover {
            color: var(--accent);
            border-color: var(--accent);
        }

        .colonne-droite {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .entete-droite {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .entete-droite h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            line-height: 1.1;
            color: var(--blanc);
        }

        .entete-droite h2 em {
            font-style: normal;
            color: var(--accent);
        }

        .entreprise-tag {
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--texte-dim);
            border: 1px solid var(--gris-clair);
            padding: 5px 12px;
            white-space: nowrap;
            margin-top: 6px;
        }

        .section-titre {
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .bio-texte {
            font-size: 0.9rem;
            font-style: italic;
            font-weight: 300;
            line-height: 1.8;
            color: #bbb;
            border-left: 2px solid var(--accent);
            padding-left: 1.2rem;
        }

        .stats-grille {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: rgba(200,169,110,0.1);
        }

        .stat-item {
            background: var(--gris);
            padding: 1.25rem;
            text-align: center;
        }

        .stat-item .chiffre {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--accent);
            line-height: 1;
        }

        .stat-item .libelle {
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--texte-dim);
            margin-top: 4px;
        }

        .competences-liste {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge-competence {
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            color: var(--blanc);
            background: rgba(200,169,110,0.1);
            border: 1px solid rgba(200,169,110,0.3);
            padding: 5px 14px;
            transition: all 0.2s ease;
            cursor: default;
        }

        .badge-competence:hover {
            background: rgba(200,169,110,0.2);
            border-color: var(--accent);
        }

        .bouton-contact {
            display: inline-block;
            margin-top: auto;
            align-self: flex-start;
            background: var(--accent);
            color: var(--noir);
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 14px 36px;
            transition: background 0.25s ease, color 0.25s ease;
        }

        .bouton-contact:hover {
            background: var(--blanc);
        }

        @media (max-width: 700px) {
            .carte {
                grid-template-columns: 1fr;
            }

            .colonne-gauche {
                border-right: none;
                border-bottom: 1px solid rgba(200,169,110,0.15);
            }

            .colonne-droite {
                padding: 2rem 1.5rem;
            }

            .entete-droite {
                flex-direction: column;
                gap: 1rem;
            }

            .stats-grille {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>

<div class="carte">

    <aside class="colonne-gauche">

        <div class="cadre-photo">
            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Photo de <?= htmlspecialchars($user['nom']) ?>">
            <span class="badge-statut">Disponible</span>
        </div>

        <div class="identite">
            <h1><?= htmlspecialchars($user['nom']) ?></h1>
            <p class="poste"><?= htmlspecialchars($user['poste']) ?></p>
        </div>

        <div class="separateur"></div>

        <div class="info-contact">
            <?php
            $champs = [
                'Email'        => $user['email'],
                'Téléphone'    => $user['telephone'],
                'Localisation' => $user['localisation'],
            ];
            foreach ($champs as $etiquette => $valeur):
            ?>
            <div class="ligne-info">
                <span class="etiquette"><?= $etiquette ?></span>
                <span class="valeur"><?= htmlspecialchars($valeur) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="separateur"></div>

        <nav class="reseaux">
            <?php foreach ($user['reseaux'] as $reseau): ?>
                <a href="<?= htmlspecialchars($reseau['url']) ?>" target="_blank" rel="noopener">
                    <?= htmlspecialchars($reseau['nom']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

    </aside>

    <main class="colonne-droite">

        <div class="entete-droite">
            <h2>Profil<br><em>Professionnel</em></h2>
            <span class="entreprise-tag"><?= htmlspecialchars($user['entreprise']) ?></span>
        </div>

        <section>
            <p class="section-titre">Biographie</p>
            <p class="bio-texte"><?= htmlspecialchars($user['bio']) ?></p>
        </section>

        <section>
            <p class="section-titre">Statistiques</p>
            <div class="stats-grille">
                <?php foreach ($user['stats'] as $stat): ?>
                <div class="stat-item">
                    <div class="chiffre"><?= htmlspecialchars($stat['valeur']) ?></div>
                    <div class="libelle"><?= htmlspecialchars($stat['label']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <p class="section-titre">Compétences</p>
            <div class="competences-liste">
                <?php foreach ($user['competences'] as $comp): ?>
                    <span class="badge-competence"><?= htmlspecialchars($comp) ?></span>
                <?php endforeach; ?>
            </div>
        </section>

        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="bouton-contact">
            Contacter
        </a>

    </main>

</div>

</body>
</html>
