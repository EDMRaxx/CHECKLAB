<?php 
require 'db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    exit('<p>No autorizado.</p>');
}

$labSeleccionado = $_POST['laboratorio'] ?? null;

if (!$labSeleccionado) {
    exit('<p>Selecciona un laboratorio para ver los detalles.</p>');
}

// Obtener componentes del laboratorio
$stmt = $pdo->prepare("SELECT componente, cantidad FROM laboratorio_componentes WHERE laboratorio_id = ?");
$stmt->execute([$labSeleccionado]);
$componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($componentes)) {
    echo '<p>No hay componentes registrados para este laboratorio.</p>';
    exit();
}
?>

<!-- Diseño tipo card para el formulario -->
<form method="POST" id="form-reporte-lab" class="card-reporte">
    <input type="hidden" name="laboratorio" value="<?php echo htmlspecialchars($labSeleccionado); ?>">

    <h3>Contenido del laboratorio:</h3>

    <?php foreach ($componentes as $item): 
        $componenteKey = htmlspecialchars($item['componente']);
        $nombre = ucfirst(str_replace('_', ' ', $componenteKey));
    ?>
        <label for="<?php echo $componenteKey; ?>">
            <?php echo $nombre; ?>:
        </label>
        <select name="componentes[<?php echo $componenteKey; ?>]" id="<?php echo $componenteKey; ?>">
            <?php for ($i = 1; $i <= 80; $i++): ?>
                <option value="<?php echo $i; ?>" <?php if ($i == $item['cantidad']) echo 'selected'; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
    <?php endforeach; ?>

    <label for="observaciones" style="margin-top: 15px;"><strong>Observaciones del reporte:</strong></label>
    <textarea name="observaciones" id="observaciones" placeholder="Describe detalles, fallas o cualquier observación..."></textarea>

    <div class="acciones">
        <button type="submit">Enviar Reporte</button>
    </div>
</form>
