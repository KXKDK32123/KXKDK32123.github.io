<?php
include '../conexion.php'; // Incluir la conexión a la base de datos

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Restaurar el empleado eliminado
    $sql = "UPDATE empleado SET eliminado = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: eliminados.php?msg=Empleado restaurado correctamente");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
