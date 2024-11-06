<?php
include '../conexion.php';
session_start();

try {
    // Primero verifica si hay registros para eliminar
    $consulta = "SELECT COUNT(*) as total FROM empleado";
    $resultado = $conexion->query($consulta);
    $total = $resultado->fetch_assoc()['total'];

    if ($total > 0) {
        // Inicia una transacción para asegurar que todas las operaciones se completen
        $conexion->begin_transaction();

        // Elimina todos los registros de la tabla
        $consulta = "DELETE FROM empleado";
        if (!$conexion->query($consulta)) {
            throw new Exception("Error al eliminar los registros");
        }

        // Reinicia el auto_increment a 1
        $consulta = "ALTER TABLE empleado AUTO_INCREMENT = 1";
        if (!$conexion->query($consulta)) {
            throw new Exception("Error al reiniciar el auto_increment");
        }

        // Si todo salió bien, confirma los cambios
        $conexion->commit();
        $_SESSION['message'] = "Se han eliminado todos los registros y reiniciado el contador correctamente";
        $_SESSION['message_type'] = "success";

    } else {
        $_SESSION['message'] = "La base de datos ya está vacía";
        $_SESSION['message_type'] = "info";
    }

} catch (Exception $e) {
    // Si hay algún error, revierte los cambios
    if ($conexion->connect_errno == 0) {
        $conexion->rollback();
    }
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}

// Redirecciona de vuelta a la página principal
header("Location: eliminados.php");
exit();
?>
