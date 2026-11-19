<?php
require_once dirname(__DIR__) . '/config.php';
requireUser();
protectPost();
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 3600, 'path' => $params['path'], 'domain' => $params['domain'],
    'secure' => $params['secure'], 'httponly' => $params['httponly'], 'samesite' => 'Lax',
]);
session_destroy();
redirect();
