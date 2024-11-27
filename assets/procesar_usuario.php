<?php
// Iniciar sesión
session_start();

// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sis_sune";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$userEmail = $_POST['userEmail'];
$userPassword = $_POST['userPassword'];

// Validar datos del formulario
if (empty($userEmail) || empty($userPassword)) {
    die("Todos los campos son obligatorios.");
}

// Consultar usuario en la base de datos
$sql = "SELECT id, tipo_usuario FROM usuarios WHERE correo = ? AND contrasena = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userEmail, $userPassword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Usuario encontrado
    $user = $result->fetch_assoc();

    // Guardar datos del usuario en la sesión
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_tipo'] = $user['tipo_usuario'];

    // Redirigir según el tipo de usuario
    if ($user['tipo_usuario'] === "Agricultor") {
        header("Location: agricultor.html");
    } else {
        echo "Inicio de sesión exitoso. Tipo de usuario: " . $user['tipo_usuario'];
    }
} else {
    // Usuario no encontrado
    echo "Correo o contraseña incorrectos.";
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
