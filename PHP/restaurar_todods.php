<?php
include '../conexion.php';
session_start();

$consulta = $conexion->prepare("UPDATE empleado SET eliminado = 0 WHERE eliminado = 1");

if ($consulta->execute()) {
    $_SESSION['message'] = "Todos los empleados han sido restaurados correctamente";
} else {
    $_SESSION['message'] = "Hubo un error al restaurar los empleados";
}

header("Location: eliminados.php");
exit();
?>
