<?php
include '../conexion.php';

$id = $_GET['id'];
$error = '';

$consulta = "SELECT * FROM empleado WHERE id = ? AND eliminado = 0";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$empleado = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $turno = $_POST['turno'];

    if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $error = "El nombre solo debe contener letras.";
    } else {
        $consulta = "UPDATE empleado SET nombre = ?, turno = ? WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("ssi", $nombre, $turno, $id);
        $stmt->execute();

        header("Location: ../index.html");
        exit;
    }
}
?>
