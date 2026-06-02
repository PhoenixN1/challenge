<?php
session_start();

$pdo = new PDO('sqlite:/tmp/blog_system.db', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        couleur TEXT DEFAULT '#6c63ff',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titre TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        contenu TEXT NOT NULL,
        extrait TEXT,
        categorie_id INTEGER,
        auteur TEXT NOT NULL,
        statut TEXT DEFAULT 'brouillon',
        date_publication DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (categorie_id) REFERENCES categories(id)
    );

    CREATE TABLE IF NOT EXISTS taches (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titre TEXT NOT NULL,
        description TEXT,
        article_id INTEGER,
        priorite TEXT DEFAULT 'normale',
        statut TEXT DEFAULT 'a_faire',
        date_echeance DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id)
    );
");

$countCat = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if ($countCat == 0) {
    $pdo->exec("
        INSERT INTO categories (nom, slug, couleur) VALUES
        ('Technologie', 'technologie', '#6c63ff'),
        ('Design', 'design', '#f7931e'),
        ('Actualite', 'actualite', '#e74c3c'),
        ('Tutoriels', 'tutoriels', '#2ecc71');

        INSERT INTO articles (titre, slug, contenu, extrait, categorie_id, auteur, statut, date_publication) VALUES
        ('Introduction au PHP moderne', 'introduction-php-moderne', 'Le PHP moderne a beaucoup evolue ces dernieres annees avec l introduction de nouvelles fonctionnalites comme les types, les attributs et les fibres. Cette evolution permet de creer des applications plus robustes et maintenables.', 'Decouvrez les nouvelles fonctionnalites du PHP moderne.', 1, 'Admin', 'publie', datetime('now', '-5 days')),
        ('Les tendances du design 2026', 'tendances-design-2026', 'Le design en 2026 est marque par le minimalisme expressif, les interfaces adaptatives et l utilisation intelligente de l espace negatif. Les designers privilegient l accessibilite et l experience utilisateur avant tout.', 'Les grandes tendances du design pour cette annee.', 2, 'Admin', 'publie', datetime('now', '-3 days')),
        ('Guide complet des bases de donnees', 'guide-bases-donnees', 'Les bases de donnees relationnelles restent la reference pour la gestion des donnees structurees. Ce guide couvre les concepts fondamentaux, la normalisation et les bonnes pratiques.', 'Tout ce que vous devez savoir sur les bases de donnees.', 4, 'Admin', 'brouillon', null),
        ('Nouvelle version de Laravel', 'nouvelle-version-laravel', 'La derniere version de Laravel apporte de nombreuses ameliorations en termes de performances et de nouvelles fonctionnalites tres attendues par la communaute.', 'Decouvrez les nouveautes de Laravel.', 1, 'Admin', 'planifie', datetime('now', '+7 days'));

        INSERT INTO taches (titre, description, article_id, priorite, statut, date_echeance) VALUES
        ('Rediger l introduction', 'Ecrire une introduction accrocheuse', 1, 'haute', 'termine', date('now', '-6 days')),
        ('Ajouter des exemples de code', 'Inclure des exemples pratiques', 1, 'haute', 'termine', date('now', '-4 days')),
        ('Relecture et correction', 'Verifier la grammaire et l orthographe', 1, 'normale', 'en_cours', date('now')),
        ('Creer les visuels', 'Preparer les images et schemas', 2, 'haute', 'termine', date('now', '-4 days')),
        ('Recherche documentaire', 'Collecter les references', 3, 'normale', 'en_cours', date('now', '+2 days')),
        ('Plan de l article', 'Definir la structure', 3, 'basse', 'a_faire', date('now', '+5 days')),
        ('Test des exemples', 'Verifier tous les exemples de code', 4, 'haute', 'a_faire', date('now', '+3 days')),
        ('Revue SEO', 'Optimiser pour les moteurs de recherche', 4, 'normale', 'a_faire', date('now', '+6 days'));
    ");
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

$action = $_GET['action'] ?? 'dashboard';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'save_article') {
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $extrait = trim($_POST['extrait'] ?? '');
        $categorie_id = $_POST['categorie_id'] ?: null;
        $auteur = trim($_POST['auteur'] ?? 'Admin');
        $statut = $_POST['statut'] ?? 'brouillon';
        $date_pub = $_POST['date_publication'] ?: null;
        $aid = $_POST['article_id'] ? (int)$_POST['article_id'] : null;

        if ($titre && $contenu) {
            $slug = slugify($titre);
            if ($aid) {
                $stmt = $pdo->prepare("UPDATE articles SET titre=?, slug=?, contenu=?, extrait=?, categorie_id=?, auteur=?, statut=?, date_publication=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
                $stmt->execute([$titre, $slug, $contenu, $extrait, $categorie_id, $auteur, $statut, $date_pub, $aid]);
                flash("Article mis a jour avec succes.");
            } else {
                $stmt = $pdo->prepare("INSERT INTO articles (titre, slug, contenu, extrait, categorie_id, auteur, statut, date_publication) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$titre, $slug, $contenu, $extrait, $categorie_id, $auteur, $statut, $date_pub]);
                flash("Article cree avec succes.");
            }
        }
        header("Location: ?action=articles");
        exit;
    }

    if ($postAction === 'delete_article') {
        $aid = (int)$_POST['article_id'];
        $pdo->prepare("DELETE FROM taches WHERE article_id=?")->execute([$aid]);
        $pdo->prepare("DELETE FROM articles WHERE id=?")->execute([$aid]);
        flash("Article supprime.");
        header("Location: ?action=articles");
        exit;
    }

    if ($postAction === 'save_tache') {
        $titre = trim($_POST['titre'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $article_id = $_POST['article_id'] ?: null;
        $priorite = $_POST['priorite'] ?? 'normale';
        $statut = $_POST['statut'] ?? 'a_faire';
        $echeance = $_POST['date_echeance'] ?: null;
        $tid = $_POST['tache_id'] ? (int)$_POST['tache_id'] : null;

        if ($titre) {
            if ($tid) {
                $stmt = $pdo->prepare("UPDATE taches SET titre=?, description=?, article_id=?, priorite=?, statut=?, date_echeance=? WHERE id=?");
                $stmt->execute([$titre, $desc, $article_id, $priorite, $statut, $echeance, $tid]);
                flash("Tache mise a jour.");
            } else {
                $stmt = $pdo->prepare("INSERT INTO taches (titre, description, article_id, priorite, statut, date_echeance) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$titre, $desc, $article_id, $priorite, $statut, $echeance]);
                flash("Tache ajoutee.");
            }
        }
        header("Location: ?action=planning");
        exit;
    }

    if ($postAction === 'delete_tache') {
        $pdo->prepare("DELETE FROM taches WHERE id=?")->execute([(int)$_POST['tache_id']]);
        flash("Tache supprimee.");
        header("Location: ?action=planning");
        exit;
    }

    if ($postAction === 'update_statut_tache') {
        $pdo->prepare("UPDATE taches SET statut=? WHERE id=?")->execute([$_POST['statut'], (int)$_POST['tache_id']]);
        header("Location: ?action=planning");
        exit;
    }

    if ($postAction === 'save_categorie') {
        $nom = trim($_POST['nom'] ?? '');
        $couleur = $_POST['couleur'] ?? '#6c63ff';
        if ($nom) {
            $slug = slugify($nom);
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (nom, slug, couleur) VALUES (?,?,?)");
            $stmt->execute([$nom, $slug, $couleur]);
            flash("Categorie ajoutee.");
        }
        header("Location: ?action=categories");
        exit;
    }

    if ($postAction === 'delete_categorie') {
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['categorie_id']]);
        flash("Categorie supprimee.");
        header("Location: ?action=categories");
        exit;
    }
}

$stats = [
    'total_articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'publies' => $pdo->query("SELECT COUNT(*) FROM articles WHERE statut='publie'")->fetchColumn(),
    'brouillons' => $pdo->query("SELECT COUNT(*) FROM articles WHERE statut='brouillon'")->fetchColumn(),
    'planifies' => $pdo->query("SELECT COUNT(*) FROM articles WHERE statut='planifie'")->fetchColumn(),
    'taches_total' => $pdo->query("SELECT COUNT(*) FROM taches")->fetchColumn(),
    'taches_terminees' => $pdo->query("SELECT COUNT(*) FROM taches WHERE statut='termine'")->fetchColumn(),
    'taches_en_cours' => $pdo->query("SELECT COUNT(*) FROM taches WHERE statut='en_cours'")->fetchColumn(),
    'taches_a_faire' => $pdo->query("SELECT COUNT(*) FROM taches WHERE statut='a_faire'")->fetchColumn(),
];

$flash = getFlash();

$navItems = [
    'dashboard' => 'Tableau de bord',
    'articles' => 'Articles',
    'planning' => 'Planning',
    'categories' => 'Categories',
];

$statusLabels = [
    'publie' => 'Publie',
    'brouillon' => 'Brouillon',
    'planifie' => 'Planifie',
];

$tacheStatutLabels = [
    'a_faire' => 'A faire',
    'en_cours' => 'En cours',
    'termine' => 'Termine',
];

$prioriteLabels = [
    'basse' => 'Basse',
    'normale' => 'Normale',
    'haute' => 'Haute',
];
?>
