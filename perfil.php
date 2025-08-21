<?php 
session_start();
require_once 'db.php';
// Prevenir que se pueda regresar con la flecha del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Recuperar y borrar la notificación para que aparezca solo una vez
$notificacion = $_SESSION['notificacion'] ?? null;
unset($_SESSION['notificacion']);

// Validar sesión
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$fila = $_SESSION['user'];

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    // Redirigir al login si no está logueado
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['user'];
$nombre = $usuario['nombre'];
$tipo = $usuario['tipo'];

// Detecta automáticamente si es alumno o maestro
if ($tipo === 'alumno') {
    $usuario_id = $usuario['matricula'] ?? $usuario['usuario'] ?? '';
} elseif ($tipo === 'maestro') {
    $usuario_id = $usuario['empleado'] ?? $usuario['usuario'] ?? '';
} else {
    $usuario_id = $usuario['usuario'] ?? '';
}

$usuario_interno_id = $usuario['id'];
$password = $usuario['password'] ?? '';   // ← cambio aquí



// Obtener lista de grupos para el combo en editar perfil
$grupos_disponibles = $pdo->query("SELECT nombre FROM grupos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_COLUMN);

// Actualizar perfil alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar_perfil') {
    $nuevo_nombre = $_POST['nuevo_nombre'] ?? '';
    $nuevo_grupo = $_POST['nuevo_grupo'] ?? '';
    $nueva_password = $_POST['nueva_password'] ?? '';

    if ($nuevo_nombre && $nuevo_grupo) {
        $sql = "UPDATE alumnos SET nombre = ?, grupo = ?" . ($nueva_password ? ", password = ?" : "") . " WHERE id = ?";
        $params = [$nuevo_nombre, $nuevo_grupo];
        if ($nueva_password) {
            $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $params[] = $hash;
        }
        $params[] = $usuario_interno_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['user']['nombre'] = $nuevo_nombre;
        $_SESSION['user']['grupo'] = $nuevo_grupo;
        if ($nueva_password) {
            $_SESSION['user']['password'] = $hash;
        }

        echo "<script>alert('✅ Perfil actualizado correctamente.');</script>";
        $nombre = $nuevo_nombre;
    }
}



// Manejo de reportes (actualizar y eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'actualizar') {
    $id = $_POST['id'];
    $observaciones = $_POST['observaciones'] ?? '';
    $componentes = $_POST['componentes'] ?? [];
    $jsonComponentes = json_encode($componentes);
    $stmt = $pdo->prepare("UPDATE reportes SET observaciones = ?, componentes = ? WHERE id = ? AND maestro_id = ?");
    $stmt->execute([$observaciones, $jsonComponentes, $id, $usuario_interno_id]);

    $_SESSION['mensaje'] = '✅ Reporte actualizado correctamente.';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if ($_POST['accion'] === 'eliminar') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM reportes WHERE id = ? AND maestro_id = ?");
    $stmt->execute([$id, $usuario_interno_id]);

    $_SESSION['mensaje'] = '🗑️ Reporte eliminado correctamente.';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

}

require_once 'libs/qrlib.php';
$qrData = json_encode(['usuario' => $usuario_id, 'password' => $password]);
$filename = 'temp/' . $usuario_id . '_qr.png';
QRcode::png($qrData, $filename, 'H', 10, 2);


