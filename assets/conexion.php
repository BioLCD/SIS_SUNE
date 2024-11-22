<?php
// Datos de la base de datos
$host = "localhost";
$dbname = "sis_sune"; // Cambia por el nombre de tu base de datos
$username = "root"; // Cambia si usas otro usuario
$password = ""; // Cambia si tienes contraseña configurada

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    // Opcional: mensaje para depuración
    // echo "Conexión exitosa.";
}


include 'conexion.php';

// Ejemplo: Consulta para obtener datos
$sql = "SELECT * FROM departamentos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Departamento: " . $row["nombre"] . "<br>";
    }
} else {
    echo "No se encontraron resultados.";
}

$conn->close(); // Cierra la conexión después de usarla


?>



