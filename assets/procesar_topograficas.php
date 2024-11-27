<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sis_sune";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener ID del usuario autenticado
session_start();
if (!isset($_SESSION['usuario_id'])) {
    die("Error: Usuario no autenticado.");
}
$usuarioId = $_SESSION['usuario_id'];

// Verificar si el usuario tiene una solicitud activa
$sqlActiva = "SELECT id, solicitud_usuario_id FROM solicitudes WHERE usuario_id = $usuarioId AND estado = 'Activa'";
$resultActiva = $conn->query($sqlActiva);

if ($resultActiva && $row = $resultActiva->fetch_assoc()) {
    $solicitudId = $row['id'];
    $solicitudUsuarioId = $row['solicitud_usuario_id'];
} else {
    die("No hay una solicitud activa. Por favor, inicie una nueva solicitud.");
}

// Verificar si es una solicitud de caracterización (JSON)
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['solicitudCaracterizaciontopograficas']) && $data['solicitudCaracterizaciontopograficas']) {
        $sqlTopograficas = "INSERT INTO condiciones_topograficas (solicitud_id, solicitud_usuario_id, caracterizacion_topografica) 
                            VALUES ($solicitudId, $solicitudUsuarioId, 1)";
        if ($conn->query($sqlTopograficas) === TRUE) {
            echo "Caracterización topográfica registrada.";
        } else {
            echo "Error al registrar la caracterización topográfica: " . $conn->error;
        }
    }
} else {
    // Procesar formulario (POST)
    $direccion = $_POST['direccion'];
    $departamento = $_POST['departamento'];
    $latitud = $_POST['latitud'];
    $hemisferio_latitud = $_POST['hemisferio_latitud'];
    $longitud = $_POST['longitud'];
    $hemisferio_longitud = $_POST['hemisferio_longitud'];
    $altitud = $_POST['altitud'];
    $pendientes = $_POST['pendientes'];
    $area = $_POST['area'];

    $sqlTopograficas = "INSERT INTO condiciones_topograficas (
                            solicitud_id, solicitud_usuario_id, direccion_terreno, ubicacion_terreno, latitud_terreno, 
                            hemisferio_latitud, longitud_terreno, hemisferio_longitud, altitud_terreno, pendiente_terreno, area_terreno
                        ) VALUES (
                            $solicitudId, $solicitudUsuarioId, '$direccion', '$departamento', '$latitud', 
                            '$hemisferio_latitud', '$longitud', '$hemisferio_longitud', '$altitud', '$pendientes', '$area'
                        )";
    if ($conn->query($sqlTopograficas) === TRUE) {
        echo "Datos del formulario topográfico guardados.";
    } else {
        echo "Error al guardar los datos del formulario topográfico: " . $conn->error;
    }
}

$conn->close();
?>
