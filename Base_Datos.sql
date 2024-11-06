CREATE DATABASE IF NOT EXISTS empleados;

USE empleados;

CREATE TABLE IF NOT EXISTS `empleado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_trabajador` int(11) NOT NULL,
  `no_credencial` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `turno` varchar(20) NOT NULL,
  `check_in` tinyint(1) DEFAULT 0,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_check_in` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample data
INSERT INTO `empleado` (`no_trabajador`, `no_credencial`, `nombre`, `turno`, `check_in`, `eliminado`, `fecha_check_in`) VALUES
(1339, '0001177B', 'Juan Pérez', 'Matutino', 0, 1, NULL),
(1341, '0001269B', 'María López', 'Matutino', 0, 0, '2024-10-22 11:31:09');

