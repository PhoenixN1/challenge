<?php

ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800);

session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ecole_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

function regenererSession() {
    session_regenerate_id(true);
    $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['derniere_activite'] = time();
}

function verifierExpiration() {
    $duree_max = 1800;
    if (isset($_SESSION['derniere_activite'])) {
        if (time() - $_SESSION['derniere_activite'] > $duree_max) {
            detruireSession();
            header("Location: login.php?raison=expiration");
            exit();
        }
    }
    $_SESSION['derniere_activite'] = time();
}

function detruireSession() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}

function verifierCsrf($token) {
    if (!isset($_SESSION['token_csrf']) || !hash_equals($_SESSION['token_csrf'], $token)) {
        die("Erreur CSRF : requête non autorisée.");
    }
}

function connecterUtilisateur($conn, $email, $mot_de_passe) {
    $stmt = $conn->prepare("SELECT id, nom, prenom, role, mot_de_passe FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultat = $stmt->get_result();

    if ($resultat->num_rows === 1) {
        $utilisateur = $resultat->fetch_assoc();
        if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['utilisateur_id']  = $utilisateur['id'];
            $_SESSION['nom']             = $utilisateur['nom'];
            $_SESSION['prenom']          = $utilisateur['prenom'];
            $_SESSION['role']            = $utilisateur['role'];
            $_SESSION['token_csrf']      = bin2hex(random_bytes(32));
            $_SESSION['derniere_activite'] = time();
            $_SESSION['ip']              = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent']      = $_SERVER['HTTP_USER_AGENT'];
            return true;
        }
    }
    return false;
}

function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

function verifierRole($role_requis) {
    if (!estConnecte() || $_SESSION['role'] !== $role_requis) {
        header("Location: acces_refuse.php");
        exit();
    }
}

function verifierEmpreinte() {
    if (
        isset($_SESSION['ip']) && $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
        isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
    ) {
        detruireSession();
        header("Location: login.php?raison=securite");
        exit();
    }
}

function stockerDansSession($cle, $valeur) {
    $_SESSION['data'][$cle] = $valeur;
}

function lireDepuisSession($cle, $defaut = null) {
    return $_SESSION['data'][$cle] ?? $defaut;
}

function supprimerCleSession($cle) {
    unset($_SESSION['data'][$cle]);
}

function ajouterFlash($type, $message) {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function afficherFlash() {
    if (!empty($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $flash) {
            echo "<div class='alerte alerte-" . htmlspecialchars($flash['type']) . "'>";
            echo htmlspecialchars($flash['message']);
            echo "</div>";
        }
        unset($_SESSION['flash']);
    }
}

function sauvegarderRecherche($criteres) {
    $_SESSION['recherche_etudiants'] = $criteres;
}

function restaurerRecherche() {
    return $_SESSION['recherche_etudiants'] ?? [];
}

function sauvegarderPagination($page, $par_page = 10) {
    $_SESSION['pagination'] = ['page' => $page, 'par_page' => $par_page];
}

function restaurerPagination() {
    return $_SESSION['pagination'] ?? ['page' => 1, 'par_page' => 10];
}

if (estConnecte()) {
    verifierExpiration();
    verifierEmpreinte();
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'connexion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $mdp   = $_POST['mot_de_passe'] ?? '';

            if (connecterUtilisateur($conn, $email, $mdp)) {
                ajouterFlash('succes', 'Connexion réussie. Bienvenue ' . $_SESSION['prenom'] . '.');
                header("Location: tableau_de_bord.php");
            } else {
                ajouterFlash('erreur', 'Email ou mot de passe incorrect.');
                header("Location: login.php");
            }
            exit();
        }
        break;

    case 'deconnexion':
        ajouterFlash('info', 'Vous avez été déconnecté.');
        detruireSession();
        session_start();
        header("Location: login.php");
        exit();

    case 'tableau_de_bord':
        verifierRole('admin');
        stockerDansSession('derniere_page', 'tableau_de_bord');
        echo "<h1>Tableau de bord - " . htmlspecialchars($_SESSION['prenom']) . "</h1>";
        afficherFlash();
        break;

    case 'recherche_etudiant':
        verifierRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifierCsrf($_POST['token_csrf'] ?? '');
            $criteres = [
                'nom'      => trim($_POST['nom'] ?? ''),
                'classe'   => trim($_POST['classe'] ?? ''),
                'annee'    => trim($_POST['annee'] ?? ''),
            ];
            sauvegarderRecherche($criteres);
            sauvegarderPagination(1);
        }

        $criteres   = restaurerRecherche();
        $pagination = restaurerPagination();
        $offset     = ($pagination['page'] - 1) * $pagination['par_page'];

        $sql = "SELECT id, nom, prenom, email FROM etudiants WHERE 1=1";
        $params = [];
        $types  = "";

        if (!empty($criteres['nom'])) {
            $sql .= " AND nom LIKE ?";
            $params[] = "%" . $criteres['nom'] . "%";
            $types   .= "s";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $pagination['par_page'];
        $params[] = $offset;
        $types   .= "ii";

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $etudiants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo "<h2>Résultats de recherche</h2>";
        foreach ($etudiants as $e) {
            echo htmlspecialchars($e['prenom']) . " " . htmlspecialchars($e['nom']) . "<br>";
        }
        break;

    default:
        if (estConnecte()) {
            echo "<p>Connecté en tant que : " . htmlspecialchars($_SESSION['prenom']) . " (" . htmlspecialchars($_SESSION['role']) . ")</p>";
            echo "<p>Token CSRF : " . htmlspecialchars($_SESSION['token_csrf']) . "</p>";
            echo "<p>Dernière activité : " . date('H:i:s', $_SESSION['derniere_activite']) . "</p>";
        } else {
            echo "<p>Aucune session active.</p>";
        }
        break;
}

$conn->close();
