<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma DICOM - Acceso</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="login-container">


        <h1>Portal de Imagenología Médica</h1>

        <form action="login.php" method="POST">
            
            <label for="usuario">📧 Usuario (Email/Nombre):</label>
            <input 
                type="text" 
                id="usuario" 
                name="usuario" 
                placeholder="Ingrese su usuario o email" 
                required
            >

            <label for="contrasena">🔒 Contraseña:</label>
            <input 
                type="password" 
                id="contrasena" 
                name="contrasena" 
                placeholder="Ingrese su contraseña" 
                required
            >

            <button type="submit">Iniciar Sesión</button>
        </form>

        <p class="nota">Acceso para Pacientes, Hospitales y Médicos.</p>
    </div>

</body>
</html>