<?php

session_start();

function session_set($key, $value) {
    $_SESSION[$key] = $value;
}

function session_get($key, $default = null) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

function session_remove($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

function session_has($key) {
    return isset($_SESSION[$key]);
}

function session_destroy_all() {
    $_SESSION = [];
    session_unset();
    session_destroy();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}

function session_regenerate_safe() {
    session_regenerate_id(true);
}

function session_flash_set($key, $value) {
    $_SESSION['_flash'][$key] = $value;
}

function session_flash_get($key) {
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function session_is_expired($timeout = 1800) {
    if (!isset($_SESSION['_last_activity'])) {
        $_SESSION['_last_activity'] = time();
        return false;
    }
    if (time() - $_SESSION['_last_activity'] > $timeout) {
        session_destroy_all();
        return true;
    }
    $_SESSION['_last_activity'] = time();
    return false;
}

session_set('utilisateur', ['id' => 1, 'nom' => 'Alice']);
session_set('connecte', true);

$utilisateur = session_get('utilisateur');
$connecte    = session_get('connecte', false);

if (session_has('connecte')) {
    echo 'Bienvenue, ' . htmlspecialchars($utilisateur['nom']) . "\n";
}

if (session_is_expired(1800)) {
    header('Location: login.php');
    exit;
}

session_flash_set('succes', 'Profil mis a jour.');
$msg = session_flash_get('succes');
if ($msg) {
    echo $msg . "\n";
}

?>
