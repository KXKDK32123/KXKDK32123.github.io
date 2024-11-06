<?php
include '../conexion.php';

// Validate that the ID exists and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ./eliminados.php");
    exit();
}

$id = (int)$_GET['id'];

// Use prepared statement to prevent SQL injection
$consulta = $conexion->prepare("UPDATE empleado SET eliminado = 1 WHERE id = ?");
$consulta->bind_param("i", $id);
$consulta->execute();

header("Location: ./eliminados.php");
exit();
?>
