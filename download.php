<?php
session_start();
require_once "db_connect.php"; 

// 1. Verificar si es médico y si se proporcionó un ID de estudio
if(!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != 'medico' || !isset($_GET['id']) || empty($_GET['id'])){
    // Si no es médico o falta el ID, redirige
    header("location: dashboard.php");
    exit;
}

$estudio_id = intval($_GET['id']);

// 2. Obtener el nombre del archivo del estudio desde la BD
$sql = "SELECT nombre_archivo FROM estudios WHERE id = ?";
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $estudio_id;
    
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $nombre_archivo);
        if(mysqli_stmt_fetch($stmt)){
            $file_path = "uploads/" . $nombre_archivo;
            
            // 3. Verificar si el archivo existe en el servidor
            if (file_exists($file_path)) {
                
                // 4. Forzar la descarga mediante encabezados HTTP
                header('Content-Description: File Transfer');
                // Tipo MIME para DICOM (puede variar, pero 'application/dicom' es estándar)
                header('Content-Type: application/dicom'); 
                header('Content-Disposition: attachment; filename="DICOM_Estudio_' . $estudio_id . '.dcm"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                
                // Limpiar buffer y leer el archivo para enviarlo
                ob_clean();
                flush();
                readfile($file_path);
                
                exit;
            } else {
                echo "Error: El archivo no se encontró en el servidor.";
            }
        } else {
            echo "Error: Estudio no encontrado en la base de datos.";
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>