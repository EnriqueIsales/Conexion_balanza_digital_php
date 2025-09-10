




drop table balanzas

-- Crea la tabla 'balanzas'
CREATE TABLE `balanzas` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`nombre` VARCHAR(100) NOT NULL,
`ip` VARCHAR(45) NOT NULL,
`puerto` INT(6) NOT NULL DEFAULT 8899,
`estado`  INT(6) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo',
`fecha_ingreso` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`fecha_modificacion` DATETIME default null,
`usuario_ingreso` INT(50) DEFAULT NULL,
`usuario_modificacion` INT(50) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


