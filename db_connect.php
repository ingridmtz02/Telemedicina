<?php
// Define las constantes de conexión a la base de datos
// Recuerda: XAMPP por defecto usa 'root' como usuario y contraseña vacía.
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '2004a');
define('DB_NAME', 'db_dicom'); // Asegúrate de que este es el nombre que usaste.
define('DB_PORT',3307);
// Intenta conectar a la base de datos MySQL
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Verifica la conexión
if($link === false){
    // Si la conexión falla, detiene el script y muestra un error
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// Opcional: Establecer el juego de caracteres a UTF-8 para evitar problemas con acentos y ñ
mysqli_set_charset($link, "utf8mb4");

// La variable $link contiene ahora el objeto de conexión
?>