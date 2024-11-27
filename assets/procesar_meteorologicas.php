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

// Verificar si es una solicitud de caracterización (JSON)
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['solicitudCaracterizacionmeteorologicas']) && $data['solicitudCaracterizacionmeteorologicas']) {
        // Crear siempre una nueva solicitud
        $sqlSolicitud = "INSERT INTO solicitudes (usuario_id) VALUES ($usuarioId)";
        if ($conn->query($sqlSolicitud) === TRUE) {
            $solicitudId = $conn->insert_id;
        } else {
            die("Error al registrar la solicitud: " . $conn->error);
        }

        // Insertar caracterización meteorológica
        $sqlMeteorologicas = "INSERT INTO condiciones_meteorologicas (solicitud_id, caracterizacion_meteorologica) VALUES ($solicitudId, 1)";
        if ($conn->query($sqlMeteorologicas) === TRUE) {
            echo "Solicitud de caracterización meteorológica registrada con solicitud_id: $solicitudId.";
        } else {
            echo "Error al registrar la caracterización meteorológica: " . $conn->error;
        }
    }
} else {
    // Procesar formulario (POST)
    $radiacion_solar = $_POST['radiacion'];
    $nubosidad = $_POST['nubosidad'];
    $humedad_relativa = $_POST['humedad'];
    $presion_atmosferica = $_POST['atmosfera'];
    $precipitacion = $_POST['precipitacion'];
    $temperatura = $_POST['temperatura'];

    // Crear siempre una nueva solicitud
    $sqlSolicitud = "INSERT INTO solicitudes (usuario_id) VALUES ($usuarioId)";
    if ($conn->query($sqlSolicitud) === TRUE) {
        $solicitudId = $conn->insert_id;
    } else {
        die("Error al registrar la solicitud: " . $conn->error);
    }

    // Insertar datos en condiciones_meteorologicas
    $sql = "INSERT INTO condiciones_meteorologicas (solicitud_id, radiacion_solar, nubosidad, humedad_relativa, presion_atmosferica, precipitacion, temperatura) 
            VALUES ($solicitudId, '$radiacion_solar', '$nubosidad', '$humedad_relativa', '$presion_atmosferica', '$precipitacion', '$temperatura')";
    if ($conn->query($sql) === TRUE) {
        echo "Datos del formulario meteorológico guardados correctamente con solicitud_id: $solicitudId.";
    } else {
        echo "Error al guardar los datos del formulario meteorológico: " . $conn->error;
    }
}

$conn->close();
?>
