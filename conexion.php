<?php
$servername = "localhost"; // Cambia según tu configuración
$username = "root"; // Cambia según tu configuración
$password = ""; // Cambia según tu configuración
$dbname = "empleados"; // Cambia según tu configuración

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$conexion = new mysqli("localhost", "root", "", "empleados");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

?>
