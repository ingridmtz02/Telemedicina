<?php
// Inicia la sesión para acceder a las variables de usuario
session_start();

// Verifica si el usuario ha iniciado sesión. Si no, lo redirige al login.
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Variables útiles
$usuario = $_SESSION["usuario"];
$tipo_usuario = $_SESSION["tipo_usuario"];

// Función para obtener el título según el tipo de usuario
function obtener_titulo_rol($tipo) {
    switch ($tipo) {
        case 'paciente':
            return "Panel del Paciente";
        case 'hospital':
            return "Panel de Administración Hospitalaria";
        case 'medico':
            return "Panel de Diagnóstico Médico";
        default:
            return "Panel de Usuario";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo obtener_titulo_rol($tipo_usuario); ?></title>
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* Estilos específicos para el dashboard */
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .rol-menu {
            margin-top: 20px;
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        .rol-menu a {
            padding: 15px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .rol-menu a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h1>Bienvenido, <?php echo $usuario; ?></h1>
    <h2><?php echo obtener_titulo_rol($tipo_usuario); ?></h2>
    
    <p>Utilice las opciones a continuación para acceder a las funcionalidades de la plataforma.</p>

    <div class="rol-menu">
        <?php 
        // Lógica de visualización de menús según el rol
        
        // Menú para Pacientes y Hospitales (Carga)
        if ($tipo_usuario == 'paciente' || $tipo_usuario == 'hospital') {
            echo '<a href="upload_form.php">⬆️ Cargar Nuevo Estudio DICOM</a>';
            echo '<a href="mis_estudios.php">📋 Mis Estudios Cargados</a>'; 
        }

        // Menú para Médicos (Consulta y Diagnóstico)
        if ($tipo_usuario == 'medico') {
            echo '<a href="download_list.php">⬇️ Consultar y Descargar Estudios</a>';
            echo '<a href="diagnosticos.php">📝 Mis Diagnósticos Emitidos</a>'; 
        }
        ?>
    </div>

    <hr style="margin-top: 30px;">
    <p style="text-align: right;"><a href="logout.php">Cerrar Sesión</a></p>
</div>

</body>
</html>