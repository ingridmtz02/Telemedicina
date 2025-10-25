<?php
session_start();
require_once "db_connect.php"; // Conexión a la BD

// 1. Verifica si tiene permiso para cargar (solo pacientes/hospitales)
if(!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != 'paciente' && $_SESSION["tipo_usuario"] != 'hospital')){
    header("location: dashboard.php");
    exit;
}

$mensaje = "";

// 2. Procesa la subida del formulario
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["dicom_file"])){
    $target_dir = "uploads/";
    $file_name = basename($_FILES["dicom_file"]["name"]);
    // Genera un nombre de archivo único para evitar colisiones
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    $uploadOk = 1;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es realmente DICOM (simplificado)
    if($file_type != "dcm" && $file_type != "dicom") {
        $mensaje = "ERROR: Solo se permiten archivos DICOM (.dcm, .dicom).";
        $uploadOk = 0;
    }

    // Si todo está bien, intenta subir el archivo
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["dicom_file"]["tmp_name"], $target_file)) {
            
            // 3. Guarda la información del estudio en la base de datos
            $sql = "INSERT INTO estudios (id_usuario, nombre_paciente, descripcion, nombre_archivo, fecha_subida) VALUES (?, ?, ?, ?, NOW())";
            
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "isss", $id_usuario, $nombre_paciente, $descripcion, $nombre_archivo_db);
                
                $id_usuario = $_SESSION["id"];
                $nombre_paciente = $_POST['nombre_paciente'];
                $descripcion = $_POST['descripcion'];
                $nombre_archivo_db = basename($target_file); // Solo guardamos el nombre único
                
                if(mysqli_stmt_execute($stmt)){
                    $mensaje = "✅ ¡El estudio se cargó y registró correctamente! (Archivo: " . htmlspecialchars($file_name) . ")";
                } else{
                    $mensaje = "ERROR: Hubo un problema al registrar el estudio en la base de datos.";
                    // Opcional: eliminar el archivo subido si falla el registro en BD
                    unlink($target_file); 
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $mensaje = "ERROR: Hubo un error al subir el archivo al servidor.";
        }
    }
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carga de Estudios DICOM</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Carga de Estudios DICOM</h1>
        <p>Utilice este formulario para subir un archivo de imagen médica DICOM.</p>

        <?php if (!empty($mensaje)) { echo "<p style='color:red;'>$mensaje</p>"; } ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            
            <label for="nombre_paciente">👤 Nombre del Paciente:</label>
            <input type="text" id="nombre_paciente" name="nombre_paciente" required>

            <label for="descripcion">📝 Descripción Breve del Estudio:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <label for="dicom_file">📂 Seleccionar Archivo DICOM:</label>
            <input type="file" id="dicom_file" name="dicom_file" accept=".dcm, .dicom" required>
            
            <button type="submit" style="background-color: #28a745; margin-top: 20px;">Subir Estudio</button>
        </form>

        <p><a href="dashboard.php">↩️ Volver al Panel</a></p>
    </div>
</body>
</html>