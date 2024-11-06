<?php
include '../conexion.php';
session_start();

$consulta = $conexion->prepare("DELETE FROM empleado WHERE eliminado = 1");

if ($consulta->execute()) {
    $_SESSION['message'] = "Todos los empleados eliminados han sido borrados permanentemente";
} else {
    $_SESSION['message'] = "Hubo un error al eliminar permanentemente los empleados";
}

header("Location: eliminados.php");
exit();
?>
