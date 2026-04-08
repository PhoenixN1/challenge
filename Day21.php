<?php
$uploadDir = 'uploads/';
$message = '';
$messageType = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $files = $_FILES['files'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'text/plain', 'application/zip'];
    $maxSize = 10 * 1024 * 1024;
    $uploaded = 0;
    $errors = [];

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = $files['name'][$i] . ': Erreur lors du transfert.';
            continue;
        }

        if ($files['size'][$i] > $maxSize) {
            $errors[] = $files['name'][$i] . ': Fichier trop volumineux (max 10 Mo).';
            continue;
        }

        if (!in_array($files['type'][$i], $allowedTypes)) {
            $errors[] = $files['name'][$i] . ': Type de fichier non autorise.';
            continue;
        }

        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $newName = uniqid('file_', true) . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
            $uploaded++;
        } else {
            $errors[] = $files['name'][$i] . ': Echec du deplacement du fichier.';
        }
    }

    if ($uploaded > 0 && empty($errors)) {
        $message = $uploaded . ' fichier(s) uploade(s) avec succes.';
        $messageType = 'success';
    } elseif ($uploaded > 0 && !empty($errors)) {
        $message = $uploaded . ' fichier(s) uploade(s). Erreurs : ' . implode(' | ', $errors);
        $messageType = 'warning';
    } else {
        $message = 'Echec : ' . implode(' | ', $errors);
        $messageType = 'error';
    }
}

$uploadedFiles = is_dir($uploadDir) ? array_diff(scandir($uploadDir), ['.', '..']) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Fichiers</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: #fff; border-radius: 12px; padding: 40px; width: 100%; max-width: 700px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h1 { font-size: 1.6rem; color: #1a1a2e; margin-bottom: 8px; }
        p.subtitle { color: #666; font-size: 0.9rem; margin-bottom: 30px; }
        .drop-zone { border: 2px dashed #c0c8d8; border-radius: 10px; padding: 50px 20px; text-align: center; cursor: pointer; transition: all 0.3s; background: #fafbfc; }
        .drop-zone:hover, .drop-zone.active { border-color: #4f8ef7; background: #f0f5ff; }
        .drop-zone p { color: #888; font-size: 0.95rem; }
        .drop-zone span { color: #4f8ef7; font-weight: 600; cursor: pointer; }
        input[type="file"] { display: none; }
        .file-list { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
        .file-item { display: flex; justify-content: space-between; align-items: center; background: #f5f7fa; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; color: #333; }
        .file-item span { color: #999; font-size: 0.8rem; }
        .file-item button { background: none; border: none; color: #e55; cursor: pointer; font-size: 0.85rem; }
        .btn-upload { margin-top: 24px; width: 100%; padding: 14px; background: #4f8ef7; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-upload:hover { background: #3a7ae0; }
        .message { margin-top: 20px; padding: 14px 18px; border-radius: 8px; font-size: 0.9rem; }
        .success { background: #e6f9f0; color: #1a7a4a; border-left: 4px solid #2ecc71; }
        .warning { background: #fff8e1; color: #856404; border-left: 4px solid #f0ad4e; }
        .error { background: #fdecea; color: #b71c1c; border-left: 4px solid #e74c3c; }
        .uploaded-section { margin-top: 36px; }
        .uploaded-section h2 { font-size: 1.1rem; color: #333; margin-bottom: 14px; }
        .uploaded-list { display: flex; flex-direction: column; gap: 8px; }
        .uploaded-item { display: flex; justify-content: space-between; align-items: center; background: #f9fafb; border: 1px solid #e8eaf0; border-radius: 8px; padding: 10px 16px; font-size: 0.87rem; color: #444; }
        .uploaded-item a { color: #4f8ef7; text-decoration: none; font-size: 0.82rem; }
        .uploaded-item a:hover { text-decoration: underline; }
        .progress-bar { width: 100%; background: #e8eaf0; border-radius: 6px; height: 6px; margin-top: 16px; display: none; }
        .progress-bar div { height: 100%; background: #4f8ef7; border-radius: 6px; width: 0%; transition: width 0.4s; }
    </style>
</head>
<body>
<div class="container">
    <h1>Upload de Fichiers</h1>
    <p class="subtitle">Formats acceptes : JPG, PNG, GIF, WEBP, PDF, TXT, ZIP. Taille max : 10 Mo par fichier.</p>

    <?php if ($message): ?>
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="drop-zone" id="dropZone">
            <p>Glissez-deposez vos fichiers ici ou <span onclick="document.getElementById('fileInput').click()">parcourir</span></p>
            <input type="file" name="files[]" id="fileInput" multiple>
        </div>

        <div class="file-list" id="fileList"></div>

        <div class="progress-bar" id="progressBar">
            <div id="progressFill"></div>
        </div>

        <button type="submit" class="btn-upload">Envoyer les fichiers</button>
    </form>

    <?php if (!empty($uploadedFiles)): ?>
        <div class="uploaded-section">
            <h2>Fichiers uploadés (<?= count($uploadedFiles) ?>)</h2>
            <div class="uploaded-list">
                <?php foreach ($uploadedFiles as $file): ?>
                    <div class="uploaded-item">
                        <span><?= htmlspecialchars($file) ?></span>
                        <a href="<?= $uploadDir . htmlspecialchars($file) ?>" target="_blank">Voir</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    let selectedFiles = new DataTransfer();

    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('active'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('active'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('active');
        addFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => addFiles(fileInput.files));

    function addFiles(files) {
        for (const file of files) selectedFiles.items.add(file);
        fileInput.files = selectedFiles.files;
        renderList();
    }

    function removeFile(index) {
        const dt = new DataTransfer();
        const files = Array.from(selectedFiles.files);
        files.splice(index, 1);
        files.forEach(f => dt.items.add(f));
        selectedFiles = dt;
        fileInput.files = selectedFiles.files;
        renderList();
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' o';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
        return (bytes / 1048576).toFixed(1) + ' Mo';
    }

    function renderList() {
        fileList.innerHTML = '';
        Array.from(selectedFiles.files).forEach((file, i) => {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `<div><strong>${file.name}</strong></div><span>${formatSize(file.size)}</span><button type="button" onclick="removeFile(${i})">Supprimer</button>`;
            fileList.appendChild(div);
        });
    }

    document.getElementById('uploadForm').addEventListener('submit', () => {
        const bar = document.getElementById('progressBar');
        const fill = document.getElementById('progressFill');
        bar.style.display = 'block';
        let w = 0;
        const interval = setInterval(() => {
            w = Math.min(w + 10, 90);
            fill.style.width = w + '%';
            if (w >= 90) clearInterval(interval);
        }, 200);
    });
</script>
</body>
</html>
