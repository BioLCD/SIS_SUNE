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

// Verificar si es una solicitud para preferencias SIS (JSON)
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['solicitudPreferenciasissune']) && $data['solicitudPreferenciasissune']) {
        // Crear siempre una nueva solicitud
        $sqlSolicitud = "INSERT INTO solicitudes (usuario_id) VALUES ($usuarioId)";
        if ($conn->query($sqlSolicitud) === TRUE) {
            $solicitudId = $conn->insert_id;
        } else {
            die("Error al registrar la solicitud: " . $conn->error);
        }

        // Insertar preferencias SIS Sune
        $sqlCultivo = "INSERT INTO condiciones_cultivo (solicitud_id, preferencia_sis_sune) VALUES ($solicitudId, 1)";
        if ($conn->query($sqlCultivo) === TRUE) {
            echo "Preferencias SIS Sune registradas con solicitud_id: $solicitudId.";
        } else {
            echo "Error al registrar las preferencias SIS Sune: " . $conn->error;
        }
    }
} else {
    // Procesar formulario (POST)
    $tipo_cultivo = $_POST['tipo_cultivo'];
    $tiempo_cosecha = $_POST['tiempo_cosecha'];
    $posibilidad_riego = $_POST['posibilidad_riego'];
    $tipo_riego = $_POST['tipo_riego'];
    $posibilidad_manejo = $_POST['posibilidad_manejo'];
    $tipo_manejo = $_POST['tipo_manejo'];

    // Crear siempre una nueva solicitud
    $sqlSolicitud = "INSERT INTO solicitudes (usuario_id) VALUES ($usuarioId)";
    if ($conn->query($sqlSolicitud) === TRUE) {
        $solicitudId = $conn->insert_id;
    } else {
        die("Error al registrar la solicitud: " . $conn->error);
    }

    // Insertar datos en condiciones_cultivo
    $sql = "INSERT INTO condiciones_cultivo (
                solicitud_id, tipo_cultivo, tiempo_cosecha, posibilidad_riego, tipo_riego, posibilidad_manejo, tipo_manejo
            ) VALUES (
                $solicitudId, '$tipo_cultivo', '$tiempo_cosecha', '$posibilidad_riego', '$tipo_riego', '$posibilidad_manejo', '$tipo_manejo'
            )";
    if ($conn->query($sql) === TRUE) {
        echo "Datos del formulario guardados correctamente con solicitud_id: $solicitudId.";
    } else {
        echo "Error al guardar los datos del formulario: " . $conn->error;
    }
}

$conn->close();
?>
