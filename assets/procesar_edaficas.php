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

    if (isset($data['solicitudCaracterizacionedaficas']) && $data['solicitudCaracterizacionedaficas']) {
        $sqlEdaficas = "INSERT INTO condiciones_edaficas (solicitud_id, solicitud_usuario_id, caracterizacion_edafica) 
                        VALUES ($solicitudId, $solicitudUsuarioId, 1)";
        if ($conn->query($sqlEdaficas) === TRUE) {
            echo "Caracterización edáfica registrada.";
        } else {
            echo "Error al registrar la caracterización edáfica: " . $conn->error;
        }
    }
} else {
    // Procesar formulario (POST)
    $ph = $_POST['ph'];
    $contenido_agua = $_POST['contenido_agua'];
    $capa_organica = $_POST['capa_organica'];
    $porosidad = $_POST['porosidad'];
    $textura = $_POST['textura'];
    $nivel_freatico = $_POST['nivel_freatico'];
    $microbiota = $_POST['microbiota'];
    $fauna_edafica = $_POST['fauna_edafica'];

    $sqlEdaficas = "INSERT INTO condiciones_edaficas (solicitud_id, solicitud_usuario_id, ph, contenido_agua, capa_organica, porosidad, textura, nivel_freatico, microbiota, fauna_edafica) 
                    VALUES ($solicitudId, $solicitudUsuarioId, '$ph', '$contenido_agua', '$capa_organica', '$porosidad', '$textura', '$nivel_freatico', '$microbiota', '$fauna_edafica')";
    if ($conn->query($sqlEdaficas) === TRUE) {
        echo "Datos del formulario edáfico guardados.";
    } else {
        echo "Error al guardar los datos del formulario edáfico: " . $conn->error;
    }
}

$conn->close();
?>
