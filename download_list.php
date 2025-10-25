<?php
session_start();
require_once "db_connect.php"; 

// 1. Verificar si es médico
if(!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != 'medico'){
    header("location: dashboard.php");
    exit;
}

$estudios = [];
$sql = "SELECT id, nombre_paciente, descripcion, fecha_subida, diagnostico FROM estudios ORDER BY fecha_subida DESC";
$result = mysqli_query($link, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $estudios[] = $row;
    }
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Estudios DICOM</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Consulta de Estudios Pendientes</h1>
        <p>Haga clic en el botón para descargar y visualizar el estudio.</p>

        <?php if (empty($estudios)): ?>
            <p>No hay estudios disponibles en este momento.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Fecha de Carga</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudios as $estudio): ?>
                    <tr>
                        <td><?php echo $estudio['id']; ?></td>
                        <td><?php echo htmlspecialchars($estudio['nombre_paciente']); ?></td>
                        <td><?php echo $estudio['fecha_subida']; ?></td>
                        <td><?php echo empty($estudio['diagnostico']) ? '🔴 Pendiente' : '🟢 Diagnosticado'; ?></td>
                        <td>
                            <a href="download.php?id=<?php echo $estudio['id']; ?>" class="button" style="background-color:#007bff; color:white; padding: 5px 10px; text-decoration: none; border-radius: 4px;">Descargar</a>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="dashboard.php">↩️ Volver al Panel</a></p>
    </div>
</body>
</html>