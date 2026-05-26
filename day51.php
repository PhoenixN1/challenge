<?php

define('UPLOAD_DIR', 'uploads/');
define('MAX_SIZE', 5 * 1024 * 1024);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$message = '';
$messageType = '';
$uploadedFiles = [];

function validateFile(array $file): array {
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = match($file['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée.',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier sélectionné.',
            default => 'Erreur inconnue lors du téléchargement.'
        };
        return $errors;
    }

    if ($file['size'] > MAX_SIZE) {
        $errors[] = 'Le fichier "' . htmlspecialchars($file['name']) . '" dépasse 5 Mo.';
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_TYPES)) {
        $errors[] = 'Le type de fichier "' . htmlspecialchars($file['name']) . '" n\'est pas autorisé.';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $errors[] = 'L\'extension ".' . htmlspecialchars($ext) . '" n\'est pas autorisée.';
    }

    return $errors;
}

function generateFilename(string $originalName): string {
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
}

function formatSize(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' Mo';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' Ko';
    return $bytes . ' o';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        $message = 'Veuillez sélectionner au moins une image.';
        $messageType = 'error';
    } else {
        $files = $_FILES['images'];
        $count = count($files['name']);
        $errors = [];
        $successes = [];

        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            $fileErrors = validateFile($file);

            if (!empty($fileErrors)) {
                $errors = array_merge($errors, $fileErrors);
            } else {
                $newFilename = generateFilename($file['name']);
                $destination = UPLOAD_DIR . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $successes[] = [
                        'original' => $file['name'],
                        'saved'    => $newFilename,
                        'size'     => formatSize($file['size']),
                        'path'     => $destination,
                    ];
                } else {
                    $errors[] = 'Impossible de déplacer le fichier "' . htmlspecialchars($file['name']) . '".';
                }
            }
        }

        $uploadedFiles = $successes;

        if (empty($errors)) {
            $message = count($successes) . ' image(s) téléchargée(s) avec succès.';
            $messageType = 'success';
        } elseif (!empty($successes)) {
            $message = count($successes) . ' image(s) téléchargée(s), mais ' . count($errors) . ' erreur(s) rencontrée(s).';
            $messageType = 'warning';
        } else {
            $message = implode(' ', $errors);
            $messageType = 'error';
        }
    }
}

