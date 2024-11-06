<?php
include '../conexion.php'; // Incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $turno = $_POST['turno'];

    // Actualizar el empleado en la base de datos
    $sql = "UPDATE empleado SET nombre = ?, turno = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $turno, $id);

    if ($stmt->execute()) {
        header("Location: ../index.html?msg=Empleado actualizado correctamente");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
