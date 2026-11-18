CREATE DATABASE PERSONAL
DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_spanish_ci;

USE PERSONAL;

SELECT * FROM DEPARTAMENTO

SELECT * FROM EMPLEADOS

SELECT * FROM EMPLEADOS
                WHERE Apellidos = 'López'

SELECT * FROM EMPLEADOS
                WHERE Apellidos IN ('López', 'Pérez')


SELECT Nombre, Clave_empleado
                FROM EMPLEADOS
                WHERE Clave_Depto =4;

SELECT * FROM EMPLEADOS
                WHERE Clave_Depto IN (1, 3)
                ORDER BY Nombre ASC;
    
SELECT Nombre, Apellidos
                FROM EMPLEADOS
                WHERE Apellidos LIKE 'H%';  

SELECT * FROM DEPARTAMENTO
                WHERE Presupuesto BETWEEN 50000 AND 70000;

ELECT E.Clave_empleado, E.Nombre, E.Apellidos
                FROM EMPLEADOS E
                INNER JOIN DEPARTAMENTO D
                ON E.Clave_Depto = D.Clave_Depto
                WHERE D.Nombre = 'Contabilidad';

SELECT E.Nombre, E.Apellidos,
                D.Nombre AS Departamento
                FROM EMPLEADOS E
                INNER JOIN DEPARTAMENTO D
                ON E.Clave_Depto = D.Clave_Depto;