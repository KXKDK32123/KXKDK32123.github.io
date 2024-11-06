<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Empleados</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>
    <div class="container">
        <a href="../index.html" class="back-button">← Volver al inicio</a>
        
        <div class="date-filter">
            <h2>Filtrar Registros</h2>
            <form method="GET" action="">
                <select name="filter_type" id="filter_type">
                    <option value="day">Por día</option>
                    <option value="month">Por mes</option>
                    <option value="year">Por año</option>
                    <option value="range">Rango de fechas</option>
                </select>

                <div id="single_date">
                    <input type="date" name="single_date" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div id="date_range" style="display:none;">
                    <input type="date" name="start_date">
                    <input type="date" name="end_date">
                </div>

                <div class="turno-filtro">
                    <select name="turno" id="turno">
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>
                </div>
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <div class="date-display">
            <?php
            $filterType = $_GET['filter_type'] ?? 'day';
            $singleDate = $_GET['single_date'] ?? date('Y-m-d');
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';

            if ($filterType === 'day') {
                $timestamp = strtotime($singleDate);
                $today = strtotime('today');
                $yesterday = strtotime('yesterday');
                
                if ($timestamp === $today) {
                    echo "<h3>Registros de Hoy</h3>";
                } elseif ($timestamp === $yesterday) {
                    echo "<h3>Registros de Ayer</h3>";
                } else {
                    echo "<h3>Registros del " . date('d/m/Y', $timestamp) . "</h3>";
                }
            } elseif ($filterType === 'month') {
                echo "<h3>Registros de " . date('F Y', strtotime($singleDate)) . "</h3>";
            } elseif ($filterType === 'year') {
                echo "<h3>Registros del año " . date('Y', strtotime($singleDate)) . "</h3>";
            } elseif ($filterType === 'range') {
                echo "<h3>Registros del " . date('d/m/Y', strtotime($startDate)) . 
                     " al " . date('d/m/Y', strtotime($endDate)) . "</h3>";
            }
            ?>
        </div>

        <!-- Tabla de registros -->
        <?php
        include '../conexion.php';

        $query = "SELECT e.*, 
                         DATE(e.fecha_check_in) as fecha,
                         TIME(e.fecha_check_in) as hora
                  FROM empleado e 
                  WHERE eliminado = 0";

        if ($filterType === 'day') {
            $query .= " AND DATE(fecha_check_in) = '$singleDate'";
        } elseif ($filterType === 'month') {
            $query .= " AND MONTH(fecha_check_in) = MONTH('$singleDate') 
                       AND YEAR(fecha_check_in) = YEAR('$singleDate')";
        } elseif ($filterType === 'year') {
            $query .= " AND YEAR(fecha_check_in) = YEAR('$singleDate')";
        } elseif ($filterType === 'range') {
            $query .= " AND DATE(fecha_check_in) BETWEEN '$startDate' AND '$endDate'";
        }

        $query .= " ORDER BY fecha_check_in DESC";
        $resultado = $conexion->query($query);
        ?>
        <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No. Trabajador</th>
                    <th>Nombre</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $fila['no_trabajador']; ?></td>
                        <td><?php echo $fila['nombre']; ?></td>
                        <td><?php echo $fila['turno']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fecha'])); ?></td>
                        <td><?php echo date('H:i:s', strtotime($fila['hora'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>

    <script>
        document.getElementById('filter_type').addEventListener('change', function() {
            const singleDate = document.getElementById('single_date');
            const dateRange = document.getElementById('date_range');
            
            if (this.value === 'range') {
                singleDate.style.display = 'none';
                dateRange.style.display = 'block';
            } else {
                singleDate.style.display = 'block';
                dateRange.style.display = 'none';
            }
        });
    </script>
</body>
</html> 