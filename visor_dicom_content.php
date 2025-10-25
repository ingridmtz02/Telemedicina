<?php
// visor_dicom_content.php

// Asegúrate de que este archivo no sea accedido directamente
if (!isset($_GET['id'])) {
    die("ID de estudio requerido.");
}

// 1. Incluye la conexión y verifica la sesión si es necesario
require_once "db_connect.php"; 
session_start();
// Opcional: Verifica que el usuario tenga permiso para ver este ID.

$estudio_id = (int)$_GET['id'];
$ruta_dicom = ""; // Variable que contendrá la ruta real del archivo.

// 2. Consulta la base de datos para obtener la RUTA del archivo DICOM.
// **Esta es la parte crucial que debes completar.**
$sql = "SELECT nombre_archivo FROM estudios WHERE id = ? AND id_usuario = ?"; 
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "ii", $estudio_id, $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre_archivo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Asume que los archivos DICOM están en una carpeta dentro de PIA-Telemed/
    // y construye la ruta COMPLETA para que el visor pueda acceder a ella.
    $ruta_dicom = "http://".$_SERVER['HTTP_HOST']."/PIA-Telemed/uploads/" . $nombre_archivo;
}
mysqli_close($link);

if (empty($nombre_archivo)) {
    echo "<p>No se encontró el estudio o no tienes permisos para verlo.</p>";
    exit;
}

// 3. Incluye los archivos de la librería DICOM (si no están ya en mis_estudios.php)
// NOTA: Es más eficiente incluir estos JS/CSS en mis_estudios.php una sola vez.

// 4. Genera el HTML y el JavaScript para inicializar el visor.
?>

<div id="dicom-canvas-<?php echo $estudio_id; ?>" 
     style="width: 100%; height: 60vh; background-color: #000;">
     Cargando imagen DICOM...
</div>

<script>
    // Usamos una función para asegurar que el script se ejecute
    function inicializarVisorDICOM(canvasId, filePath) {
        // ------------------------------------------------------------------
        // === INICIALIZACIÓN DE LIBRERÍA DICOM (Ejemplo: DWV o Cornerstone) ===
        // ------------------------------------------------------------------
        
        console.log('Inicializando visor para el estudio:', canvasId, 'en ruta:', filePath);
        
        // **AQUÍ VA EL CÓDIGO ESPECÍFICO DE TU LIBRERÍA DE VISUALIZACIÓN.**
        
        // EJEMPLO CONCEPTUAL (Usando una función hipotética de carga):
        // var visor = new MiLibreriaDICOM.Visor(document.getElementById(canvasId));
        // visor.load(filePath);
        
        // EJEMPLO: Si usas DWV (el canvas requiere un ID único)
        /*
        try {
            var app = new dwv.App();
            app.init({ "containerDivId": canvasId });
            app.loadURL(filePath); 
        } catch (e) {
            document.getElementById(canvasId).innerText = 'Error al iniciar DWV: ' + e.message;
        }
        */

        // EJEMPLO: Muestra un mensaje de éxito si la ruta existe
        document.getElementById(canvasId).innerText = 
            "Visor listo. Intentando cargar el archivo:\n" + filePath + 
            "\nSi el visor no aparece, asegúrate de que los archivos JS/CSS de la librería estén cargados.";

    }

    // Llama a la función con los datos PHP
    inicializarVisorDICOM(
        "dicom-canvas-<?php echo $estudio_id; ?>", 
        "<?php echo $ruta_dicom; ?>"
    );
</script>