$laboratorios = $alumnos = [];
if ($tipo === 'maestro') {
    $laboratorios = $pdo->query("SELECT * FROM laboratorios")->fetchAll(PDO::FETCH_ASSOC);
    $alumnos = $pdo->query("SELECT * FROM alumnos")->fetchAll(PDO::FETCH_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="diseño.css">
</head>
<!-- …encima ya tienes tu <link rel="stylesheet" …> -->
<style>

/* ====  Cards para “Mis Reportes”  =============================== */
.contenedor-cards-reportes{
    /* fila ordenada, con salto automático si no caben */
    display:flex;
    flex-wrap:wrap;
    gap:20px;                /* separación horizontal y vertical */
    justify-content:flex-start; /* cambia a center/right si lo prefieres */
}

.card-reporte{
    background:#1e1e1e;      /* negro grisáceo • armoniza con tu fondo */
    border:1px solid #ff8c00ff;/* tono verde del navbar */
    border-radius:8px;
    width:310px;             /* 3 cards ≈ 930 px. Ajusta a tu gusto */
    padding:16px;
    box-shadow:0 3px 6px rgba(0,0,0,.4);
    display:flex;            /* para empujar los botones al fondo */
    flex-direction:column;
    row-gap:12px;
}

.card-reporte h3{
    margin:0 0 6px 0;
    font-size:1.2rem;
    color:#ff8c00ff;          
}

.card-reporte p{ margin:0 0 10px 0; }

/* ====== Componentes y observaciones ============================= */
.componentes{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.card-reporte textarea{
    width:100%;
    min-height:72px;
    resize:vertical;
    border-radius:4px;
    padding:6px 8px;
    background:#2b2b2b;
    color:#fff;
    border:1px solid #444;
}

/* ====== Área de acciones (botones) ============================== */
.card-reporte .acciones{
    margin-top:auto;         /* empuja a la parte baja de la card */
    display:flex;
    gap:10px;
}

.card-reporte button{
    flex:1;
    border:none;
    border-radius:4px;
    padding:8px 0;
    font-weight:600;
    cursor:pointer;
}

.card-reporte button[type="submit"]{     /* “Actualizar” */
    background:#2e4d2c;
    color:#fff;
}

.card-reporte .btn-eliminar{             /* “Eliminar”  */
    background:#a62f2f;
    color:#fff;
}

.card-reporte button:hover{
    filter:brightness(1.1);
}
.card-reporte,
.card-laboratorio{          /* ← usa aquí tu propio selector */
    width:260px;           /* antes 310 px – más compacto   */
    padding:14px;          /* un pelín menos de relleno     */
}

/* Opcional: si quieres que los títulos y textos se vean         */
/* proporcionados con la nueva anchura, baja un poco la fuente.  */
.card-reporte h3,
.card-laboratorio h3{
    font-size:1.05rem;     /* antes 1.2 rem                 */
}

.card-reporte p,
.card-laboratorio p{
    font-size:0.92rem;
}

/* Si tu contenedor se ve muy espacioso al achicar las tarjetas,   */
/* prueba también con un gap menor:                                */
.contenedor-cards-reportes{
    gap:16px;              /* antes 20 px – opcional         */
}

table td{
    padding: 10px 15px;
}

table th {
    padding: 10px 15px;
}

table td:nth-child(2){
    white-space:nowrap;
}

view-mode-switch {
    position: absolute;
    bottom: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

.view-mode-switch button {
    background-color: transparent;
    border: none;
    font-size: 22px;
    color: #ccc;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.3s;
}

.view-mode-switch button:hover {
    color: #fff;
    transform: scale(1.2);
    box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.3);
}

/* Botón menú hamburguesa */
#toggleMenuBtn {
    display: none;
    position: absolute;
    top: 15px;
    left: 10px;
    font-size: 26px;
    background: transparent;
    border: none;
    color: white;
    z-index: 1100;
    cursor: pointer;
}

/* ====== Estilo para el formulario de editar perfil del alumno ====== */
#editar-perfil form {
    background-color: #0f0f0fff;
    border: 1px solid #2e4d2c;
    border-radius: 10px;
    padding: 20px;
    max-width: 480px;
    margin-top: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
}

#editar-perfil h2 {
    color: #aaffaa;
    margin-bottom: 20px;
}

#editar-perfil label {
    display: block;
    margin-bottom: 15px;
    color: #ddd;
    font-weight: bold;
}

