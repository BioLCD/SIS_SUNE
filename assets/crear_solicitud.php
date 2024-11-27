<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sis_sune";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Obtener ID del usuario autenticado
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}
$usuarioId = $_SESSION['usuario_id'];

// Calcular el siguiente solicitud_usuario_id para el usuario
$sqlCount = "SELECT COUNT(*) AS total_solicitudes FROM solicitudes WHERE usuario_id = $usuarioId";
$resultCount = $conn->query($sqlCount);
if ($resultCount && $row = $resultCount->fetch_assoc()) {
    $solicitudUsuarioId = $row['total_solicitudes'] + 1;
} else {
    echo json_encode(['success' => false, 'message' => 'Error al calcular el número de solicitud_usuario_id.']);
    exit;
}

// Crear una nueva solicitud activa
$sqlSolicitud = "INSERT INTO solicitudes (usuario_id, solicitud_usuario_id, estado) VALUES ($usuarioId, $solicitudUsuarioId, 'Activa')";
if ($conn->query($sqlSolicitud) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la solicitud: ' . $conn->error]);
}

$conn->close();
?>
