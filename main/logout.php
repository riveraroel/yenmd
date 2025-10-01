<?php
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_unset();
session_destroy();
if (isset($_SERVER['HTTP_COOKIE'])) {//do we have any
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);//get all cookies 
    foreach($cookies as $cookie) {//loop
        $parts = explode('=', $cookie);//get the bits we need
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);//kill it
        setcookie($name, '', time()-1000, '/');//kill it more
    }
}
header("Location: ../");
?>