#editar-perfil input[type="text"],
#editar-perfil input[type="password"] {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    background-color: #2b2b2b;
    border: 1px solid #444;
    color: #fff;
    box-sizing: border-box;
}
#editar-perfil select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    background-color: #2b2b2b;
    border: 1px solid #444;
    color: #fff;
    box-sizing: border-box;
    appearance: none;
}

#editar-perfil button[type="submit"] {
    display: inline-block;
    background-color: #2e4d2c;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

#editar-perfil button[type="submit"]:hover {
    background-color: #3d6b3a;
}
#mensaje-alerta {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #ff8c00ff;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    font-weight: bold;
    z-index: 9999;
    animation: aparecer 0.5s ease, desaparecer 0.5s ease 3.5s;
}

@keyframes aparecer {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes desaparecer {
    from { opacity: 1; }
    to { opacity: 0; transform: translateY(-10px); }
}
html, body {
    overflow-x: hidden; /* Evita scroll horizontal */
}
.card-reporte-formulario {
    min-height: 100px;
}

#perfil-maestro select,
#perfil-maestro input,
#perfil-maestro textarea {
    background-color: #75f075; /* Verde que quieres */
    color: white;              /* Texto blanco */
    border: 1px solid #444;
    border-radius: 6px;
    padding: 6px 10px;
    appearance: none;
}//

#perfil-maestro select option {
    background-color: #75f075; /* Para que el desplegable también sea verde */
    color: white;
}