$existingFiles = [];
if (is_dir(UPLOAD_DIR)) {
    foreach (glob(UPLOAD_DIR . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $filePath) {
        $existingFiles[] = [
            'path' => $filePath,
            'name' => basename($filePath),
            'size' => formatSize(filesize($filePath)),
        ];
    }
    usort($existingFiles, fn($a, $b) => filemtime($b['path']) - filemtime($a['path']));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire d'images</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0d0d0d;
            --surface: #161616;
            --surface-2: #1f1f1f;
            --border: #2a2a2a;
            --accent: #c8f060;
            --accent-dim: rgba(200, 240, 96, 0.12);
            --text: #f0ede8;
            --text-muted: #888;
            --error: #ff6b6b;
            --success: #6bffb8;
            --warning: #ffd96b;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            padding: 60px 24px;
        }

        .wrapper {
            max-width: 860px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 56px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 32px;
        }

        header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 400;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        header h1 em {
            color: var(--accent);
            font-style: italic;
        }

        header p {
            margin-top: 12px;
            color: var(--text-muted);
            font-size: 0.9rem;
            letter-spacing: 0.01em;
        }

        .upload-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 40px;
            margin-bottom: 32px;
        }

        .drop-zone {
            border: 1.5px dashed var(--border);
            border-radius: 4px;
            padding: 60px 40px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            position: relative;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: var(--accent);
            background: var(--accent-dim);
        }

        .drop-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .drop-zone-label {
            pointer-events: none;
        }

        .drop-zone-label .main-text {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            font-weight: 400;
            display: block;
            margin-bottom: 8px;
        }

        .drop-zone-label .sub-text {
            font-size: 0.82rem;
            color: var(--text-muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .file-preview-list {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .file-preview-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 0.85rem;
        }

        .file-preview-item .file-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .file-preview-item .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-preview-item .file-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text);
        }

        .file-preview-item .file-size {
            color: var(--text-muted);
            font-size: 0.78rem;
            margin-top: 2px;
        }

        .file-preview-item .remove-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 4px;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .file-preview-item .remove-btn:hover {
            color: var(--error);
        }

        .form-footer {
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .limits-info {
            font-size: 0.78rem;
            color: var(--text-muted);
            letter-spacing: 0.03em;
        }

        .submit-btn {
            background: var(--accent);
            color: #0d0d0d;
            border: none;
            padding: 12px 32px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: 0.88rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-radius: 2px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .submit-btn:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .message {
            padding: 16px 20px;
            border-radius: 4px;
            font-size: 0.88rem;
            margin-bottom: 32px;
            border-left: 3px solid;
        }

        .message.success {
            background: rgba(107, 255, 184, 0.08);
            border-color: var(--success);
            color: var(--success);
        }

        .message.error {
            background: rgba(255, 107, 107, 0.08);
            border-color: var(--error);
            color: var(--error);
        }

        .message.warning {
            background: rgba(255, 217, 107, 0.08);
            border-color: var(--warning);
            color: var(--warning);
        }

        .gallery-section h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            font-weight: 400;
            margin-bottom: 24px;
            letter-spacing: -0.01em;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }

        .gallery-item {
            position: relative;
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            background: var(--surface);
            aspect-ratio: 1;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.04);
        }

        .gallery-item-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
            padding: 20px 10px 10px;
            transform: translateY(100%);
            transition: transform 0.25s ease;
        }

        .gallery-item:hover .gallery-item-overlay {
            transform: translateY(0);
        }

        .gallery-item-overlay .img-name {
            font-size: 0.75rem;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .gallery-item-overlay .img-size {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .empty-gallery {
            color: var(--text-muted);
            font-size: 0.88rem;
            padding: 32px 0;
            text-align: center;
            border: 1px dashed var(--border);
            border-radius: 4px;
        }

        .progress-bar-wrap {
            height: 3px;
            background: var(--border);
            border-radius: 2px;
            margin-top: 16px;
            overflow: hidden;
            display: none;
        }

        .progress-bar {
            height: 100%;
            background: var(--accent);
            width: 0%;
            transition: width 0.3s;
        }

        @media (max-width: 600px) {
            .upload-section { padding: 24px 20px; }
            .drop-zone { padding: 40px 20px; }
            .form-footer { flex-direction: column; align-items: flex-start; }
            .submit-btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <header>
        <h1>Gestionnaire <em>d'images</em></h1>
        <p>Téléchargement sécurisé &mdash; JPG, PNG, GIF, WEBP &mdash; max 5 Mo par fichier</p>
    </header>

    <?php if ($message): ?>
        <div class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="upload-section">
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="drop-zone" id="dropZone">
                <input type="file" name="images[]" id="fileInput" multiple accept="image/jpeg,image/png,image/gif,image/webp">
                <div class="drop-zone-label">
                    <span class="main-text">Déposer vos images ici</span>
                    <span class="sub-text">ou cliquer pour sélectionner</span>
                </div>
            </div>

            <div class="file-preview-list" id="previewList"></div>

            <div class="progress-bar-wrap" id="progressWrap">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <div class="form-footer">
                <span class="limits-info">Formats acceptés : JPG, PNG, GIF, WEBP &nbsp;|&nbsp; Taille max : 5 Mo</span>
                <button type="submit" class="submit-btn" id="submitBtn">Télécharger</button>
            </div>
        </form>
    </div>

    <div class="gallery-section">
        <h2>Images téléchargées</h2>

        <?php if (empty($existingFiles)): ?>
            <div class="empty-gallery">Aucune image pour le moment.</div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($existingFiles as $file): ?>
                    <div class="gallery-item">
                        <img src="<?= htmlspecialchars($file['path']) ?>" alt="<?= htmlspecialchars($file['name']) ?>" loading="lazy">
                        <div class="gallery-item-overlay">
                            <div class="img-name"><?= htmlspecialchars($file['name']) ?></div>
                            <div class="img-size"><?= htmlspecialchars($file['size']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
    const dropZone   = document.getElementById('dropZone');
    const fileInput  = document.getElementById('fileInput');
    const previewList = document.getElementById('previewList');
    const progressWrap = document.getElementById('progressWrap');
    const progressBar  = document.getElementById('progressBar');
    const uploadForm   = document.getElementById('uploadForm');

    let selectedFiles = [];

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        addFiles(Array.from(e.dataTransfer.files));
    });

    fileInput.addEventListener('change', () => {
        addFiles(Array.from(fileInput.files));
        fileInput.value = '';
    });

    function addFiles(files) {
        const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        files.forEach(file => {
            if (allowed.includes(file.type) && !selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                selectedFiles.push(file);
            }
        });
        renderPreviews();
    }

    function renderPreviews() {
        previewList.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div');
                item.className = 'file-preview-item';
                item.innerHTML = `
                    <img class="file-thumb" src="${e.target.result}" alt="">
                    <div class="file-info">
                        <div class="file-name">${escHtml(file.name)}</div>
                        <div class="file-size">${formatSize(file.size)}</div>
                    </div>
                    <button type="button" class="remove-btn" data-index="${index}">&times;</button>
                `;
                item.querySelector('.remove-btn').addEventListener('click', () => {
                    selectedFiles.splice(index, 1);
                    renderPreviews();
                });
                previewList.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    uploadForm.addEventListener('submit', function(e) {
        if (selectedFiles.length === 0) return;
        e.preventDefault();

        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;

        progressWrap.style.display = 'block';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href);

        xhr.upload.addEventListener('progress', ev => {
            if (ev.lengthComputable) {
                progressBar.style.width = Math.round((ev.loaded / ev.total) * 100) + '%';
            }
        });

        xhr.addEventListener('load', () => {
            document.open();
            document.write(xhr.responseText);
            document.close();
        });

        const formData = new FormData(this);
        xhr.send(formData);
    });

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' Mo';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' Ko';
        return bytes + ' o';
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
</script>
</body>
</html>
