<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    header("Location: index.php");
    exit();
}

$labSeleccionado = $_POST['laboratorio'] ?? null;

/* ──────── obtener todos los laboratorios ──────── */
$stmt = $pdo->query("SELECT * FROM laboratorios");
$laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ──────── obtener componentes si ya se eligió laboratorio ──────── */
$reporte = [];
if ($labSeleccionado) {
    $stmt = $pdo->prepare(
        "SELECT componente, cantidad
         FROM laboratorio_componentes
         WHERE laboratorio_id = ?"
    );
    $stmt->execute([$labSeleccionado]);
    $reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hacer Reporte</title>
    <link rel="stylesheet" href="diseño.css">
</head>
<body>
    <h1>Reporte de Laboratorio</h1>

    <!-- formulario solo para seleccionar laboratorio -->
    <form method="POST" action="">
        <label for="laboratorio">Selecciona laboratorio:</label>
        <select name="laboratorio" onchange="this.form.submit()">
            <option value="">-- Selecciona --</option>
            <?php foreach ($laboratorios as $lab): ?>
                <option value="<?= $lab['id']; ?>"
                        <?= $labSeleccionado == $lab['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lab['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

<?php if (!empty($reporte)): ?>
    <!-- ──────── formulario para enviar reporte ──────── -->
    <form id="form-reporte-lab" method="POST" action="enviar_reporte.php">
        <input type="hidden" name="laboratorio" value="<?= $labSeleccionado ?>">

        <h3>Contenido del laboratorio (editable):</h3>
        <ul>
            <?php foreach ($reporte as $item):
                $nombreBonito = ucfirst(str_replace('_', ' ', $item['componente']));
                $key = htmlspecialchars($item['componente']);
            ?>
                <li>
                    <?= $nombreBonito ?>:
                    <select name="componentes[<?= $key ?>]">
                        <?php for ($i = 0; $i <= 30; $i++): ?>
                            <option value="<?= $i ?>"
                                    <?= $i == $item['cantidad'] ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </li>
            <?php endforeach; ?>
        </ul>

        <label for="observaciones">Observaciones:</label><br>
        <textarea name="observaciones" rows="5" cols="40"
                  placeholder="Describe cualquier detalle, falla o comentario..."></textarea><br><br>

        <button type="submit">Enviar Reporte</button>
        <!-- cancelar vuelve sin enviar nada -->
        <a href="perfil.php"><button type="button">Cancelar</button></a>
    </form>
<?php endif; ?>

    <br><br>
    <a href="perfil.php"><button>Volver al Perfil</button></a>

    <script>
/* Cuando la página ya cargó… */
document.addEventListener('DOMContentLoaded', () => {

    /* ──────── VALIDAR selección del laboratorio ──────── */
    const selLab = document.querySelector('select[name="laboratorio"]');
    if (selLab) {
        selLab.form.addEventListener('submit', e => {
            /* Si trataron de “submit” sin escoger laboratorio */
            if (!selLab.value) {
                e.preventDefault();
                alert('⚠️  Selecciona un laboratorio para continuar.');
            }
        });
    }

    /* ──────── CONFIRMACIÓN antes de enviar reporte ──────── */
    const form = document.getElementById('form-reporte-lab');
    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault(); // detenemos el envío clásico

            /* Este confirm() se dibuja igual al diálogo de tu captura */
            if (!confirm('¿Enviar este reporte?')) {
                return; // usuario canceló
            }

            /* Enviar datos vía fetch */
            const datos = new FormData(form);
            fetch('enviar_reporte.php', { method:'POST', body:datos })
            .then(r => r.text())
            .then(txt => {
                if (txt.trim() === 'Éxito') {
                    alert('✅ Reporte enviado correctamente.');
                    form.reset();
                    /* Regresamos al perfil tras éxito */
                    window.location.href = 'perfil.php';
                } else {
                    alert('❌ Error: ' + txt);
                }
            })
            .catch(err => {
                alert('❌ Error de conexión.');
                console.error(err);
            });
        });
    }
});
    </script>
</body>
</html>
