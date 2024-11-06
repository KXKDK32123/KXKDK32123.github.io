<?php
include '../conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta para eliminar al empleado permanentemente
    $consulta = "DELETE FROM empleado WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Ajustar el autoincremento
        ajustarAutoIncremento($conexion);
        
        // Redirigir a la página anterior con un mensaje de éxito
        header("Location: eliminados.php?mensaje=Empleado eliminado permanentemente.");
        exit;
    } else {
        // Redirigir a la página anterior con un mensaje de error
        header("Location: eliminados.php?mensaje=Error al eliminar el empleado.");
        exit;
    }
} else {
    // Redirigir a la página anterior si no se proporciona un ID
    header("Location: eliminados.php");
    exit;
}

function ajustarAutoIncremento($conexion) {
    // Obtener el máximo ID actual
    $consulta = "SELECT MAX(id) AS max_id FROM empleado";
    $resultado = $conexion->query($consulta);
    $fila = $resultado->fetch_assoc();
    
    $maxID = $fila['max_id'];

    // Ajustar el autoincremento si hay registros
    if ($maxID !== null) {
        $conexion->query("ALTER TABLE empleado AUTO_INCREMENT = " . ($maxID + 1));
    }
}


?>

<?php
include '../conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM empleado WHERE id = ?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        echo "Error al eliminar el empleado: " . $stmt->error;
        exit;
    }
    $stmt->close();
    $conexion->close();
    header("Location: eliminados.php?mensaje=Empleado eliminado permanentemente.");
    exit;
} else {
    header("Location: eliminados.php");
    exit;
}
?>