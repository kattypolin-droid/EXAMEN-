<?php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "PERSONAL";

try {
    $conexion = new PDO(
        "mysql:host=" . $host . ";charset=utf8mb4",
        $usuario,
        $contrasena,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    $conexion->exec(
        "CREATE DATABASE IF NOT EXISTS `" . $baseDatos . "`
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
    $conexion->exec("USE `" . $baseDatos . "`");

    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS DEPARTAMENTO (
            Clave_Depto INT PRIMARY KEY,
            Nombre VARCHAR(30),
            Presupuesto INT
        )"
    );

    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS EMPLEADOS (
            Clave_empleado VARCHAR(8) PRIMARY KEY,
            Nombre VARCHAR(30),
            Apellidos VARCHAR(30),
            Clave_Depto INT,
            FOREIGN KEY (Clave_Depto)
            REFERENCES DEPARTAMENTO(Clave_Depto)
        )"
    );

    $conexion->exec(
        "INSERT IGNORE INTO DEPARTAMENTO
        (Clave_Depto, Nombre, Presupuesto) VALUES
        (1, 'Personal', 80000),
        (2, 'Almacén', 70000),
        (3, 'Contabilidad', 60000),
        (4, 'Manufactura', 50000),
        (5, 'Empaque', 30000)"
    );

    $conexion->exec(
        "INSERT IGNORE INTO EMPLEADOS
        (Clave_empleado, Nombre, Apellidos, Clave_Depto) VALUES
        ('EMP01', 'Armando', 'López', 2),
        ('EMP02', 'Tatiana', 'Vargas', 1),
        ('EMP03', 'Laura', 'Iturria', 3),
        ('EMP04', 'Juan', 'Pérez', 4),
        ('EMP05', 'Iván', 'López', 4),
        ('EMP06', 'Margarita', 'Hernández', 1),
        ('EMP07', 'Jesús', 'Pérez', 3),
        ('EMP08', 'Tonatiuh', 'Flores', 2),
        ('EMP09', 'Juan', 'López', 4),
        ('EMP10', 'José', 'Hernández', 5)"
    );
} catch (PDOException $error) {
    die("Error de conexión o configuración de la base de datos: " . $error->getMessage());
}

?>
