<?php
require_once __DIR__ . "/conexion/conexion.php";

$casos = array(
    'Tablas completas' => array(
        'tipo' => 'tablas',
        'codigo' => "SELECT * FROM DEPARTAMENTO;\n\nSELECT * FROM EMPLEADOS;"
    ),
    'A. Todos los departamentos' => array(
        'codigo' => "SELECT * FROM DEPARTAMENTO;",
        'sql' => "SELECT * FROM DEPARTAMENTO"
    ),
    'B. Todos los empleados' => array(
        'codigo' => "SELECT * FROM EMPLEADOS;",
        'sql' => "SELECT * FROM EMPLEADOS"
    ),
    'C. Empleados con apellido López' => array(
        'codigo' => "SELECT * FROM EMPLEADOS\nWHERE Apellidos = 'López';",
        'sql' => "SELECT * FROM EMPLEADOS WHERE Apellidos = :apellido",
        'parametros' => array('apellido' => 'López')
    ),
    'D. Empleados López y Pérez' => array(
        'codigo' => "SELECT * FROM EMPLEADOS\nWHERE Apellidos IN ('López', 'Pérez');",
        'sql' => "SELECT * FROM EMPLEADOS WHERE Apellidos IN (:apellido1, :apellido2)",
        'parametros' => array('apellido1' => 'López', 'apellido2' => 'Pérez')
    ),
    'E. Empleados del departamento 4' => array(
        'codigo' => "SELECT Nombre, Clave_empleado\nFROM EMPLEADOS\nWHERE Clave_Depto = 4;",
        'sql' => "SELECT Nombre, Clave_empleado FROM EMPLEADOS WHERE Clave_Depto = :departamento",
        'parametros' => array('departamento' => 4)
    ),
    'F. Empleados de los departamentos 1 y 3' => array(
        'codigo' => "SELECT * FROM EMPLEADOS\nWHERE Clave_Depto IN (1, 3)\nORDER BY Nombre ASC;",
        'sql' => "SELECT * FROM EMPLEADOS WHERE Clave_Depto IN (:departamento1, :departamento2) ORDER BY Nombre ASC",
        'parametros' => array('departamento1' => 1, 'departamento2' => 3)
    ),
    'G. Apellidos que comienzan con H' => array(
        'codigo' => "SELECT Nombre, Apellidos\nFROM EMPLEADOS\nWHERE Apellidos LIKE 'H%';",
        'sql' => "SELECT Nombre, Apellidos FROM EMPLEADOS WHERE Apellidos LIKE :patron",
        'parametros' => array('patron' => 'H%')
    ),
    'H. Presupuestos entre 50000 y 70000' => array(
        'codigo' => "SELECT * FROM DEPARTAMENTO\nWHERE Presupuesto BETWEEN 50000 AND 70000;",
        'sql' => "SELECT * FROM DEPARTAMENTO WHERE Presupuesto BETWEEN :minimo AND :maximo",
        'parametros' => array('minimo' => 50000, 'maximo' => 70000)
    ),
    'I. Empleados del departamento de Contabilidad' => array(
        'codigo' => "SELECT E.Clave_empleado, E.Nombre, E.Apellidos\nFROM EMPLEADOS E\nINNER JOIN DEPARTAMENTO D\nON E.Clave_Depto = D.Clave_Depto\nWHERE D.Nombre = 'Contabilidad';",
        'sql' => "SELECT E.Clave_empleado, E.Nombre, E.Apellidos FROM EMPLEADOS E INNER JOIN DEPARTAMENTO D ON E.Clave_Depto = D.Clave_Depto WHERE D.Nombre = :departamento",
        'parametros' => array('departamento' => 'Contabilidad')
    ),
    'J. Empleados y sus departamentos' => array(
        'codigo' => "SELECT E.Nombre, E.Apellidos,\nD.Nombre AS Departamento\nFROM EMPLEADOS E\nINNER JOIN DEPARTAMENTO D\nON E.Clave_Depto = D.Clave_Depto;",
        'sql' => "SELECT E.Nombre, E.Apellidos, D.Nombre AS Departamento FROM EMPLEADOS E INNER JOIN DEPARTAMENTO D ON E.Clave_Depto = D.Clave_Depto"
    )
);

function ejecutarConsulta($conexion, $sql, $parametros = array())
{
    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);
    return $consulta;
}

