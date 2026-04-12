<?php
$cookie_name = "user_session";
$cookie_value = "abc123";
$cookie_expiry = time() + (86400 * 30);
$cookie_path = "/";
$cookie_domain = "example.com";
$cookie_secure = true;
$cookie_httponly = true;

setcookie($cookie_name, $cookie_value, $cookie_expiry, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);

if (isset($_COOKIE[$cookie_name])) {
    $value = $_COOKIE[$cookie_name];
}

setcookie($cookie_name, "", time() - 3600, $cookie_path, $cookie_domain);

session_start();
$_SESSION["username"] = "john_doe";
$_SESSION["role"] = "admin";

$session_id = session_id();

session_destroy();
