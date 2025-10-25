<?php
// 1. Inicia la sesión. Es necesario para poder acceder y modificar las variables de sesión.
session_start();

// 2. Destruye todas las variables de sesión.
$_SESSION = array();

// 3. Si se desea destruir la sesión completamente, borra también la cookie de sesión.
// Nota: Esto destruirá la sesión, y no solo los datos de la sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión.
session_destroy();

// 4. Redirige a la página de inicio de sesión.
header("location: index.php");
exit;
?>