.notificacion {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    animation: aparecer 0.5s ease-in-out;
}
@keyframes aparecer {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>

<body>
<script>
async function labIni() {
            const e = document.getElementById('laboratorio');
            const labId = e.value;
            //alert(labId);
            const caja  = document.getElementById('caja-reporte');
            if (!labId) { caja.innerHTML = ''; return; }

            caja.textContent = 'Cargando…';
            try {
                const fd = new FormData();  fd.append('laboratorio', labId);
                const res = await fetch('cargar_reporte.php', { method:'POST', body:fd });
                caja.innerHTML = await res.text();
            } catch (err) {
                caja.innerHTML = '<p style="color: #faa;">No se pudo cargar el contenido. Revisa tu conexión.</p>';
            }
}
</script>
    <!-- Mensaje flotante -->
<div id="mensaje-flotante" style="
  visibility: hidden;
  min-width: 250px;
  background-color: #ff8c00ff;
  color: #aaffaa;
  text-align: center;
  border-radius: 8px;
  padding: 12px 20px;
  position: fixed;
  z-index: 9999;
  right: 20px;
  top: 20px;
  font-weight: bold;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
  opacity: 0;
  transition: opacity 0.5s ease;
"></div>
    <?php if (isset($_SESSION['mensaje'])): ?>
<script>
    alert("<?php echo addslashes($_SESSION['mensaje']); ?>");
</script>
<?php unset($_SESSION['mensaje']); endif; ?>
    <button id="toggleMenuBtn" onclick="toggleSidebar()">☰</button>
<header class="navbar">
    <div class="logo">Sistema de Laboratorio</div>
    <div class="user-info">
        <span><?php echo ucfirst($tipo); ?>: <?php echo $nombre; ?></span>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
    </header>
    

<aside class="sidebar">
    <?php if ($tipo === 'maestro'): ?>
        <div id="perfil-maestro">
        
    <?php endif; ?>
    <ul> 
    <li><a href="#inicio">Inicio</a></li>
    <?php if ($tipo === 'maestro'): ?>
        <li><a href="#reporte">Hacer Reporte</a></li>
        <li><a href="#lista">Lista de Alumnos</a></li>
        <li><a href="#mis-reportes">Mis Reportes</a></li>
    <?php else: ?>
        <li><a href="#editar-perfil">Editar Perfil</a></li>
    <?php endif; ?>
    <li><a href="#qr-container">Mostrar/Esconder QR</a></li>
</ul>

</aside>


<main class="contenido">
    
   <section id="inicio">
        <h1>Bienvenido, <?php echo $nombre; ?></h1>
        <p>Este es tu panel como <?php echo $tipo; ?>.</p>
    </section>

    <section id="qr-container" style="display:none; margin-top: 20px;">
    <div style="background-color: #222; padding: 20px; border-radius: 10px; max-width: 380px;">
        <h3 style="color: #ddd;">Escanea este QR para iniciar sesión</h3>
        <img src="<?php echo $filename; ?>" alt="QR para iniciar sesión"
             style="width: 350px; max-width: 100%; border: 4px solid #2e4d2c; border-radius: 10px; margin-top: 10px;">
        <br>
        <a href="<?php echo $filename; ?>" download="qr_<?php echo $usuario_id; ?>.png" style="
            display: inline-block;
            margin-top: 10px;
            background-color: #ff8c00ff;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        ">📥 Descargar QR</a>
    </div>
</section>


    <?php if ($tipo === 'maestro'): ?>
    <section id="reporte" style="display:none;">
    <h2 style="margin-bottom: 20px; color: #aaffaa;">Reporte de Laboratorio</h2>

    <div class="card-reporte-seleccion">
        <label for="laboratorio"><strong>Selecciona laboratorio:</strong></label>
        <select id="laboratorio" name="laboratorio">
            <option value="">-- Selecciona --</option>
            <?php foreach ($laboratorios as $lab): ?>
                <option value="<?php echo $lab['id'];?>" 
                <?php
                if (isset($_SESSION['laboratorio'])) {
                    if(htmlspecialchars($lab['nombre']) == $_SESSION['laboratorio']) { 
                        echo "selected";
                    }  
                }
                ?>><?php echo htmlspecialchars($lab['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="caja-reporte" class="card-reporte-formulario"></div>
        <script>labIni();</script>
</section>


   <section id="lista" style="display: none;">
    <h2>Lista de Alumnos Registrados</h2>

    <label for="filtro-grupo">Filtrar por grupo:</label>
    <select id="filtro-grupo" onchange="filtrarTabla()">
        <option value="todos">Todos</option>
        <?php
       $stmt = $pdo->query("SELECT nombre FROM grupos ORDER BY nombre ASC");
$grupos = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($grupos as $grupo) {
    $grupoSafe = htmlspecialchars($grupo);
    echo "<option value=\"$grupoSafe\">$grupoSafe</option>";
}

        ?>
    </select>

    <br><br>
    <label for="filtro-mes">Mes:</label>
    <select id="filtro-mes" onchange="filtrarTabla()">
        <option value="">Todos</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
        <?php endfor; ?>
    </select>

    <label for="filtro-anio">Año:</label>
    <select id="filtro-anio" onchange="filtrarTabla()">
        <option value="">Todos</option>
        <?php
        $anios = array_unique(array_map(function($a) {
            return (new DateTime($a['fecha_registro']))->format('Y');
        }, $alumnos));
        sort($anios);
        foreach ($anios as $anio) {
            echo "<option value=\"$anio\">$anio</option>";
        }
        ?>
    </select>

    <label for="filtro-hora">Hora exacta:</label>
    <input type="time" id="filtro-hora" onchange="filtrarTabla()">

    <table>
        <thead>
            <tr style="background-color: #ff8c00ff;">
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Grupo</th>
                <th>Laboratorio</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody id="tabla-alumnos">
            <?php foreach ($alumnos as $a): ?>
                <?php
                    $fecha = new DateTime($a['fecha_registro']);
                    $mes = $fecha->format('n');
                    $anio = $fecha->format('Y');
                    $hora = $fecha->format('H:i');
                ?>
                <tr data-grupo="<?php echo htmlspecialchars($a['grupo']); ?>"
                    data-mes="<?php echo $mes; ?>"
                    data-anio="<?php echo $anio; ?>"
                    data-hora="<?php echo $hora; ?>">
                    <td><?php echo htmlspecialchars($a['matricula']); ?></td>
                    <td><?php echo htmlspecialchars($a['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($a['grupo']); ?></td>
                    <td><?php echo htmlspecialchars($a['laboratorio']); ?></td>
                    <td><?php echo $fecha->format('d/m/Y H:i'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>



    <section id="mis-reportes" style="display: none;">
    <h2>Mis Reportes Enviados</h2>
    <div class="contenedor-cards-reportes">
        <?php
        $stmt = $pdo->prepare("SELECT r.id, r.laboratorio AS lab_id, l.nombre AS laboratorio, r.observaciones, r.componentes 
                               FROM reportes r 
                               JOIN laboratorios l ON r.laboratorio = l.id 
                               WHERE r.maestro_id = ?");
        $stmt->execute([$usuario_interno_id]);
        $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $contador = 1;

        foreach ($reportes as $rep): 
            $componentes = json_decode($rep['componentes'], true) ?: [];
            $stmtComp = $pdo->prepare("SELECT componente FROM laboratorio_componentes WHERE laboratorio_id = ?");
            $stmtComp->execute([$rep['lab_id']]);
            $estructura = $stmtComp->fetchAll(PDO::FETCH_COLUMN);
        ?>
            <div class="card-reporte">
                <form method="POST" class="form-reporte">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?php echo $rep['id']; ?>">

                    <h3>Reporte #<?php echo $contador++; ?></h3>
                    <p><strong>Laboratorio:</strong> <?php echo htmlspecialchars($rep['laboratorio']); ?></p>

                    <div class="componentes">
                        <?php foreach ($estructura as $componente): 
                            $cantidad = $componentes[$componente] ?? 1;
                            $nombreMostrar = ucfirst(str_replace('_', ' ', $componente));
                        ?>
                            <label>
                                <?php echo $nombreMostrar; ?>:
                                <select name="componentes[<?php echo htmlspecialchars($componente); ?>]">
                                    <?php for ($i = 1; $i <= 80; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php if ($i == $cantidad) echo 'selected'; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <label>Observaciones:
                        <textarea name="observaciones" rows="3"><?php echo htmlspecialchars($rep['observaciones']); ?></textarea>
                    </label>

                    <div class="acciones">
                        <button type="submit">Actualizar</button>
                </form>
                <form method="POST" onsubmit="return confirm('¿Eliminar este reporte?');">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?php echo $rep['id']; ?>">
                    <button type="submit" class="btn-eliminar">Eliminar</button>
                </form>
                    </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
</div>
    <?php endif; ?>

    <?php if ($tipo === 'alumno'): ?>
    <section id="editar-perfil" style="display:none;">
        <h2>Editar Perfil</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="editar_perfil">
            <label>Nombre:<br>
                <input type="text" name="nuevo_nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </label><br><br>
            <label>Grupo:<br>
    <select name="nuevo_grupo" required>
        <option value="">-- Selecciona tu grupo --</option>
        <?php foreach ($grupos_disponibles as $grupo): ?>
            <option value="<?php echo htmlspecialchars($grupo); ?>" 
                <?php echo (isset($usuario['grupo']) && $usuario['grupo'] === $grupo) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($grupo); ?>
            </option>
        <?php endforeach; ?>
    </select>
</label><br><br>
            <label>Nueva Contraseña:<br>
                <input type="password" name="nueva_password" placeholder="Dejar en blanco para no cambiar">
            </label><br><br>
            <button type="submit">Guardar Cambios</button>
        </form>
    </section>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    /* ---- utilidades ------------------------------------------ */
    const SECTIONS = document.querySelectorAll('main > section');

    function mostrarSeccion(id) {
        SECTIONS.forEach(sec => {
            sec.style.display = (sec.id === id) ? 'block' : 'none';
        });
    }

    function seleccionarPorHash() {
        const id = location.hash.replace('#', '') || 'inicio';
        mostrarSeccion(id);
    }

    /* ---- arrancar y escuchar cambios de hash ----------------- */
    seleccionarPorHash();
    window.addEventListener('hashchange', seleccionarPorHash);


    /* ---- mostrar / ocultar menú hamburguesa ------------------ */
    window.toggleSidebar = () =>
        document.querySelector('.sidebar').classList.toggle('activa');

    /* -----------------------------------------------------------
       = SECCIÓN REPORTE (solo maestros)
       ----------------------------------------------------------- */
    const selector = document.getElementById('laboratorio');
    if (selector) {                             // existe solo para maestro
        selector.addEventListener('change', async e => {
            const labId = e.target.value;
            const caja  = document.getElementById('caja-reporte');
            if (!labId) { caja.innerHTML = ''; return; }

            caja.textContent = 'Cargando…';
            try {
                const fd = new FormData();  fd.append('laboratorio', labId);
                const res = await fetch('cargar_reporte.php', { method:'POST', body:fd });
                caja.innerHTML = await res.text();
            } catch (err) {
                caja.innerHTML = '<p style="color: #faa;">No se pudo cargar el contenido. Revisa tu conexión.</p>';
            }
        });

        /* guardar nuevo reporte */
        document.addEventListener('submit', async e => {
            if (e.target.id !== 'form-reporte-lab') return;
            e.preventDefault();

            try {
                const res = await fetch('guardar_reporte.php', {
                    method:'POST', body:new FormData(e.target)
                });
                const txt = await res.text();
                mostrarMensaje(
                    txt.includes('Éxito')
                      ? '✅ Reporte enviado correctamente.'
                      : '❌ Hubo un error al enviar el reporte.'
                );
                e.target.reset();
            } catch { mostrarMensaje('❌ Error de conexión.'); }
        });
    }

    /* ---- enlace Mostrar/Esconder QR -------------------------- */
    document.querySelectorAll('a[href="#qr-container"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();          // evita salto de página
            location.hash = '#qr-container';  // aprovecha el mismo sistema
        });
    });
});

function filtrarTabla() {
    const grupo = document.getElementById('filtro-grupo').value.toLowerCase();
    const mes = document.getElementById('filtro-mes').value;
    const anio = document.getElementById('filtro-anio').value;
    const horaExacta = document.getElementById('filtro-hora').value;

    const filas = document.querySelectorAll('#tabla-alumnos tr');

    filas.forEach(fila => {
        const grupoFila = fila.dataset.grupo.toLowerCase();
        const mesFila = fila.dataset.mes;
        const anioFila = fila.dataset.anio;
        const horaFila = fila.dataset.hora;

        let mostrar = true;

        if (grupo !== 'todos' && grupo !== grupoFila) {
            mostrar = false;
        }

        if (mes && mes !== mesFila) {
            mostrar = false;
        }

        if (anio && anio !== anioFila) {
            mostrar = false;
        }

        if (horaExacta && horaExacta !== horaFila) {
            mostrar = false;
        }

        fila.style.display = mostrar ? '' : 'none';
    });
}
function mostrarMensaje(texto) {
    const div = document.createElement('div');
    div.id = 'mensaje-alerta';
    div.textContent = texto;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 4000);
}
window.addEventListener('offline', () => {
    mostrarMensaje('⚠️ Sin conexión a internet. Algunos datos no se cargarán.');
});

window.addEventListener('online', () => {
    mostrarMensaje('✅ Conexión restablecida.');
});


window.addEventListener('offline', () => {
    document.getElementById('estado-conexion').textContent = '⚠️ Sin conexión a internet.';
    document.getElementById('estado-conexion').style.display = 'block';
});

window.addEventListener('online', () => {
    document.getElementById('estado-conexion').textContent = '';
    document.getElementById('estado-conexion').style.display = 'none';
});
</script>
</body>
</html>