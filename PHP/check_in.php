<?php
include 'conexion.php';

$id = $_GET['id'];
$fechaActual = date('Y-m-d H:i:s');

// Actualizar `check_in` y registrar la `fecha_check_in`
$consulta = "UPDATE empleado SET check_in = 1, fecha_check_in = '$fechaActual' WHERE id = '$id'";
$conexion->query($consulta);

header("Location: index.html");
?>