function mostrarTabla($consulta)
{
    echo "<table>";
    echo "<thead><tr>";

    for ($indice = 0; $indice < $consulta->columnCount(); $indice++) {
        $campo = $consulta->getColumnMeta($indice);
        echo "<th>" . htmlspecialchars($campo['name'], ENT_QUOTES, 'UTF-8') . "</th>";
    }

    echo "</tr></thead><tbody>";

    $filas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    if (count($filas) === 0) {
        echo "<tr><td colspan=\"" . $consulta->columnCount() . "\">Sin resultados</td></tr>";
    } else {
        foreach ($filas as $fila) {
            echo "<tr>";
            foreach ($fila as $dato) {
                echo "<td>" . htmlspecialchars((string) $dato, ENT_QUOTES, 'UTF-8') . "</td>";
            }
            echo "</tr>";
        }
    }

    echo "</tbody></table>";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Personal</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:12px;background:#edf1f5;font:14px Arial,sans-serif;color:#243447}
        .contenedor{max-width:1100px;margin:auto;background:#fff;border-radius:6px;overflow:hidden}
        header{padding:12px;background:#17365d;color:#fff;text-align:center}
        h1,h2,h3,h4{margin:0}
        h1{font-size:22px}
        main{padding:12px}
        .indice{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px}
        .indice a{padding:5px 8px;border:1px solid #b7c5d6;border-radius:4px;background:#eaf0f7;color:#17365d;text-decoration:none;font-weight:bold}
        .caso{margin-bottom:10px;border:1px solid #ccd6e2;border-radius:5px;overflow:hidden}
        .caso h2{padding:7px 10px;background:#17365d;color:#fff;font-size:15px}
        .contenido{display:grid;grid-template-columns:35% 65%}
        .codigo,.tabla{height:230px;overflow:auto}
        .codigo{border-right:1px solid #ccd6e2;padding:10px;background:#17202a}
        .codigo h3{margin-bottom:7px;color:#a9c7e8;font-size:13px}
        .codigo pre{margin:0;color:#fff;font:12px/1.4 Consolas,monospace;white-space:pre-wrap;user-select:text}
        .tabla{padding:8px}
        .tabla h3,.tabla h4{margin:4px 0 7px;color:#17365d}
        table{width:100%;border-collapse:collapse;font-size:13px}
        th,td{border:1px solid #ccd6e2;padding:6px;text-align:left}
        th{position:sticky;top:0;background:#17365d;color:#fff}
        tr:nth-child(even){background:#f2f5f8}
        @media(max-width:700px){body{padding:0}.contenedor{border-radius:0}.contenido{grid-template-columns:1fr}.codigo{border-right:0;border-bottom:1px solid #ccd6e2}.codigo,.tabla{height:200px}}
    </style>
</head>

<body>
    <div class="contenedor">
        <header>
            <h1>SISTEMA DE PERSONAL</h1>
        </header>

        <main>
            <nav class="indice">
                <?php $numeroCaso = 1; ?>
                <?php foreach ($casos as $titulo => $caso): ?>
                    <a href="#caso-<?= $numeroCaso ?>">
                        <?= $numeroCaso === 1 ? 'Tablas' : substr($titulo, 0, 1) ?>
                    </a>
                    <?php $numeroCaso++; ?>
                <?php endforeach; ?>
            </nav>

            <?php $numeroCaso = 1; ?>
            <?php foreach ($casos as $titulo => $caso): ?>
                <section class="caso" id="caso-<?= $numeroCaso ?>">
                    <h2><?= htmlspecialchars($titulo) ?></h2>
                    <div class="contenido">
                        <aside class="codigo">
                            <h3>Código SQL</h3>
                            <pre><?= htmlspecialchars($caso['codigo'], ENT_QUOTES, 'UTF-8') ?></pre>
                        </aside>

                        <div class="tabla">
                            <h3>Tabla</h3>
                            <?php
                            if (isset($caso['tipo']) && $caso['tipo'] === 'tablas') {
                                echo "<h4>DEPARTAMENTO</h4>";
                                mostrarTabla(ejecutarConsulta($conexion, "SELECT * FROM DEPARTAMENTO"));
                                echo "<h4>EMPLEADOS</h4>";
                                mostrarTabla(ejecutarConsulta($conexion, "SELECT * FROM EMPLEADOS"));
                            } else {
                                mostrarTabla(ejecutarConsulta(
                                    $conexion,
                                    $caso['sql'],
                                    isset($caso['parametros']) ? $caso['parametros'] : array()
                                ));
                            }
                            ?>
                        </div>
                    </div>
                </section>
                <?php $numeroCaso++; ?>
            <?php endforeach; ?>
        </main>
    </div>
</body>

</html>