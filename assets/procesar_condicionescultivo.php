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

// Verificar si es una solicitud de preferencias SIS (JSON)
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['solicitudPreferenciasissune']) && $data['solicitudPreferenciasissune']) {
        $sqlCultivo = "INSERT INTO condiciones_cultivo (solicitud_id, solicitud_usuario_id, preferencia_sis_sune) 
                       VALUES ($solicitudId, $solicitudUsuarioId, 1)";
        if ($conn->query($sqlCultivo) === TRUE) {
            echo "Preferencias SIS Sune registradas.";
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

    $sql = "INSERT INTO condiciones_cultivo (
                solicitud_id, solicitud_usuario_id, tipo_cultivo, tiempo_cosecha, posibilidad_riego, tipo_riego, posibilidad_manejo, tipo_manejo
            ) VALUES (
                $solicitudId, $solicitudUsuarioId, '$tipo_cultivo', '$tiempo_cosecha', '$posibilidad_riego', '$tipo_riego', '$posibilidad_manejo', '$tipo_manejo'
            )";
    if ($conn->query($sql) === TRUE) {
        echo "Datos de condiciones de cultivo guardados correctamente.";
    } else {
        echo "Error al guardar los datos de condiciones de cultivo: " . $conn->error;
    }
}

$conn->close();
?>
