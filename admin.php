<?php 
session_start();
// Prevenir que se pueda regresar con la flecha del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
setlocale(LC_TIME, 'es_MX.UTF-8');

// ✅ FUNCION DE FORMATO BONITO DE FECHAS
function traducirFecha($fecha) {
    $dias = [
        'Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves',
        'Friday' => 'Viernes', 'Saturday' => 'Sábado'
    ];
    $meses = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];

    $formato = date('l, d \d\e F \d\e Y H:i', strtotime($fecha));
    $formato = strtr($formato, $dias);
    $formato = strtr($formato, $meses);

    return $formato;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// VALIDACIÓN y NORMALIZACIÓN de filtros mes y año
$mesSeleccionado = $_GET['mes'] ?? '';
$anioSeleccionado = $_GET['anio'] ?? '';

// Validar mes
if (!empty($mesSeleccionado)) {
    if (!is_numeric($mesSeleccionado) || (int)$mesSeleccionado < 1 || (int)$mesSeleccionado > 12) {
        $mesSeleccionado = '';
    } else {
        $mesSeleccionado = (int)$mesSeleccionado;
    }
}

// Validar año
if (!empty($anioSeleccionado)) {
    if (!is_numeric($anioSeleccionado) || (int)$anioSeleccionado < 2000 || (int)$anioSeleccionado > 2100) {
        $anioSeleccionado = '';
    } else {
        $anioSeleccionado = (int)$anioSeleccionado;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupos = $pdo->query("SELECT * FROM grupos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (isset($_POST['eliminar_alumno'])) {
        $pdo->prepare("DELETE FROM alumnos WHERE id = ?")->execute([$_POST['eliminar_alumno']]);
    } elseif (isset($_POST['eliminar_maestro'])) {
        $pdo->prepare("DELETE FROM maestros WHERE id = ?")->execute([$_POST['eliminar_maestro']]);
    } elseif (isset($_POST['guardar_alumno'])) {
        $id = $_POST['id_alumno'];
        $nombre = trim($_POST['nombre_alumno']);
        $pass = trim($_POST['pass_alumno']);
        if ($pass !== '') {
            $passHash = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE alumnos SET nombre = ?, password = ? WHERE id = ?")->execute([$nombre, $passHash, $id]);
        } else {
            $pdo->prepare("UPDATE alumnos SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
        }
    } elseif (isset($_POST['guardar_maestro'])) {
        $id = $_POST['id_maestro'];
        $nombre = trim($_POST['nombre_maestro']);
        $pass = trim($_POST['pass_maestro']);
        if ($pass !== '') {
            $passHash = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE maestros SET nombre = ?, password = ? WHERE id = ?")->execute([$nombre, $passHash, $id]);
        } else {
            $pdo->prepare("UPDATE maestros SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
        }
    } elseif (isset($_POST['nuevo_alumno_nombre'], $_POST['nuevo_alumno_matricula'])) {
        $stmt = $pdo->prepare("INSERT INTO alumnos (nombre, matricula, fecha_registro) VALUES (?, ?, NOW())");
        $stmt->execute([trim($_POST['nuevo_alumno_nombre']), trim($_POST['nuevo_alumno_matricula'])]);
    } elseif (isset($_POST['nuevo_maestro_nombre'], $_POST['nuevo_maestro_empleado'])) {
        $stmt = $pdo->prepare("INSERT INTO maestros (nombre, empleado, fecha_registro) VALUES (?, ?, NOW())");
        $stmt->execute([trim($_POST['nuevo_maestro_nombre']), trim($_POST['nuevo_maestro_empleado'])]);
    }

    if (isset($_POST['crear_grupo']) && !empty(trim($_POST['nuevo_grupo']))) {
        $nombreGrupo = trim($_POST['nuevo_grupo']);
        $stmt = $pdo->prepare("INSERT INTO grupos (nombre) VALUES (?)");
        $stmt->execute([$nombreGrupo]);
    }

    if (isset($_POST['actualizar_grupo'], $_POST['grupo_id'], $_POST['nuevo_nombre_grupo'])) {
        $nuevoNombre = trim($_POST['nuevo_nombre_grupo']);
        $grupoId = (int) $_POST['grupo_id'];
        if ($nuevoNombre !== '') {
            $stmt = $pdo->prepare("UPDATE grupos SET nombre = ? WHERE id = ?");
            $stmt->execute([$nuevoNombre, $grupoId]);
        }
    }

    if (isset($_POST['eliminar_grupo'], $_POST['grupo_id'])) {
        $grupoId = (int) $_POST['grupo_id'];
        $stmt = $pdo->prepare("DELETE FROM grupos WHERE id = ?");
        $stmt->execute([$grupoId]);
    }

    $seccion = $_POST['seccion_actual'] ?? '';
    $fragmento = $seccion ? "#$seccion" : '';
    header("Location: admin.php$fragmento");
    exit();
}

$grupos = $pdo->query("SELECT * FROM grupos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$tipoUsuarioFiltro = $_GET['tipo_usuario'] ?? '';

if ($tipoUsuarioFiltro === 'maestro') {
    $alumnos = [];
} else {
    $sql = "SELECT * FROM alumnos WHERE 1=1";
    $params = [];

    if (!empty($_GET['grupo'])) {
        $sql .= " AND grupo = ?";
        $params[] = $_GET['grupo'];
    }
    if (!empty($_GET['hora'])) {
        $sql .= " AND hora = ?";
        $params[] = $_GET['hora'];
    }
    if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
        $sql .= " AND MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = ?";
        $params[] = $mesSeleccionado;
        $params[] = $anioSeleccionado;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$maestros = ($tipoUsuarioFiltro === 'alumno') ? [] : $pdo->query("SELECT * FROM maestros")->fetchAll(PDO::FETCH_ASSOC);

/* ------------------- FILTROS DE REPORTES ------------------- */
$laboratorioFiltro = $_GET['laboratorio'] ?? '';
$diaFiltro = $_GET['dia'] ?? '';
$mesFiltro = $_GET['mes'] ?? '';
$anioFiltro = $_GET['anio'] ?? '';

if (!is_numeric($diaFiltro) || $diaFiltro < 1 || $diaFiltro > 31) $diaFiltro = '';
if (!is_numeric($mesFiltro) || $mesFiltro < 1 || $mesFiltro > 12) $mesFiltro = '';
if (!is_numeric($anioFiltro) || $anioFiltro < 2000 || $anioFiltro > 2100) $anioFiltro = '';

$sqlReportes = "
    SELECT r.*, m.nombre AS maestro_nombre 
    FROM reportes r 
    JOIN maestros m ON r.maestro_id = m.id
    WHERE 1=1
";
$paramsReportes = [];

if ($laboratorioFiltro && in_array($laboratorioFiltro, ['1','2','3','4','5','6','7','8'])) {
    $sqlReportes .= " AND r.laboratorio = ?";
    $paramsReportes[] = $laboratorioFiltro;
}
if (!empty($diaFiltro)) {
    $sqlReportes .= " AND DAY(r.fecha) = ?";
    $paramsReportes[] = $diaFiltro;
}
if (!empty($mesFiltro)) {
    $sqlReportes .= " AND MONTH(r.fecha) = ?";
    $paramsReportes[] = $mesFiltro;
}
if (!empty($anioFiltro)) {
    $sqlReportes .= " AND YEAR(r.fecha) = ?";
    $paramsReportes[] = $anioFiltro;
}

$sqlReportes .= " ORDER BY r.id DESC";
$stmt = $pdo->prepare($sqlReportes);
$stmt->execute($paramsReportes);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
/* ------------------------------------------------------------ */

$fechas = $pdo->query("SELECT DISTINCT YEAR(fecha_ingreso) AS anio, MONTH(fecha_ingreso) AS mes FROM ingresos_usuarios ORDER BY anio DESC, mes DESC")->fetchAll(PDO::FETCH_ASSOC);

$query = "
    SELECT tipo, usuario_id, fecha_ingreso,
    CASE tipo
        WHEN 'alumno' THEN (SELECT nombre FROM alumnos WHERE id = usuario_id)
        WHEN 'maestro' THEN (SELECT nombre FROM maestros WHERE id = usuario_id)
    END AS nombre_usuario
    FROM ingresos_usuarios";

$params = [];

if (!empty($anioSeleccionado) && !empty($mesSeleccionado)) {
    $query .= " WHERE YEAR(fecha_ingreso) = ? AND MONTH(fecha_ingreso) = ?";
    $params = [$anioSeleccionado, $mesSeleccionado];
}

$query .= " ORDER BY fecha_ingreso DESC";
$ingresosFiltrados = $pdo->prepare($query);
$ingresosFiltrados->execute($params);
$ingresosFiltrados = $ingresosFiltrados->fetchAll(PDO::FETCH_ASSOC);

$nombreBonito = [
    'maquinas'        => 'Máquinas',
    'mouses'          => 'Mouses',
    'teclados'        => 'Teclados',
    'pc'              => 'PC',
    'monitores'       => 'Monitores',
    'cable_red'       => 'Cable de red',
    'multiconectores' => 'Multiconectores',
    'tv'              => 'TV',
    'hdmi'            => 'HDMI'
];

$ingresosLaboratorios = $pdo->query("
    SELECT a.id, a.tipo, a.laboratorio, a.fecha, a.hora,
        CASE a.tipo
            WHEN 'alumno' THEN (SELECT nombre FROM alumnos WHERE id = a.id_usuario)
            WHEN 'maestro' THEN (SELECT nombre FROM maestros WHERE id = a.id_usuario)
        END AS nombre_usuario
    FROM accesos a
    ORDER BY a.fecha DESC, a.hora DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <style>
        /* ... todo tu CSS existente sin cambios ... */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            min-height: 100vh;
            padding-top: 60px;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #0e3311;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
        }

        .navbar .logo {
            font-weight: bold;
            color: #aaffaa;
            font-size: 1.4em;
        }

        .navbar button {
            background-color: #2e4d2c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .container-admin {
            display: flex;
        }

        .sidebar {
            width: 220px;
            background-color: #001a00;
            padding: 20px;
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
        }

        .sidebar h2 {
            color: #aaffaa;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav ul li {
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            color: #ddd;
            transition: background-color 0.2s;
        }

        .sidebar nav ul li:hover {
            background-color: #145214;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: 100%;
        }

        .seccion {
            display: none;
        }

        .seccion.visible {
            display: block;
        }

        h2, h3, h4 {
            color: #aaffaa;
            margin-bottom: 10px;
        }

        ul {
            list-style: none;
            margin-bottom: 20px;
        }

        li {
            background-color: #222;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        input, select {
            background-color: #222;
            border: 1px solid #444;
            color: #ddd;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        button {
            background-color: #2e4d2c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
        }

        table {
            width: 100%;
            background-color: #222;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #444;
        }

        th {
            background-color: #2e4d2c;
            color: #fff;
        }

        /* Estilos para formulario inline usuarios */
        .usuario-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .usuario-form input[type="text"],
        .usuario-form input[type="password"] {
            background-color: #333;
            border: 1px solid #555;
            color: #eee;
            padding: 5px 8px;
            border-radius: 4px;
            display: none; /* Oculto inicialmente */
            width: 180px;
        }

        .usuario-form .nombre-text {
            flex-grow: 1;
        }
        .reporte-lista {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;           /* Para que salten de línea si no caben */
    gap: 15px;
    justify-content: flex-start;
}

.reporte-card {
    background-color: #2a2a2a;
    border: 1px solid #444;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 0 5px rgba(0,255,0,0.1);
    transition: transform 0.2s ease;
    width: fit-content;        /* Ajusta el ancho al contenido */
    max-width: 300px;          /* O límite si hay textos largos */
    min-width: 220px;          /* Para uniformidad visual */
    flex-shrink: 0;
}

.reporte-card:hover {
    transform: scale(1.02);
    box-shadow: 0 0 8px rgba(0,255,0,0.2);
}

.reporte-card h4 {
    margin-bottom: 8px;
    color: #aaffaa;
}

.reporte-card p {
    margin: 4px 0;
    line-height: 1.4;
}
.reporte-card ul{
    list-style:none;
    padding-left:0;
    margin:4px 0 8px;
}
.reporte-card li{
    margin:2px 0;
}
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">Panel Admin</div>
        <form action="logout.php" method="POST">
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>

    <div class="container-admin">
        <aside class="sidebar">
            <h2>Sistema de Laboratorio</h2>
            <nav>
                <ul>
                    <li onclick="mostrarSeccion('gestionar-grupos')">Gestionar Grupos</li>
                    <li onclick="mostrarSeccion('usuarios')">Usuarios</li>
                    <li onclick="mostrarSeccion('reportes')">Reportes</li>
                    <li onclick="mostrarSeccion('historial')">Historial</li>
                    <li onclick="mostrarSeccion('ingresos-lab')">Accesos a Laboratorios</li>
                    <li onclick="mostrarSeccion('qr')">Códigos QR</li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <section id="gestionar-grupos" class="seccion" style="display: none;">
    <h2>Gestionar Grupos</h2>

    <form method="POST" onsubmit="guardarSeccionActual();">
        <input type="hidden" name="seccion_actual" value="gestionar-grupos">
        <label>Nuevo grupo:</label>
        <input type="text" name="nuevo_grupo" placeholder="Nombre del grupo" required>
        <button type="submit" name="crear_grupo">Crear</button>
    </form>

    <hr>

    <?php if (!empty($grupos)): ?>
        <ul>
            <?php foreach ($grupos as $g): ?>
                <li>
                    <form method="POST" style="display: inline-block;" onsubmit="guardarSeccionActual(); return confirm('¿Confirmar acción?');">
                        <input type="hidden" name="seccion_actual" value="gestionar-grupos">
                        <input type="hidden" name="grupo_id" value="<?= $g['id'] ?>">
                        <input type="text" name="nuevo_nombre_grupo" value="<?= htmlspecialchars($g['nombre']) ?>" required>
                        <button type="submit" name="actualizar_grupo">Actualizar</button>
                        <button type="submit" name="eliminar_grupo">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay grupos registrados aún.</p>
    <?php endif; ?>
</section>

            <section id="usuarios" class="seccion">
    <form method="GET" action="admin.php" id="formFiltroAlumnos" style="margin-bottom:15px;" onsubmit="redirigirAUsuarios(event)">
        <label>Ver:</label>
        <select name="tipo_usuario" id="tipo_usuario" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="alumno" <?= ($_GET['tipo_usuario'] ?? '') == 'alumno' ? 'selected' : '' ?>>Solo alumnos</option>
            <option value="maestro" <?= ($_GET['tipo_usuario'] ?? '') == 'maestro' ? 'selected' : '' ?>>Solo maestros</option>
        </select>

        <!-- Filtros adicionales para alumnos -->
        <div id="filtrosAlumnos" style="display: none; margin-top: 10px;">
            <label>Grupo:</label>
            <select name="grupo">
                <option value="">Todos</option>
                <option value="A" <?= ($_GET['grupo'] ?? '') == 'A' ? 'selected' : '' ?>>Grupo A</option>
                <option value="B" <?= ($_GET['grupo'] ?? '') == 'B' ? 'selected' : '' ?>>Grupo B</option>
                <option value="C" <?= ($_GET['grupo'] ?? '') == 'C' ? 'selected' : '' ?>>Grupo C</option>
            </select>

            <label>Hora:</label>
            <select name="hora">
                <option value="">Todas</option>
                <option value="mañana" <?= ($_GET['hora'] ?? '') == 'mañana' ? 'selected' : '' ?>>Mañana</option>
                <option value="tarde" <?= ($_GET['hora'] ?? '') == 'tarde' ? 'selected' : '' ?>>Tarde</option>
            </select>

            <label>Mes:</label>
            <select name="mes">
                <option value="">Todos</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($_GET['mes'] ?? '') == $m ? 'selected' : '' ?>>
                        <?= ucfirst(strftime('%B', mktime(0, 0, 0, $m, 1))) ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label>Año:</label>
            <select name="anio">
                <option value="">Todos</option>
                <?php foreach (range(date('Y'), 2020) as $a): ?>
                    <option value="<?= $a ?>" <?= ($_GET['anio'] ?? '') == $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Aplicar filtros</button>
        </div>
    </form>


<h2>Usuarios Registrados</h2>

<?php if (!empty($alumnos)): ?>
    <h3>Alumnos</h3>
    <ul>
        <?php foreach ($alumnos as $a): ?>
            <li>
                <form method="POST" class="usuario-form" onsubmit="guardarSeccionActual(); return confirm('¿Confirmar acción?');">
                    <input type="hidden" name="id_alumno" value="<?= $a['id'] ?>">
                    <input type="hidden" name="seccion_actual" id="seccion_actual_input">
                    <span class="nombre-text"><?= htmlspecialchars($a['nombre']) ?> (<?= htmlspecialchars($a['matricula']) ?>)</span>
                    <input type="text" name="nombre_alumno" value="<?= htmlspecialchars($a['nombre']) ?>" class="nombre-input" autocomplete="off">
                    <input type="password" name="pass_alumno" placeholder="Nueva contraseña" class="pass-input" autocomplete="off">
                    <button type="button" class="btn-editar">Editar</button>
                    <button type="submit" name="guardar_alumno" class="btn-guardar" style="display:none;">Actualizar</button>
                    <button type="submit" name="eliminar_alumno" value="<?= $a['id'] ?>">Eliminar</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($maestros)): ?>
    <h3>Maestros</h3>
    <ul>
        <?php foreach ($maestros as $m): ?>
            <li>
                <form method="POST" class="usuario-form" onsubmit="guardarSeccionActual(); return confirm('¿Confirmar acción?');">
                    <input type="hidden" name="id_maestro" value="<?= $m['id'] ?>">
                    <input type="hidden" name="seccion_actual" id="seccion_actual_input">
                    <span class="nombre-text"><?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['empleado']) ?>)</span>
                    <input type="text" name="nombre_maestro" value="<?= htmlspecialchars($m['nombre']) ?>" class="nombre-input" autocomplete="off">
                    <input type="password" name="pass_maestro" placeholder="Nueva contraseña" class="pass-input" autocomplete="off">
                    <button type="button" class="btn-editar">Editar</button>
                    <button type="submit" name="guardar_maestro" class="btn-guardar" style="display:none;">Actualizar</button>
                    <button type="submit" name="eliminar_maestro" value="<?= $m['id'] ?>">Eliminar</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>



                            </ul> <!-- Maestros -->

            </section> <!-- ← Agrega este cierre para la sección de usuarios -->

         <section id="reportes" class="seccion">
    <h2>Reportes</h2>
    <form method="GET">
        <label>Laboratorio:</label>
        <select name="laboratorio" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php
            $nombresLaboratorios = [
                1 => 'Laboratorio 1',
                2 => 'Laboratorio 2',
                3 => 'Laboratorio 3',
                4 => 'Laboratorio 4',
                5 => 'Laboratorio 5',
                6 => 'Cisco',
                7 => 'Multimedia',
                8 => 'Servidor'
            ];

            foreach ($nombresLaboratorios as $i => $nombre):
            ?>
                <option value="<?= $i ?>" <?= ($laboratorioFiltro == $i) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Combo Día -->
        <label>Día:</label>
        <select name="dia" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php for ($d = 1; $d <= 31; $d++): ?>
                <option value="<?= $d ?>" <?= (isset($_GET['dia']) && $_GET['dia'] == $d) ? 'selected' : '' ?>>
                    <?= str_pad($d, 2, '0', STR_PAD_LEFT) ?>
                </option>
            <?php endfor; ?>
        </select>

        <!-- Combo Mes -->
        <label>Mes:</label>
        <select name="mes" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php 
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            foreach ($meses as $num => $nombreMes): ?>
                <option value="<?= $num ?>" <?= (isset($_GET['mes']) && $_GET['mes'] == $num) ? 'selected' : '' ?>>
                    <?= $nombreMes ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Combo Año -->
        <label>Año:</label>
        <select name="anio" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php 
            $anioActual = date('Y');
            for ($a = $anioActual; $a >= 2000; $a--): ?>
                <option value="<?= $a ?>" <?= (isset($_GET['anio']) && $_GET['anio'] == $a) ? 'selected' : '' ?>>
                    <?= $a ?>
                </option>
            <?php endfor; ?>
        </select>
    </form>

    <div class="reporte-lista">
        <?php if (empty($reportes)): ?>
            <p style="margin-top: 15px;">No hay reportes registrados.</p>
        <?php else: ?>
            <?php foreach ($reportes as $r): ?>
                <div class="reporte-card">
                    <?php
                    $nombreLab = $nombresLaboratorios[$r['laboratorio']] ?? 'Desconocido';
                    ?>
                    <h4><?= htmlspecialchars($nombreLab) ?></h4>

                    <p><strong>Maestro:</strong> <?= htmlspecialchars($r['maestro_nombre']) ?></p>

                    <?php
                    $comp = json_decode($r['componentes'] ?? '', true);
                    if (is_array($comp) && count($comp)):
                    ?>
                        <p><strong>Componentes:</strong></p>
                        <ul style="margin-left:18px;">
                            <?php foreach ($comp as $clave => $cantidad): ?>
                                <?php if ($cantidad !== '' && $cantidad !== null): ?>
                                    <li><?= $nombreBonito[$clave] ?? ucfirst($clave) ?>: <?= (int)$cantidad ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <p><strong>Observaciones:</strong><br><?= nl2br(htmlspecialchars($r['observaciones'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

            <section id="historial" class="seccion">
                <h2>Historial de Ingresos</h2>
                <form method="GET">
                    <label>Año:</label>
                    <select name="anio" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach (array_unique(array_column($fechas, 'anio')) as $anio): ?>
                            <option value="<?= $anio ?>" <?= ($anio == $anioSeleccionado) ? 'selected' : '' ?>><?= $anio ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Mes:</label>
                    <select name="mes" onchange="this.form.submit()">
    <option value="">Todos</option>
    <?php
    $meses_es = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
        4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    foreach ($meses_es as $numero => $nombre):
    ?>
        <option value="<?= $numero ?>" <?= ($mesSeleccionado == $numero) ? 'selected' : '' ?>>
            <?= $nombre ?>
        </option>
    <?php endforeach; ?>
</select>

                </form>

                <ul>
                    <?php foreach ($ingresosFiltrados as $ing): ?>
                        <li>
                            <?= ucfirst($ing['tipo']) ?>: <strong><?= htmlspecialchars($ing['nombre_usuario']) ?></strong> el <?= traducirFecha($ing['fecha_ingreso']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

                <section id="qr" class="seccion">
    <h2>Códigos QR</h2>

    <label for="selector-lab">Seleccionar laboratorio:</label>
    <select id="selector-lab" onchange="filtrarQR()">
        <option value="todos">Todos</option>
        <?php foreach ($nombresLaboratorios as $clave => $nombre): ?>
            <option value="lab<?= $clave ?>"><?= htmlspecialchars($nombre) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="reporte-lista" id="contenedor-qr">
        <?php foreach ($nombresLaboratorios as $clave => $nombre): ?>
            <div class="reporte-card qr-lab lab<?= $clave ?>">
                <h4><?= $nombre ?> - Alumnos</h4>
                <img src="ver_qr_laboratorio.php?tipo=alumno&lab=<?= $clave ?>" alt="QR alumnos <?= $nombre ?>" width="140" height="140">
            </div>
            <div class="reporte-card qr-lab lab<?= $clave ?>">
                <h4><?= $nombre ?> - Maestros</h4>
                <img src="ver_qr_laboratorio.php?tipo=maestro&lab=<?= $clave ?>" alt="QR maestros <?= $nombre ?>" width="140" height="140">
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function filtrarQR() {
    const seleccion = document.getElementById("selector-lab").value;
    document.querySelectorAll(".qr-lab").forEach(el => {
        el.style.display = (seleccion === "todos" || el.classList.contains(seleccion)) ? "block" : "none";
    });
}
document.addEventListener("DOMContentLoaded", filtrarQR);
</script>


<section id="ingresos-lab" class="seccion">
    <h2>Registro de Accesos a Laboratorios</h2>

    <?php if (empty($ingresosLaboratorios)): ?>
        <p>No hay registros aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Laboratorio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingresosLaboratorios as $ing): ?>
                    <tr>
                        <td><?= htmlspecialchars($ing['nombre_usuario']) ?></td>
                        <td><?= ucfirst($ing['tipo']) ?></td>
                        <td><?= htmlspecialchars($ing['laboratorio']) ?></td>
                        <td><?= htmlspecialchars($ing['fecha']) ?></td>
                        <td><?= htmlspecialchars($ing['hora']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

        </main>
    </div>

<script>
    function mostrarSeccion(id) {
        document.querySelectorAll('.seccion').forEach(s => s.classList.remove('visible'));
        document.getElementById(id).classList.add('visible');
        localStorage.setItem('seccionActual', id); // Guardar sección actual
    }

    // Al cargar la página, restaurar sección desde localStorage si no hay parámetros GET
    const params = new URLSearchParams(window.location.search);

    if (params.has('laboratorio')) {
        mostrarSeccion('reportes');
    } else if (params.has('anio') || params.has('mes')) {
        mostrarSeccion('historial');
    } else if (params.has('tipo_usuario')) {
        mostrarSeccion('usuarios');
    } else {
        const ultimaSeccion = localStorage.getItem('seccionActual') || 'usuarios';
        mostrarSeccion(ultimaSeccion);
    }
</script>


<script>
document.querySelectorAll('.btn-editar').forEach(function(botonEditar) {
    botonEditar.addEventListener('click', function () {
        const form = botonEditar.closest('form');
        const nombreInput = form.querySelector('.nombre-input');
        const passInput = form.querySelector('.pass-input');
        const btnGuardar = form.querySelector('.btn-guardar');

        // Mostrar los campos de edición y el botón guardar
        nombreInput.style.display = 'inline-block';
        passInput.style.display = 'inline-block';
        btnGuardar.style.display = 'inline-block';

        // Opcional: ocultar el nombre de texto plano
        const nombreTexto = form.querySelector('.nombre-text');
        if (nombreTexto) nombreTexto.style.display = 'none';
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const tipoSelect = document.getElementById('tipo_usuario');
    const filtros = document.getElementById('filtrosAlumnos');

    function toggleFiltros() {
        filtros.style.display = tipoSelect.value === 'alumno' ? 'block' : 'none';
    }

    tipoSelect.addEventListener('change', toggleFiltros);
    toggleFiltros(); // Ejecutar al inicio
});
function guardarSeccionActual() {
    const seccion = localStorage.getItem('seccionActual') || 'usuarios';
    const input = document.getElementById('seccion_actual_input');
    if (input) input.value = seccion;
}
document.addEventListener('DOMContentLoaded', () => {
    const hash = location.hash.replace('#', '');

    if (hash && document.getElementById(hash)) {
        mostrarSeccion(hash);
    } else if (new URLSearchParams(location.search).has('tipo_usuario')) {
        mostrarSeccion('usuarios');
    } else if (new URLSearchParams(location.search).has('laboratorio')) {
        mostrarSeccion('reportes');
    } else if (new URLSearchParams(location.search).has('anio') || new URLSearchParams(location.search).has('mes')) {
        mostrarSeccion('historial');
    } else {
        mostrarSeccion(localStorage.getItem('seccionActual') || 'usuarios');
    }
});
function redirigirAUsuarios(event) {
    event.preventDefault(); // Evita el envío normal
    const form = document.getElementById('formFiltroAlumnos');
    const url = new URL(form.action, window.location.origin);

    // Agregar los valores actuales de los campos al URL
    new FormData(form).forEach((value, key) => {
        if (value) url.searchParams.set(key, value);
    });

    // Redirigir incluyendo el fragmento #usuarios
    window.location.href = url.toString() + '#usuarios';
}
function mostrarSeccion(id) {
    document.querySelectorAll('.seccion').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    // Guarda en localStorage para mantener la sección activa tras recargar
    localStorage.setItem('seccionActiva', id);
}

function guardarSeccionActual() {
    const seccionActiva = document.querySelector('.seccion:not([style*="display: none"])');
    if (seccionActiva) {
        document.querySelectorAll('input[name="seccion_actual"]').forEach(input => {
            input.value = seccionActiva.id;
        });
    }
}

// Restaurar la última sección activa al cargar
window.addEventListener('DOMContentLoaded', () => {
    const activa = localStorage.getItem('seccionActiva');
    if (activa) mostrarSeccion(activa);
});
</script>
</body>
</html>