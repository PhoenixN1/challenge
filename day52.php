<?php

define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

function formatSize(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' Mo';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' Ko';
    return $bytes . ' o';
}

function formatDate(int $timestamp): string {
    $months = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
    return date('d', $timestamp) . ' ' . $months[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $filename = basename($_POST['delete']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($ext, ALLOWED_EXTENSIONS)) {
        $target = UPLOAD_DIR . $filename;
        if (file_exists($target)) {
            unlink($target);
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$images = [];
if (is_dir(UPLOAD_DIR)) {
    foreach (glob(UPLOAD_DIR . '*.{' . implode(',', ALLOWED_EXTENSIONS) . '}', GLOB_BRACE) as $path) {
        $size = filesize($path);
        $images[] = [
            'path'  => $path,
            'name'  => basename($path),
            'size'  => formatSize($size),
            'bytes' => $size,
            'date'  => formatDate(filemtime($path)),
            'mtime' => filemtime($path),
        ];
    }
}

$sort = $_GET['sort'] ?? 'date';
usort($images, match($sort) {
    'name' => fn($a, $b) => strcmp($a['name'], $b['name']),
    'size' => fn($a, $b) => $b['bytes'] - $a['bytes'],
    default => fn($a, $b) => $b['mtime'] - $a['mtime'],
});

$total     = count($images);
$totalSize = formatSize(array_sum(array_column($images, 'bytes')));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie d'images</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f7f4ef;
            --surface: #ffffff;
            --border: #e0dbd2;
            --border-dark: #c8c0b4;
            --text: #1a1814;
            --text-muted: #8a8278;
            --accent: #2d2926;
            --accent-warm: #b5603c;
            --tag-bg: #ede9e3;
            --overlay: rgba(20, 17, 14, 0.92);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-weight: 300;
            min-height: 100vh;
        }

        .noise {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 128px;
        }

        .page {
            position: relative;
            z-index: 1;
            max-width: 1240px;
            margin: 0 auto;
            padding: 64px 32px 96px;
        }

        header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 48px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--border-dark);
            flex-wrap: wrap;
        }

        .header-left h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.4rem, 5vw, 4rem);
            font-weight: 300;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .header-left h1 span {
            color: var(--accent-warm);
            font-style: italic;
        }

        .header-left p {
            margin-top: 10px;
            font-size: 0.82rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .stat-pill {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .stat-pill .stat-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 300;
            line-height: 1;
            color: var(--text);
        }

        .stat-pill .stat-label {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .divider-v {
            width: 1px;
            height: 40px;
            background: var(--border-dark);
        }

        .upload-link {
            display: inline-block;
            padding: 10px 24px;
            background: var(--accent);
            color: var(--bg);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-radius: 2px;
            transition: background 0.2s;
        }

        .upload-link:hover { background: var(--accent-warm); }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .sort-group {
            display: flex;
            gap: 4px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: 3px;
        }

        .sort-btn {
            padding: 6px 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.78rem;
            font-weight: 400;
            letter-spacing: 0.04em;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 2px;
            color: var(--text-muted);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .sort-btn.active, .sort-btn:hover {
            background: var(--accent);
            color: var(--bg);
        }

        .view-toggle {
            display: flex;
            gap: 4px;
        }

        .view-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 3px;
            cursor: pointer;
            color: var(--text-muted);
            transition: border-color 0.15s, color 0.15s;
        }

        .view-btn.active, .view-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .view-btn svg { width: 16px; height: 16px; fill: currentColor; }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .gallery-grid.list-view {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            transition: box-shadow 0.25s, border-color 0.25s, transform 0.25s;
            animation: fadeUp 0.4s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            border-color: var(--border-dark);
            transform: translateY(-2px);
        }

        .card-thumb {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            cursor: zoom-in;
            background: var(--tag-bg);
        }

        .list-view .card-thumb {
            aspect-ratio: unset;
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }

        .list-view .card {
            display: flex;
            align-items: center;
        }

        .card-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .card:hover .card-thumb img { transform: scale(1.06); }

        .card-thumb-overlay {
            position: absolute;
            inset: 0;
            background: rgba(20, 17, 14, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .card:hover .card-thumb-overlay { opacity: 1; }

        .zoom-label {
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #fff;
            font-weight: 400;
            border: 1px solid rgba(255,255,255,0.6);
            padding: 6px 14px;
            border-radius: 2px;
        }

        .card-body {
            padding: 14px 16px;
            flex: 1;
            min-width: 0;
        }

        .card-name {
            font-size: 0.82rem;
            font-weight: 400;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text);
            margin-bottom: 6px;
        }

        .card-meta {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .meta-tag {
            font-size: 0.7rem;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            background: var(--tag-bg);
            padding: 2px 8px;
            border-radius: 2px;
        }

        .card-actions {
            padding: 0 16px 14px;
            display: flex;
            gap: 8px;
        }

        .list-view .card-actions {
            padding: 0 16px 0 0;
        }

        .btn-view {
            flex: 1;
            padding: 7px 0;
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 2px;
            border: 1px solid var(--border-dark);
            background: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }

        .btn-view:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-delete {
            padding: 7px 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 2px;
            border: 1px solid transparent;
            background: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.15s, background 0.15s, border-color 0.15s;
        }

        .btn-delete:hover {
            background: #fff0ee;
            border-color: #e08070;
            color: #c0402a;
        }

        .empty-state {
            text-align: center;
            padding: 96px 0;
            color: var(--text-muted);
        }

        .empty-state p {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 300;
            font-style: italic;
            margin-bottom: 16px;
        }

        .empty-state a {
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent-warm);
            text-decoration: none;
        }

        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: var(--overlay);
            align-items: center;
            justify-content: center;
            padding: 24px;
            backdrop-filter: blur(6px);
        }

        .lightbox.open { display: flex; }

        .lightbox-inner {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lightbox-inner img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 3px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6);
            display: block;
        }

        .lightbox-caption {
            margin-top: 16px;
            text-align: center;
        }

        .lightbox-caption .lb-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            color: rgba(255,255,255,0.9);
        }

        .lightbox-caption .lb-meta {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.06em;
            margin-top: 4px;
        }

        .lightbox-close {
            position: fixed;
            top: 24px;
            right: 28px;
            background: none;
            border: 1px solid rgba(255,255,255,0.25);
            color: rgba(255,255,255,0.7);
            font-size: 1.2rem;
            line-height: 1;
            padding: 8px 14px;
            border-radius: 2px;
            cursor: pointer;
            letter-spacing: 0.06em;
            font-family: 'Outfit', sans-serif;
            transition: border-color 0.15s, color 0.15s;
        }

        .lightbox-close:hover { border-color: rgba(255,255,255,0.7); color: #fff; }

        .lb-nav {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
            font-size: 1.4rem;
            line-height: 1;
            padding: 12px 16px;
            cursor: pointer;
            border-radius: 2px;
            transition: border-color 0.15s, color 0.15s;
        }

        .lb-nav:hover { border-color: rgba(255,255,255,0.6); color: #fff; }
        .lb-prev { left: 20px; }
        .lb-next { right: 20px; }

        @media (max-width: 640px) {
            .page { padding: 40px 16px 64px; }
            header { flex-direction: column; align-items: flex-start; }
            .header-right { width: 100%; justify-content: space-between; }
            .lb-nav { display: none; }
        }
    </style>
</head>
<body>
<div class="noise"></div>
<div class="page">

    <header>
        <div class="header-left">
            <h1>Galerie <span>d'images</span></h1>
            <p>Collection &mdash; <?= date('Y') ?></p>
        </div>
        <div class="header-right">
            <div class="stat-pill">
                <span class="stat-value"><?= $total ?></span>
                <span class="stat-label">Images</span>
            </div>
            <div class="divider-v"></div>
            <div class="stat-pill">
                <span class="stat-value"><?= $totalSize ?></span>
                <span class="stat-label">Total</span>
            </div>
            <div class="divider-v"></div>
            <a href="upload.php" class="upload-link">Ajouter</a>
        </div>
    </header>

    <div class="toolbar">
        <div class="sort-group">
            <a href="?sort=date" class="sort-btn <?= $sort === 'date' ? 'active' : '' ?>">Date</a>
            <a href="?sort=name" class="sort-btn <?= $sort === 'name' ? 'active' : '' ?>">Nom</a>
            <a href="?sort=size" class="sort-btn <?= $sort === 'size' ? 'active' : '' ?>">Taille</a>
        </div>
        <div class="view-toggle">
            <button class="view-btn active" id="gridBtn" title="Grille">
                <svg viewBox="0 0 16 16"><rect x="0" y="0" width="7" height="7"/><rect x="9" y="0" width="7" height="7"/><rect x="0" y="9" width="7" height="7"/><rect x="9" y="9" width="7" height="7"/></svg>
            </button>
            <button class="view-btn" id="listBtn" title="Liste">
                <svg viewBox="0 0 16 16"><rect x="0" y="1" width="16" height="2"/><rect x="0" y="7" width="16" height="2"/><rect x="0" y="13" width="16" height="2"/></svg>
            </button>
        </div>
    </div>

    <?php if (empty($images)): ?>
        <div class="empty-state">
            <p>Aucune image téléchargée.</p>
            <a href="upload.php">Commencer l'upload</a>
        </div>
    <?php else: ?>
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($images as $i => $img): ?>
                <div class="card" style="animation-delay: <?= $i * 0.04 ?>s">
                    <div class="card-thumb" data-index="<?= $i ?>" onclick="openLightbox(<?= $i ?>)">
                        <img src="<?= htmlspecialchars($img['path']) ?>" alt="<?= htmlspecialchars($img['name']) ?>" loading="lazy">
                        <div class="card-thumb-overlay">
                            <span class="zoom-label">Agrandir</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-name"><?= htmlspecialchars($img['name']) ?></div>
                        <div class="card-meta">
                            <span class="meta-tag"><?= htmlspecialchars($img['size']) ?></span>
                            <span class="meta-tag"><?= htmlspecialchars($img['date']) ?></span>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-view" onclick="openLightbox(<?= $i ?>)">Voir</button>
                        <form method="POST" onsubmit="return confirm('Supprimer cette image ?')">
                            <input type="hidden" name="delete" value="<?= htmlspecialchars($img['name']) ?>">
                            <button type="submit" class="btn-delete">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<div class="lightbox" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">Fermer</button>
    <button class="lb-nav lb-prev" onclick="navLightbox(-1)">&#8592;</button>
    <div class="lightbox-inner">
        <img id="lbImg" src="" alt="">
        <div class="lightbox-caption">
            <div class="lb-name" id="lbName"></div>
            <div class="lb-meta" id="lbMeta"></div>
        </div>
    </div>
    <button class="lb-nav lb-next" onclick="navLightbox(1)">&#8594;</button>
</div>

<script>
    const images = <?= json_encode(array_values(array_map(fn($img) => [
        'path' => $img['path'],
        'name' => $img['name'],
        'size' => $img['size'],
        'date' => $img['date'],
    ], $images))) ?>;

    let current = 0;

    function openLightbox(index) {
        current = index;
        renderLightbox();
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        documen
