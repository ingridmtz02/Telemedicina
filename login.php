<?php
// === HABILITAR MUESTRA DE ERRORES (QUITA ESTO EN PRODUCCIÓN) ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia la sesión para almacenar las variables de usuario
session_start();

// 1. Incluye el archivo de conexión
require_once "db_connect.php";

// Variables para almacenar los datos del formulario
$usuario = $contrasena = "";
$error_login = "";

// Procesa el formulario SOLO si se envió por el método POST
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // 2. Obtiene y limpia los datos del formulario
    // Se usa mysqli_real_escape_string para prevenir inyección SQL (aunque es mejor usar sentencias preparadas)
    $usuario = trim(mysqli_real_escape_string($link, $_POST["usuario"]));
    $contrasena = trim($_POST["contrasena"]);

    // 3. Verifica que el usuario exista
    $sql = "SELECT id, usuario, contrasena, tipo_usuario FROM usuarios WHERE usuario = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Enlaza la variable al parámetro de la sentencia preparada
        mysqli_stmt_bind_param($stmt, "s", $param_usuario);
        $param_usuario = $usuario;

        // Ejecuta la sentencia
        if(mysqli_stmt_execute($stmt)){
            // Almacena el resultado
            mysqli_stmt_store_result($stmt);

            // 4. Verifica si el usuario existe en la base de datos (si hay 1 o más filas)
            if(mysqli_stmt_num_rows($stmt) == 1){                    
                // Enlaza las variables a las columnas del resultado
                mysqli_stmt_bind_result($stmt, $id, $usuario_db, $contrasena_hash, $tipo_usuario);
                
                if(mysqli_stmt_fetch($stmt)){
                    // 5. Verifica la contraseña. USAR HASHES SEGUROS EN PROD.
                    // Para este ejemplo, estamos usando MD5, pero DEBERÍAS USAR password_verify($contrasena, $contrasena_hash)
                    if(md5($contrasena) == $contrasena_hash){
                        // Contraseña correcta, iniciar sesión
                        
                        // Almacena datos en variables de sesión
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["usuario"] = $usuario_db;
                        $_SESSION["tipo_usuario"] = $tipo_usuario; // ¡CRUCIAL para los permisos!
                        
                        // Redirige al usuario al panel principal (dashboard)
                        header("location: dashboard.php");
                        exit;
                    } else{
                        // Contraseña no válida
                        $error_login = "La contraseña que ingresó no es válida.";
                    }
                }
            } else{
                // Usuario no existe
                $error_login = "No se encontró ninguna cuenta con ese nombre de usuario.";
            }
        } else{
            echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }

        // Cierra la sentencia
        mysqli_stmt_close($stmt);
    }
}

// Si hay error, redirige de nuevo a index.php con el error (puedes usar una variable GET)
if (!empty($error_login)) {
    // Para simplificar, si hay un error, redirigiremos y mostraremos el mensaje en index.php
    header("location: index.php?error=" . urlencode($error_login));
}

// Cierra la conexión
mysqli_close($link);
?>