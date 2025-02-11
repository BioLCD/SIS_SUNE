<?php
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
$userEmail = $_POST['userEmail'] ?? '';
$userPassword = $_POST['userPassword'] ?? '';
$userType = $_POST['userType'] ?? '';

// Validar datos del formulario
if (empty($userEmail) || empty($userPassword) || empty($userType)) {
    die("Todos los campos son obligatorios.");
}

// Validar si el correo ya existe
$sqlVerificar = "SELECT id FROM usuarios WHERE correo = ?";
$stmtVerificar = $conn->prepare($sqlVerificar);
$stmtVerificar->bind_param("s", $userEmail);
$stmtVerificar->execute();
$result = $stmtVerificar->get_result();

if ($result->num_rows > 0) {
    die("Este correo ya está registrado.");
}

// Insertar nuevo usuario en la base de datos
$sql = "INSERT INTO usuarios (correo, contrasena, tipo_usuario) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $userEmail, $userPassword, $userType);

if ($stmt->execute()) {
    $responseMessage = "Registro exitoso. Ahora puedes iniciar sesión.";

    // Verificar si la solicitud proviene de Postman
    $isPostman = isset($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'postman') !== false;

    if ($isPostman) {
        // Responder con texto plano para Postman
        echo $responseMessage;
    } else {
        // Redirigir en navegadores
        header("Location: usuarios.html");
        exit; // Asegura que no se ejecute más código después de la redirección
    }
} else {
    echo "Error al registrar el usuario: " . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
