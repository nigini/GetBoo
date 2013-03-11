<?php
// Provides HTTP Basic authentication of a user

define("MAX_API_BOOKMARKS", 1000);
function authenticate() {
    header('WWW-Authenticate: Basic realm="del.icio.us API"');
    header('HTTP/1.0 401 Unauthorized');
    die("Use of the API calls requires authentication.");
}
$SETTINGS['path_mod'] = "../";
require_once('../config.inc.php');
require_once('../includes/user.php');
if (isset($_GET['Authorization'])) {
    if (preg_match('/Basic\s+(.*)$/i', $_GET['Authorization'], $Authorization)) {
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode($Authorization[1]));
    }
}
if (isset($_SERVER['PHP_AUTH_USER']) && strlen($_SERVER['PHP_AUTH_PW']) > 0) {
    $user = new User();
    $login = $user->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    if (!$login['success']) {
        authenticate();
    }
} else {
    authenticate();
}
?>