<?php
session_start(); // Inicia la sesión PHP para manejar variables de sesión
include '../conexion.php'; // Incluye el archivo de conexión a la base de datos
require '../vendor/autoload.php'; // Carga las dependencias (PhpSpreadsheet)
use PhpOffice\PhpSpreadsheet\IOFactory; // Para manejar archivos Excel

// Variables iniciales
$error = '';              // Almacena mensajes de error
$successMessage = '';     // Almacena mensajes de éxito
$no_trabajador = '';      // Número de trabajador
$nombre = '';             // Nombre del empleado
$turno = '';             // Turno del empleado
$fileUploaded = false;    // Control de subida de archivos

// Manejo de agregar empleado individual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitEmpleado'])) {
    $no_trabajador = $_POST['no_trabajador'];
    $nombre = $_POST['nombre'];
    $turno = $_POST['turno'];

    // Validaciones de entrada
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        $error = "El nombre solo debe contener letras.";
    } elseif (!is_numeric($no_trabajador) || $no_trabajador <= 0) {
        $error = "El número de trabajador debe ser un número positivo.";
    } else {
        // Verifica si el número de trabajador ya existe
        $consulta = "SELECT COUNT(*) AS count FROM empleado WHERE no_trabajador = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("i", $no_trabajador);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        if ($fila['count'] > 0) {
            $error = "El número de trabajador ya existe. Intente con otro.";
        } else {
            // Inserta el nuevo empleado
            $no_credencial = '000' . $no_trabajador . 'B';
            $consulta = "INSERT INTO empleado (no_trabajador, nombre, turno, no_credencial) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("isss", $no_trabajador, $nombre, $turno, $no_credencial);
            $stmt->execute();
            $successMessage = "Empleado agregado correctamente.";
            // Limpia los campos después de la inserción
            $no_trabajador = '';
            $nombre = '';
            $turno = '';
        }
    }
}

// Manejo de carga de archivo Excel
if (isset($_FILES['excelFile'])) {
    if ($_FILES['excelFile']['error'] == UPLOAD_ERR_OK) {
        // Procesa el archivo Excel
        $fileType = IOFactory::identify($_FILES['excelFile']['tmp_name']);
        $reader = IOFactory::createReader($fileType);
        $spreadsheet = $reader->load($_FILES['excelFile']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Procesa cada fila del Excel
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            if ($row->getRowIndex() == 1) {
                continue; // Salta la primera fila (encabezados)
            }
            
            // Extrae datos de la fila
            $no_trabajador = $data[0];
            $nombre = $data[1];
            $turno = $data[2];

            // Validaciones para cada fila
            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
                $error = "El nombre '$nombre' solo debe contener letras.";
                break;
            } elseif (!is_numeric($no_trabajador) || $no_trabajador <= 0) {
                $error = "El número de trabajador '$no_trabajador' debe ser un número positivo.";
                break;
            } else {
                // Verifica duplicados
                $consulta = "SELECT COUNT(*) AS count FROM empleado WHERE no_trabajador = ?";
                $stmt = $conexion->prepare($consulta);
                $stmt->bind_param("i", $no_trabajador);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $fila = $resultado->fetch_assoc();

                if ($fila['count'] > 0) {
                    $error = "El número de trabajador '$no_trabajador' ya existe. Intente con otro.";
                    break;
                } else {
                    // Inserta el empleado desde Excel
                    $no_credencial = '000' . $no_trabajador . 'B';
                    $consulta = "INSERT INTO empleado (no_trabajador, nombre, turno, no_credencial) VALUES (?, ?, ?, ?)";
                    $stmt = $conexion->prepare($consulta);
                    $stmt->bind_param("isss", $no_trabajador, $nombre, $turno, $no_credencial);
                    $stmt->execute();
                }
            }
        }
        $fileUploaded = true;
        if (empty($error)) {
            $successMessage = "El archivo se ha subido correctamente.";
        }
        // Limpia los campos después de la carga
        $no_trabajador = '';
        $nombre = '';
        $turno = '';
    } elseif ($_FILES['excelFile']['error'] != UPLOAD_ERR_NO_FILE) {
        $error = "Hubo un problema al subir el archivo. Por favor, inténtelo de nuevo.";
    }
}

// Obtiene la lista de empleados activos
$consulta = "SELECT * FROM empleado WHERE eliminado = 0";
$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Empleados</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>
    <div class="containerphp">
        <h1>Administrar Empleados</h1>
        <!-- Formulario para agregar empleado individual -->
        <form action="" method="POST" onsubmit="return validarFormulario()">
            <h2>Agregar Empleado</h2>
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($successMessage): ?>
                <div class="success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <label for="no_trabajador">Número de Trabajador:</label>
            <input type="number" name="no_trabajador" min="1" value="<?php echo htmlspecialchars($no_trabajador); ?>" required>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            <label for="turno">Turno:</label>
            <select name="turno" required>
                <option value="Matutino" <?php echo $turno === 'Matutino' ? 'selected' : ''; ?>>Matutino</option>
                <option value="Vespertino" <?php echo $turno === 'Vespertino' ? 'selected' : ''; ?>>Vespertino</option>
            </select>
            <input type="submit" name="submitEmpleado" value="Agregar Empleado" class="btn-save">
        </form>

        <!-- Formulario para subir archivo Excel -->
        <h2>Subir Archivo Excel</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="excelFile">Archivo Excel:</label>
            <label class="btn-excel">
                <input type="file" name="excelFile" accept=".xlsx" required style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="20" height="20">
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 6.627 5.373 12 12 12s12-5.373 12-12S18.627 0 12 0zm1.5 17.25h-3v-3h3v3zm0-4.5h-3v-3h3v3zm3 4.5h-1.5v-3h1.5v3zm0-4.5h-1.5v-3h1.5v3zM9 6.75h3v3H9v-3zm-3 0h1.5v3H6v-3zm0 4.5h1.5v3H6v-3zm0 4.5h1.5v-3H6v-3z"/>
                </svg>
                Subir Excel
            </label>
            <input type="submit" value="Cargar Excel" class="btn-save">
        </form>

        <!-- Lista de empleados -->
        <h3>Lista de Empleados</h3>
        <div class="scroll">
            <form method="POST" action="agregar.php">
                <table>
                    <thead>
                        <tr>
                            <th>No. Trabajador</th>
                            <th>Nombre</th>
                            <th>Turno</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $fila['no_trabajador']; ?></td>
                                <td><?php echo $fila['nombre']; ?></td>
                                <td><?php echo $fila['turno']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>
        <a href="../index.html" class="button-back">regresar</a>
    </div>
</body>
</html>
  















