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

// Iniciar sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    die("Error: Usuario no autenticado.");
}
$usuarioId = $_SESSION['usuario_id'];

// Verificar si existe una solicitud activa
$sqlVerificar = "SELECT id FROM solicitudes WHERE usuario_id = $usuarioId AND estado = 'Activa' LIMIT 1";
$resultVerificar = $conn->query($sqlVerificar);

if ($resultVerificar->num_rows > 0) {
    // Marcar la solicitud activa como completada
    $sqlCompletar = "UPDATE solicitudes SET estado = 'Completada' WHERE usuario_id = $usuarioId AND estado = 'Activa'";
    if ($conn->query($sqlCompletar) === TRUE) {
        // Cerrar sesión después de finalizar la solicitud
        session_destroy();
        echo "Solicitud finalizada y sesión cerrada exitosamente.";
    } else {
        echo "Error al finalizar la solicitud: " . $conn->error;
    }
} else {
    echo "No hay ninguna solicitud activa para este usuario.";
}

$conn->close();
?>
