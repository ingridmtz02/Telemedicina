<?php
// 1. INICIAR SESIÓN Y PROTEGER LA PÁGINA
session_start();

// Verifica si el usuario NO ha iniciado sesión. Si no, lo redirige al login.
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Incluye el archivo de conexión a la base de datos
require_once "db_connect.php";

// Obtenemos el ID del usuario actual de la sesión
$usuario_id = $_SESSION["id"];

// 2. CONSULTA SQL PARA OBTENER LOS ESTUDIOS DEL USUARIO
// Asume que tienes una tabla 'estudios' con una columna 'usuario_id'
$sql = "SELECT id, fecha_subida, nombre_paciente, descripcion FROM estudios WHERE id_usuario = ?";

$estudios = []; // Array para almacenar los resultados

if($stmt = mysqli_prepare($link, $sql)){
    // Enlaza la variable de sesión al parámetro de la sentencia preparada
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $usuario_id;

    // Ejecuta la sentencia
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        // Almacena los resultados en un array
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $estudios[] = $row;
        }

        // Libera el set de resultados
        mysqli_free_result($result);

    } else{
        echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
    }

    // Cierra la sentencia
    mysqli_stmt_close($stmt);
}

// Cierra la conexión
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Estudios DICOM</title>
    <link rel="stylesheet" href="css/style.css"> 
    </head>
<body>
    <div class="container">
        <h2>🔬 Mis Estudios Médicos</h2>
        <p>Bienvenido, **<?php echo htmlspecialchars($_SESSION["usuario"]); ?>**. Aquí puedes ver tus estudios.</p>
        
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>

        <?php if (!empty($estudios)): ?>
            
            <table>
                <thead>
                    <tr>
                        <th>ID Estudio</th>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudios as $estudio): ?>
                        <tr>
                            <td><?php echo $estudio['id']; ?></td>
                            <td><?php echo htmlspecialchars($estudio['nombre_paciente']); ?></td>
                            <td><?php echo $estudio['fecha_subida']; ?></td>
                            <td><?php echo htmlspecialchars($estudio['descripcion']); ?></td>
                            <td>
                            <a href="#" onclick="abrirVisorModal(<?php echo $estudio['id']; ?>); return false;" class="btn-ver">Ver Imagen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="alerta">Aún no tienes estudios asociados en la base de datos.</p>
        <?php endif; ?>
    </div>

    <div id="visorModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarVisorModal()">&times;</span>
        
        <h4>Visor DICOM del Estudio ID: <span id="estudioIdDisplay"></span></h4>
        <div id="dicomViewerContainer">
            Cargando visor...
        </div>
    </div>
</div>

<script>
function abrirVisorModal(estudioId) {
    // 1. Muestra el ID del estudio
    document.getElementById('estudioIdDisplay').innerText = estudioId;
    
    // 2. Muestra la ventana modal
    document.getElementById('visorModal').style.display = "block";
    
    // 3. Realiza la carga dinámica del contenido DICOM (AJAX)
    // Carga el contenido de visor_dicom_content.php?id=[estudioId]
    var contenedor = document.getElementById('dicomViewerContainer');
    contenedor.innerHTML = 'Cargando visor...'; // Muestra mensaje de carga

    // Usa fetch o XMLHttpRequest para cargar el contenido
    fetch('visor_dicom_content.php?id=' + estudioId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar el estudio.');
            }
            return response.text();
        })
        .then(html => {
            // Inserta el contenido del visor DICOM en el contenedor
            contenedor.innerHTML = html;
        })
        .catch(error => {
            contenedor.innerHTML = 'Error al cargar el visor: ' + error.message;
            console.error('Error de carga AJAX:', error);
        });
}

function cerrarVisorModal() {
    // Oculta la ventana modal
    document.getElementById('visorModal').style.display = "none";
    
    // Opcional: Limpia el contenido para liberar recursos del visor DICOM
    document.getElementById('dicomViewerContainer').innerHTML = 'Cargando visor...';
}

// Cierra la modal si el usuario hace clic fuera de ella
window.onclick = function(event) {
    var modal = document.getElementById('visorModal');
    if (event.target == modal) {
        cerrarVisorModal();
    }
}
</script>
</body>
</html>