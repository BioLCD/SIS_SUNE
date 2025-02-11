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

    if (isset($data['solicitudCaracterizacionMeteorologicas']) && $data['solicitudCaracterizacionMeteorologicas']) {
        $sqlMeteorologicas = "INSERT INTO condiciones_meteorologicas (solicitud_id, solicitud_usuario_id, caracterizacion_meteorologica) 
                              VALUES ($solicitudId, $solicitudUsuarioId, 1)";
        if ($conn->query($sqlMeteorologicas) === TRUE) {
            echo "Caracterización meteorológica registrada.";
        } else {
            echo "Error al registrar la caracterización meteorológica: " . $conn->error;
        }
    }
} else {
    // Procesar formulario (POST)
    $temperatura = $_POST['temperatura'];
    $humedad_relativa = $_POST['humedad_relativa'];
    $presion_atmosferica = $_POST['presion_atmosferica'];
    $precipitacion = $_POST['precipitacion'];
    $radiacion_solar = $_POST['radiacion_solar'];

    $sqlMeteorologicas = "INSERT INTO condiciones_meteorologicas (
                              solicitud_id, solicitud_usuario_id, temperatura, humedad_relativa, 
                              presion_atmosferica, precipitacion, radiacion_solar
                          ) VALUES (
                              $solicitudId, $solicitudUsuarioId, '$temperatura', '$humedad_relativa', 
                              '$presion_atmosferica', '$precipitacion', '$radiacion_solar'
                          )";

    if ($conn->query($sqlMeteorologicas) === TRUE) {
        echo "Datos meteorológicos guardados correctamente.";
    } else {
        echo "Error al guardar los datos meteorológicos: " . $conn->error;
    }
}

$conn->close();
?>
