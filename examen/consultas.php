<?php

require_once __DIR__ . "/conexion/conexion.php";

$consulta = isset($_GET['consulta']) ? $_GET['consulta'] : '';
$parametros = array();

// CONSULTAS DEL EXAMEN

switch ($consulta) {

    case 'a':
        $titulo = "A. Todos los departamentos";
        $sql = "SELECT * FROM DEPARTAMENTO";
        break;

    case 'b':
        $titulo = "B. Todos los empleados";
        $sql = "SELECT * FROM EMPLEADOS";
        break;

    case 'c':
        $titulo = "C. Empleados con apellido López";
        $sql = "SELECT * FROM EMPLEADOS
                WHERE Apellidos = :apellido";
        $parametros = array('apellido' => 'López');
        break;

    case 'd':
        $titulo = "D. Empleados López y Pérez";
        $sql = "SELECT * FROM EMPLEADOS
                WHERE Apellidos IN (:apellido1, :apellido2)";
        $parametros = array('apellido1' => 'López', 'apellido2' => 'Pérez');
        break;

    case 'e':
        $titulo = "E. Empleados del departamento 4";
        $sql = "SELECT Nombre, Clave_empleado
                FROM EMPLEADOS
                WHERE Clave_Depto = :departamento";
        $parametros = array('departamento' => 4);
        break;

    case 'f':
        $titulo = "F. Empleados del departamento 1 o 3";
        $sql = "SELECT * FROM EMPLEADOS
                WHERE Clave_Depto IN (:departamento1, :departamento2)
                ORDER BY Nombre ASC";
        $parametros = array('departamento1' => 1, 'departamento2' => 3);
        break;

    case 'g':
        $titulo = "G. Empleados cuyos apellidos comienzan con H";
        $sql = "SELECT Nombre, Apellidos
                FROM EMPLEADOS
                WHERE Apellidos LIKE :patron";
        $parametros = array('patron' => 'H%');
        break;

    case 'h':
        $titulo = "H. Departamentos con presupuesto entre 50000 y 70000";
        $sql = "SELECT * FROM DEPARTAMENTO
                WHERE Presupuesto BETWEEN :minimo AND :maximo";
        $parametros = array('minimo' => 50000, 'maximo' => 70000);
        break;

    case 'i':
        $titulo = "I. Empleados del departamento de Contabilidad";
        $sql = "SELECT E.Clave_empleado, E.Nombre, E.Apellidos
                FROM EMPLEADOS E
                INNER JOIN DEPARTAMENTO D
                ON E.Clave_Depto = D.Clave_Depto
                WHERE D.Nombre = :departamento";
        $parametros = array('departamento' => 'Contabilidad');
        break;

    case 'j':
        $titulo = "J. Empleados y sus departamentos";
        $sql = "SELECT E.Nombre, E.Apellidos,
                D.Nombre AS Departamento
                FROM EMPLEADOS E
                INNER JOIN DEPARTAMENTO D
                ON E.Clave_Depto = D.Clave_Depto";
        break;

    default:
        $titulo = "";
        $sql = "";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <title>Consultas - Personal</title>

    <style>
        body {
            font-family: Arial;
            background: #edf1f5;
            padding: 30px;
        }

        .contenedor {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1,
        h2 {
            color: #17365d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #17365d;
            color: white;
        }

        tr:nth-child(even) {
            background: #f0f0f0;
        }

        .boton {
            display: inline-block;
            background: #17365d;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>

</head>

<body>

    <div class="contenedor">

        <h1>SISTEMA DE PERSONAL</h1>

        <?php

        // MOSTRAR TABLAS COMPLETAS
        
        if ($consulta == 'tablas') {

            // TABLA DEPARTAMENTO
        
            echo "<h2>TABLA DEPARTAMENTO</h2>";

            $resultado = $conexion->prepare(
                "SELECT * FROM DEPARTAMENTO"
            );
            $resultado->execute();

            echo "<table>";

            echo "<tr>
            <th>Clave_Depto</th>
            <th>Nombre</th>
            <th>Presupuesto</th>
          </tr>";

            while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {

                echo "<tr>
                <td>" . htmlspecialchars($fila['Clave_Depto'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($fila['Nombre'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($fila['Presupuesto'], ENT_QUOTES, 'UTF-8') . "</td>
              </tr>";
            }

            echo "</table>";

            // TABLA EMPLEADOS
        
            echo "<h2>TABLA EMPLEADOS</h2>";

            $resultado = $conexion->prepare(
                "SELECT * FROM EMPLEADOS"
            );
            $resultado->execute();

            echo "<table>";

            echo "<tr>
            <th>Clave_empleado</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Clave_Depto</th>
          </tr>";

            while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {

                echo "<tr>
                <td>" . htmlspecialchars($fila['Clave_empleado'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($fila['Nombre'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($fila['Apellidos'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($fila['Clave_Depto'], ENT_QUOTES, 'UTF-8') . "</td>
              </tr>";
            }

            echo "</table>";

        }

        // MOSTRAR CONSULTAS A-J
        elseif ($sql != "") {

            echo "<h2>" . htmlspecialchars($titulo) . "</h2>";

            $resultado = $conexion->prepare($sql);
            $resultado->execute($parametros);

            echo "<table>";

            echo "<tr>";

            for ($indice = 0; $indice < $resultado->columnCount(); $indice++) {
                $campo = $resultado->getColumnMeta($indice);
                echo "<th>" . htmlspecialchars($campo['name'], ENT_QUOTES, 'UTF-8') . "</th>";
            }

            echo "</tr>";

            while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {

                echo "<tr>";

                foreach ($fila as $dato) {
                    echo "<td>" . htmlspecialchars((string) $dato, ENT_QUOTES, 'UTF-8') . "</td>";
                }

                echo "</tr>";
            }

            echo "</table>";

        } else {
            echo "<h2>Seleccione una consulta válida.</h2>";
        }

        ?>

        <br>

        <a class="boton" href="index.php">VOLVER AL INICIO</a>

    </div>

</body>

</html>